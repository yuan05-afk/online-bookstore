"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { and, count, eq, ne, sql } from "drizzle-orm";
import { getDb } from "@/db";
import { books, categories, orderItems, orders, ORDER_STATUSES, users } from "@/db/schema";
import { requireAdmin } from "@/lib/auth";
import type { FormState } from "@/lib/form-state";
import { parseDollarsToCents } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { bookSchema, fieldErrors, formText, isUniqueViolation } from "@/lib/validation";

function bookValues(data: FormData) {
  return {
    isbn: formText(data, "isbn"),
    title: formText(data, "title"),
    author: formText(data, "author"),
    price: formText(data, "price"),
    stock: formText(data, "stock"),
    categoryId: formText(data, "categoryId"),
    description: formText(data, "description"),
    coverImage: formText(data, "coverImage"),
  };
}

async function parseBook(data: FormData): Promise<{ errors: string[]; values: Record<string, string> } | { book: ReturnType<typeof bookSchema.parse> & { priceCents: number } }> {
  const values = bookValues(data);
  const parsed = bookSchema.safeParse(values);
  const errors: string[] = parsed.success ? [] : Object.values(fieldErrors(parsed.error));
  const priceCents = parseDollarsToCents(values.price);
  if (priceCents === null && !errors.some((message) => message.startsWith("Price"))) errors.push("Enter a price greater than $0 with at most two decimals.");
  if (!parsed.success || priceCents === null) return { errors, values };

  const [category] = await getDb().select({ id: categories.id }).from(categories).where(eq(categories.id, parsed.data.categoryId)).limit(1);
  if (!category) return { errors: ["Choose a category from the list."], values };
  return { book: { ...parsed.data, priceCents } };
}

export async function createBookAction(_previous: FormState, data: FormData): Promise<FormState> {
  await requireAdmin();
  const result = await parseBook(data);
  if ("errors" in result) return result;
  const { book } = result;

  try {
    await getDb().insert(books).values({
      isbn: book.isbn,
      title: book.title,
      author: book.author,
      priceCents: book.priceCents,
      stock: book.stock,
      categoryId: book.categoryId,
      description: book.description || null,
      coverImage: book.coverImage || null,
    });
  } catch (error) {
    if (isUniqueViolation(error)) return { errors: ["A book with this ISBN already exists."], values: bookValues(data) };
    throw error;
  }
  revalidatePath("/", "layout");
  redirect(withNotice("/admin/books", "book_added"));
}

export async function updateBookAction(id: number, _previous: FormState, data: FormData): Promise<FormState> {
  await requireAdmin();
  const result = await parseBook(data);
  if ("errors" in result) return result;
  const { book } = result;

  // The ISBN identifies the book and is not editable.
  const updated = await getDb()
    .update(books)
    .set({
      title: book.title,
      author: book.author,
      priceCents: book.priceCents,
      stock: book.stock,
      categoryId: book.categoryId,
      description: book.description || null,
      coverImage: book.coverImage || null,
      updatedAt: new Date(),
    })
    .where(eq(books.id, id))
    .returning({ id: books.id });
  if (updated.length === 0) redirect(withNotice("/admin/books", "book_not_found"));
  revalidatePath("/", "layout");
  redirect(withNotice("/admin/books", "book_updated"));
}

export async function deleteBookAction(id: number) {
  await requireAdmin();
  const db = getDb();
  const [{ used }] = await db.select({ used: count() }).from(orderItems).where(eq(orderItems.bookId, id));
  if (used > 0) redirect(withNotice("/admin/books", "book_in_orders"));
  await db.delete(books).where(eq(books.id, id));
  revalidatePath("/", "layout");
  redirect(withNotice("/admin/books", "book_deleted"));
}

export async function updateOrderAction(id: number, data: FormData) {
  await requireAdmin();
  const status = formText(data, "status");
  const notes = formText(data, "notes").trim().slice(0, 1000);
  if (!(ORDER_STATUSES as readonly string[]).includes(status)) redirect(withNotice(`/admin/orders/${id}`, "order_not_found"));

  const db = getDb();
  await db.transaction(async (tx) => {
    const [current] = await tx.select({ status: orders.status }).from(orders).where(eq(orders.id, id)).for("update").limit(1);
    if (!current) return;
    await tx
      .update(orders)
      .set({ status: status as (typeof ORDER_STATUSES)[number], notes: notes || null, updatedAt: new Date() })
      .where(eq(orders.id, id));

    // Cancelling gives the copies back to the shelf; reopening a cancelled order takes them again.
    const wasCancelled = current.status === "Cancelled";
    const nowCancelled = status === "Cancelled";
    if (wasCancelled !== nowCancelled) {
      const lines = await tx.select({ bookId: orderItems.bookId, quantity: orderItems.quantity }).from(orderItems).where(eq(orderItems.orderId, id));
      for (const line of lines) {
        const delta = nowCancelled ? line.quantity : -line.quantity;
        await tx
          .update(books)
          .set({ stock: sql`GREATEST(0, ${books.stock} + ${delta})` })
          .where(eq(books.id, line.bookId));
      }
    }
  });
  revalidatePath("/", "layout");
  redirect(withNotice(`/admin/orders/${id}`, "order_updated"));
}

export async function promoteAccountAction(id: number) {
  const admin = await requireAdmin();
  if (id === admin.id) redirect(withNotice(`/admin/accounts/${id}`, "account_self"));
  await getDb().update(users).set({ role: "admin", updatedAt: new Date() }).where(eq(users.id, id));
  revalidatePath("/admin/accounts");
  redirect(withNotice(`/admin/accounts/${id}`, "account_promoted"));
}

export async function deleteAccountAction(id: number) {
  const admin = await requireAdmin();
  if (id === admin.id) redirect(withNotice(`/admin/accounts/${id}`, "account_self"));
  const db = getDb();
  // Orders stay for the shop's records; they keep the customer's name and email.
  await db.delete(users).where(and(eq(users.id, id), ne(users.id, admin.id)));
  revalidatePath("/admin/accounts");
  redirect(withNotice("/admin/accounts", "account_deleted"));
}
