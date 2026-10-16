import { relations } from "drizzle-orm";
import {
  index,
  integer,
  pgEnum,
  pgTable,
  text,
  timestamp,
  unique,
  varchar,
} from "drizzle-orm/pg-core";

/**
 * Money is stored as whole cents in `integer` columns, so totals and tax never pick up
 * floating point error. `lib/money.ts` is the only place that turns cents into dollars.
 */

export const USER_ROLES = ["user", "admin"] as const;
export type UserRole = (typeof USER_ROLES)[number];
export const roleEnum = pgEnum("user_role", USER_ROLES);

export const ORDER_STATUSES = ["Processing", "Shipped", "Delivered", "Cancelled"] as const;
export type OrderStatus = (typeof ORDER_STATUSES)[number];
export const orderStatusEnum = pgEnum("order_status", ORDER_STATUSES);

export const users = pgTable(
  "users",
  {
    id: integer("id").primaryKey().generatedByDefaultAsIdentity(),
    email: varchar("email", { length: 255 }).notNull().unique(),
    passwordHash: varchar("password_hash", { length: 255 }).notNull(),
    firstName: varchar("first_name", { length: 100 }).notNull(),
    lastName: varchar("last_name", { length: 100 }).notNull(),
    role: roleEnum("role").notNull().default("user"),
    phone: varchar("phone", { length: 30 }),
    address: text("address"),
    city: varchar("city", { length: 100 }),
    state: varchar("state", { length: 100 }),
    zipCode: varchar("zip_code", { length: 20 }),
    country: varchar("country", { length: 100 }).default("USA"),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => [index("users_role_idx").on(table.role)],
);

export const categories = pgTable("categories", {
  id: integer("id").primaryKey().generatedByDefaultAsIdentity(),
  name: varchar("name", { length: 100 }).notNull().unique(),
  slug: varchar("slug", { length: 100 }).notNull().unique(),
  description: text("description"),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
});

export const books = pgTable(
  "books",
  {
    id: integer("id").primaryKey().generatedByDefaultAsIdentity(),
    isbn: varchar("isbn", { length: 20 }).notNull().unique(),
    title: varchar("title", { length: 255 }).notNull(),
    author: varchar("author", { length: 255 }).notNull(),
    priceCents: integer("price_cents").notNull(),
    description: text("description"),
    coverImage: varchar("cover_image", { length: 500 }),
    categoryId: integer("category_id").references(() => categories.id, { onDelete: "set null" }),
    stock: integer("stock_quantity").notNull().default(0),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => [
    index("books_category_idx").on(table.categoryId),
    index("books_title_idx").on(table.title),
    index("books_created_idx").on(table.createdAt),
  ],
);

export const cartItems = pgTable(
  "cart_items",
  {
    id: integer("id").primaryKey().generatedByDefaultAsIdentity(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    bookId: integer("book_id")
      .notNull()
      .references(() => books.id, { onDelete: "cascade" }),
    quantity: integer("quantity").notNull().default(1),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => [unique("cart_items_user_book_unique").on(table.userId, table.bookId)],
);

export const orders = pgTable(
  "orders",
  {
    id: integer("id").primaryKey().generatedByDefaultAsIdentity(),
    /** Kept when an admin deletes the account, so the order history survives. */
    userId: integer("user_id").references(() => users.id, { onDelete: "set null" }),
    customerName: varchar("customer_name", { length: 200 }).notNull(),
    customerEmail: varchar("customer_email", { length: 255 }).notNull(),
    orderNumber: varchar("order_number", { length: 50 }).notNull().unique(),
    subtotalCents: integer("subtotal_cents").notNull(),
    taxCents: integer("tax_cents").notNull().default(0),
    totalCents: integer("total_cents").notNull(),
    status: orderStatusEnum("status").notNull().default("Processing"),
    shippingAddress: text("shipping_address").notNull(),
    shippingCity: varchar("shipping_city", { length: 100 }),
    shippingState: varchar("shipping_state", { length: 100 }),
    shippingZip: varchar("shipping_zip", { length: 20 }),
    shippingCountry: varchar("shipping_country", { length: 100 }),
    shippingPhone: varchar("shipping_phone", { length: 30 }),
    paymentMethod: varchar("payment_method", { length: 60 }).notNull(),
    transactionId: varchar("transaction_id", { length: 100 }),
    notes: text("notes"),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => [
    index("orders_user_idx").on(table.userId),
    index("orders_status_idx").on(table.status),
    index("orders_created_idx").on(table.createdAt),
  ],
);

export const orderItems = pgTable(
  "order_items",
  {
    id: integer("id").primaryKey().generatedByDefaultAsIdentity(),
    orderId: integer("order_id")
      .notNull()
      .references(() => orders.id, { onDelete: "cascade" }),
    bookId: integer("book_id")
      .notNull()
      .references(() => books.id, { onDelete: "restrict" }),
    quantity: integer("quantity").notNull(),
    priceCents: integer("price_at_purchase_cents").notNull(),
  },
  (table) => [index("order_items_order_idx").on(table.orderId)],
);

export const usersRelations = relations(users, ({ many }) => ({
  orders: many(orders),
  cartItems: many(cartItems),
}));

export const categoriesRelations = relations(categories, ({ many }) => ({ books: many(books) }));

export const booksRelations = relations(books, ({ one }) => ({
  category: one(categories, { fields: [books.categoryId], references: [categories.id] }),
}));

export const cartItemsRelations = relations(cartItems, ({ one }) => ({
  book: one(books, { fields: [cartItems.bookId], references: [books.id] }),
  user: one(users, { fields: [cartItems.userId], references: [users.id] }),
}));

export const ordersRelations = relations(orders, ({ one, many }) => ({
  user: one(users, { fields: [orders.userId], references: [users.id] }),
  items: many(orderItems),
}));

export const orderItemsRelations = relations(orderItems, ({ one }) => ({
  order: one(orders, { fields: [orderItems.orderId], references: [orders.id] }),
  book: one(books, { fields: [orderItems.bookId], references: [books.id] }),
}));

export type User = typeof users.$inferSelect;
export type Book = typeof books.$inferSelect;
export type Category = typeof categories.$inferSelect;
export type Order = typeof orders.$inferSelect;
export type OrderItem = typeof orderItems.$inferSelect;
