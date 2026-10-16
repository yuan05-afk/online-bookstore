import "server-only";
import { and, asc, count, desc, eq, gte, ilike, ne, or, sql, sum, type SQL } from "drizzle-orm";
import { getDb } from "@/db";
import { likePattern } from "@/lib/search";
import { books, cartItems, categories, orderItems, orders, users, type OrderStatus } from "@/db/schema";

export { toInt } from "@/lib/search";

export const ITEMS_PER_PAGE = 12;

export type SortKey = "title_asc" | "price_low" | "price_high" | "newest" | "oldest";
export const SORT_OPTIONS: { value: SortKey; label: string }[] = [
  { value: "title_asc", label: "Title (A-Z)" },
  { value: "price_low", label: "Price: Low to High" },
  { value: "price_high", label: "Price: High to Low" },
  { value: "newest", label: "Newest Arrivals" },
  { value: "oldest", label: "Oldest Arrivals" },
];

export function parseSort(value: string | undefined): SortKey {
  return SORT_OPTIONS.some((option) => option.value === value) ? (value as SortKey) : "title_asc";
}

export async function listCategories() {
  return getDb().select().from(categories).orderBy(asc(categories.name));
}

export async function searchBooks(input: { categoryId?: number; search?: string; sort: SortKey; page: number }) {
  const db = getDb();
  const filters: SQL[] = [];
  if (input.categoryId) filters.push(eq(books.categoryId, input.categoryId));
  const term = input.search?.trim();
  if (term) {
    const pattern = likePattern(term);
    filters.push(or(ilike(books.title, pattern), ilike(books.author, pattern), ilike(books.isbn, pattern)) as SQL);
  }
  const where = filters.length ? and(...filters) : undefined;
  const order = {
    title_asc: [asc(books.title)],
    price_low: [asc(books.priceCents), asc(books.title)],
    price_high: [desc(books.priceCents), asc(books.title)],
    newest: [desc(books.createdAt), asc(books.title)],
    oldest: [asc(books.createdAt), asc(books.title)],
  }[input.sort];

  const [{ total }] = await db.select({ total: count() }).from(books).where(where);
  const pages = Math.max(1, Math.ceil(total / ITEMS_PER_PAGE));
  const page = Math.min(Math.max(1, input.page), pages);
  const rows = await db
    .select({
      id: books.id,
      title: books.title,
      author: books.author,
      priceCents: books.priceCents,
      coverImage: books.coverImage,
      stock: books.stock,
      categoryName: categories.name,
    })
    .from(books)
    .leftJoin(categories, eq(books.categoryId, categories.id))
    .where(where)
    .orderBy(...order)
    .limit(ITEMS_PER_PAGE)
    .offset((page - 1) * ITEMS_PER_PAGE);

  return { rows, total, page, pages };
}

export async function getBook(id: number) {
  if (!Number.isInteger(id) || id <= 0) return null;
  const [row] = await getDb()
    .select({
      id: books.id,
      isbn: books.isbn,
      title: books.title,
      author: books.author,
      priceCents: books.priceCents,
      description: books.description,
      coverImage: books.coverImage,
      stock: books.stock,
      categoryId: books.categoryId,
      categoryName: categories.name,
    })
    .from(books)
    .leftJoin(categories, eq(books.categoryId, categories.id))
    .where(eq(books.id, id))
    .limit(1);
  return row ?? null;
}

export async function relatedBooks(book: { id: number; categoryId: number | null }) {
  if (!book.categoryId) return [];
  return getDb()
    .select({ id: books.id, title: books.title, author: books.author, priceCents: books.priceCents, coverImage: books.coverImage })
    .from(books)
    .where(and(eq(books.categoryId, book.categoryId), ne(books.id, book.id)))
    .orderBy(sql`random()`)
    .limit(4);
}

export async function cartLines(userId: number) {
  return getDb()
    .select({
      id: cartItems.id,
      bookId: cartItems.bookId,
      quantity: cartItems.quantity,
      title: books.title,
      author: books.author,
      priceCents: books.priceCents,
      coverImage: books.coverImage,
      stock: books.stock,
    })
    .from(cartItems)
    .innerJoin(books, eq(cartItems.bookId, books.id))
    .where(eq(cartItems.userId, userId))
    .orderBy(desc(cartItems.createdAt));
}

export async function cartCount(userId: number): Promise<number> {
  const [row] = await getDb().select({ total: sum(cartItems.quantity) }).from(cartItems).where(eq(cartItems.userId, userId));
  return Number(row?.total ?? 0);
}

export async function userOrders(userId: number) {
  return getDb().select().from(orders).where(eq(orders.userId, userId)).orderBy(desc(orders.createdAt));
}

export async function userOrder(userId: number, orderId: number) {
  if (!Number.isInteger(orderId) || orderId <= 0) return null;
  const db = getDb();
  const [order] = await db.select().from(orders).where(and(eq(orders.id, orderId), eq(orders.userId, userId))).limit(1);
  if (!order) return null;
  return { order, items: await orderLines(order.id) };
}

