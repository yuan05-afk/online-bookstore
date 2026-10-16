import { randomBytes } from "node:crypto";
import { config as loadEnv } from "dotenv";
import bcrypt from "bcryptjs";
import { eq, sql } from "drizzle-orm";
import { drizzle } from "drizzle-orm/node-postgres";
import { Pool } from "pg";
import * as schema from "./schema";
import seed from "./seed-data.json";

loadEnv({ path: ".env.local" });
loadEnv({ path: ".env" });

/**
 * Safe to run more than once: categories and books are matched by slug and ISBN, the two
 * accounts are created only if missing, and sample orders are written only into an empty shop.
 *
 *  - Customer demo account: user@bookstore.com / user123 (shown on the sign-in page).
 *  - Admin account: admin@bookstore.com. Its password is SEED_ADMIN_PASSWORD, or a random one
 *    printed once below. It is not published anywhere, so the public demo cannot be edited.
 */

const url = process.env.DATABASE_URL_UNPOOLED ?? process.env.DATABASE_URL;
if (!url) {
  console.error("Set DATABASE_URL_UNPOOLED (or DATABASE_URL) first.");
  process.exit(1);
}

const pool = new Pool({ connectionString: url });
const db = drizzle(pool, { schema });

const DEMO_USER_PASSWORD = "user123";

async function main() {
  const { categories, books, users, orders, orderItems } = schema;

  for (const category of seed.categories) {
    await db.insert(categories).values(category).onConflictDoNothing({ target: categories.slug });
  }
  // Keep the id counter ahead of the explicit ids above.
  await db.execute(sql`SELECT setval(pg_get_serial_sequence('categories', 'id'), (SELECT COALESCE(MAX(id), 1) FROM categories), true)`);
  const categoryBySourceId = new Map<number, number>();
  for (const category of seed.categories) {
    const [row] = await db.select({ id: categories.id }).from(categories).where(eq(categories.slug, category.slug)).limit(1);
    categoryBySourceId.set(category.id, row.id);
  }

  let addedBooks = 0;
  for (const book of seed.books) {
    const inserted = await db
      .insert(books)
      .values({
        isbn: book.isbn,
        title: book.title,
        author: book.author,
        priceCents: book.priceCents,
        description: book.description,
        coverImage: book.coverImage,
        categoryId: categoryBySourceId.get(book.categoryId) ?? null,
        stock: book.stock,
      })
      .onConflictDoNothing({ target: books.isbn })
      .returning({ id: books.id });
    addedBooks += inserted.length;
  }

  const adminPassword = process.env.SEED_ADMIN_PASSWORD?.trim() || randomBytes(9).toString("base64url");
  const accounts = [
    { email: "admin@bookstore.com", firstName: "Admin", lastName: "User", role: "admin" as const, password: adminPassword, phone: "555-0100", address: "123 Admin St", city: "New York", state: "NY", zipCode: "10001" },
    { email: "user@bookstore.com", firstName: "Demo", lastName: "Customer", role: "user" as const, password: DEMO_USER_PASSWORD, phone: "555-0200", address: "456 Reader Ave", city: "Los Angeles", state: "CA", zipCode: "90001" },
  ];
  let printedAdmin = false;
  for (const { password, ...account } of accounts) {
    const [existing] = await db.select({ id: users.id }).from(users).where(eq(users.email, account.email)).limit(1);
    if (existing) continue;
    await db.insert(users).values({ ...account, country: "USA", passwordHash: await bcrypt.hash(password, 12) });
    if (account.role === "admin" && !process.env.SEED_ADMIN_PASSWORD?.trim()) {
      console.log(`Admin account created: ${account.email} / ${password}   (shown once, save it)`);
      printedAdmin = true;
    }
  }

  // Sample orders so the admin dashboard and the order pages have something to show.
  const [{ existingOrders }] = await db.select({ existingOrders: sql<number>`count(*)::int` }).from(orders);
  let sampleOrders = 0;
  if (existingOrders === 0) {
    const [customer] = await db.select().from(users).where(eq(users.email, "user@bookstore.com")).limit(1);
    const catalog = await db.select({ id: books.id, priceCents: books.priceCents }).from(books).orderBy(books.id).limit(12);
    const plans: { daysAgo: number; status: (typeof schema.ORDER_STATUSES)[number]; picks: [number, number][] }[] = [
      { daysAgo: 21, status: "Delivered", picks: [[0, 1], [3, 2]] },
      { daysAgo: 14, status: "Delivered", picks: [[5, 1]] },
      { daysAgo: 9, status: "Shipped", picks: [[2, 1], [7, 1], [9, 1]] },
      { daysAgo: 3, status: "Processing", picks: [[1, 2]] },
      { daysAgo: 1, status: "Cancelled", picks: [[4, 1]] },
    ];
    for (const [index, plan] of plans.entries()) {
      const lines = plan.picks.map(([pick, quantity]) => ({ book: catalog[pick % catalog.length], quantity }));
      const subtotal = lines.reduce((sum, line) => sum + line.book.priceCents * line.quantity, 0);
      const tax = Math.round(subtotal * 0.085);
      const created = new Date(Date.now() - plan.daysAgo * 24 * 60 * 60 * 1000);
      const [order] = await db
        .insert(orders)
        .values({
          userId: customer.id,
          customerName: `${customer.firstName} ${customer.lastName}`,
          customerEmail: customer.email,
          orderNumber: `ORD-DEMO${String(index + 1).padStart(3, "0")}`,
          subtotalCents: subtotal,
          taxCents: tax,
          totalCents: subtotal + tax,
          status: plan.status,
          shippingAddress: "456 Reader Ave, Los Angeles, CA, 90001, USA",
          shippingCity: "Los Angeles",
          shippingState: "CA",
          shippingZip: "90001",
          shippingCountry: "USA",
          shippingPhone: "555-0200",
          paymentMethod: "Visa ending in 0366",
          transactionId: `TXN-DEMO-${10000 + index}`,
          createdAt: created,
          updatedAt: created,
        })
        .returning({ id: orders.id });
      await db.insert(orderItems).values(lines.map((line) => ({ orderId: order.id, bookId: line.book.id, quantity: line.quantity, priceCents: line.book.priceCents })));
      sampleOrders += 1;
    }
  }

  console.log(`Seed done: ${seed.categories.length} categories, ${addedBooks} new books, ${sampleOrders} sample orders.${printedAdmin ? "" : " Admin and demo accounts are in place."}`);
}

main()
  .catch((error) => {
    console.error(error);
    process.exitCode = 1;
  })
  .finally(() => pool.end());