export async function orderLines(orderId: number) {
  return getDb()
    .select({
      id: orderItems.id,
      bookId: orderItems.bookId,
      quantity: orderItems.quantity,
      priceCents: orderItems.priceCents,
      title: books.title,
      author: books.author,
      isbn: books.isbn,
      coverImage: books.coverImage,
    })
    .from(orderItems)
    .innerJoin(books, eq(orderItems.bookId, books.id))
    .where(eq(orderItems.orderId, orderId));
}

export async function adminStats() {
  const db = getDb();
  const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
  const [[{ totalBooks }], [{ totalOrders }], [{ totalUsers }], [revenue], [week], [weekUsers]] = await Promise.all([
    db.select({ totalBooks: count() }).from(books),
    db.select({ totalOrders: count() }).from(orders),
    db.select({ totalUsers: count() }).from(users).where(eq(users.role, "user")),
    db.select({ cents: sum(orders.totalCents) }).from(orders).where(ne(orders.status, "Cancelled")),
    db
      .select({ orders: count(), cents: sum(orders.totalCents) })
      .from(orders)
      .where(and(gte(orders.createdAt, weekAgo), ne(orders.status, "Cancelled"))),
    db.select({ total: count() }).from(users).where(and(eq(users.role, "user"), gte(users.createdAt, weekAgo))),
  ]);
  return {
    totalBooks,
    totalOrders,
    totalUsers,
    revenueCents: Number(revenue?.cents ?? 0),
    weekOrders: week?.orders ?? 0,
    weekRevenueCents: Number(week?.cents ?? 0),
    weekUsers: weekUsers?.total ?? 0,
  };
}

export async function recentOrders(limit = 10) {
  return getDb().select().from(orders).orderBy(desc(orders.createdAt)).limit(limit);
}

export async function lowStockBooks(limit = 10, threshold = 10) {
  return getDb()
    .select({ id: books.id, title: books.title, author: books.author, stock: books.stock })
    .from(books)
    .where(sql`${books.stock} < ${threshold}`)
    .orderBy(asc(books.stock), asc(books.title))
    .limit(limit);
}

export async function adminBooks(input: { search?: string; categoryId?: number }) {
  const filters: SQL[] = [];
  const term = input.search?.trim();
  if (term) {
    const pattern = likePattern(term);
    filters.push(or(ilike(books.title, pattern), ilike(books.author, pattern), ilike(books.isbn, pattern)) as SQL);
  }
  if (input.categoryId) filters.push(eq(books.categoryId, input.categoryId));
  return getDb()
    .select({
      id: books.id,
      isbn: books.isbn,
      title: books.title,
      author: books.author,
      priceCents: books.priceCents,
      stock: books.stock,
      coverImage: books.coverImage,
      categoryName: categories.name,
    })
    .from(books)
    .leftJoin(categories, eq(books.categoryId, categories.id))
    .where(filters.length ? and(...filters) : undefined)
    .orderBy(asc(books.title));
}

export async function adminOrders(input: { status?: OrderStatus; search?: string }) {
  const filters: SQL[] = [];
  if (input.status) filters.push(eq(orders.status, input.status));
  const term = input.search?.trim();
  if (term) {
    const pattern = likePattern(term);
    filters.push(or(ilike(orders.orderNumber, pattern), ilike(orders.customerEmail, pattern), ilike(orders.customerName, pattern)) as SQL);
  }
  return getDb()
    .select()
    .from(orders)
    .where(filters.length ? and(...filters) : undefined)
    .orderBy(desc(orders.createdAt));
}

export async function adminOrder(orderId: number) {
  if (!Number.isInteger(orderId) || orderId <= 0) return null;
  const [order] = await getDb().select().from(orders).where(eq(orders.id, orderId)).limit(1);
  if (!order) return null;
  return { order, items: await orderLines(order.id) };
}

export async function adminAccounts(input: { search?: string; role?: "user" | "admin" }) {
  const filters: SQL[] = [];
  const term = input.search?.trim();
  if (term) {
    const pattern = likePattern(term);
    filters.push(or(ilike(users.email, pattern), ilike(users.firstName, pattern), ilike(users.lastName, pattern)) as SQL);
  }
  if (input.role) filters.push(eq(users.role, input.role));
  return getDb()
    .select()
    .from(users)
    .where(filters.length ? and(...filters) : undefined)
    .orderBy(desc(users.createdAt));
}

export async function accountSummary(userId: number) {
  const db = getDb();
  const [account] = await db.select().from(users).where(eq(users.id, userId)).limit(1);
  if (!account) return null;
  const [stats] = await db
    .select({ orderCount: count(), spentCents: sum(orders.totalCents) })
    .from(orders)
    .where(and(eq(orders.userId, userId), ne(orders.status, "Cancelled")));
  const recent = await db.select().from(orders).where(eq(orders.userId, userId)).orderBy(desc(orders.createdAt)).limit(10);
  return { account, orderCount: stats?.orderCount ?? 0, spentCents: Number(stats?.spentCents ?? 0), recent };
}

