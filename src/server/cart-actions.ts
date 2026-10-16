"use server";

import { revalidatePath } from "next/cache";
import { and, eq } from "drizzle-orm";
import { getDb } from "@/db";
import { books, cartItems } from "@/db/schema";
import { requireCustomer } from "@/lib/auth";
import { cartCount } from "@/server/queries";

export type CartResult = { ok: true; message: string; count: number } | { ok: false; message: string };

const MAX_PER_BOOK = 20;

function refresh() {
  // The cart badge lives in the layout, so refresh from the root.
  revalidatePath("/", "layout");
}

function whole(value: unknown): number {
  const number = typeof value === "number" ? value : Number(value);
  return Number.isInteger(number) ? number : NaN;
}

export async function addToCartAction(bookId: number, quantity: number): Promise<CartResult> {
  const user = await requireCustomer();
  const id = whole(bookId);
  const wanted = whole(quantity);
  if (!(id > 0) || !(wanted > 0)) return { ok: false, message: "Choose a book and a quantity." };

  const db = getDb();
  const result = await db.transaction(async (tx) => {
    const [book] = await tx.select({ id: books.id, title: books.title, stock: books.stock }).from(books).where(eq(books.id, id)).for("update").limit(1);
    if (!book) return { ok: false as const, message: "That book is no longer available." };
    if (book.stock <= 0) return { ok: false as const, message: `${book.title} is out of stock.` };

    const [existing] = await tx
      .select({ id: cartItems.id, quantity: cartItems.quantity })
      .from(cartItems)
      .where(and(eq(cartItems.userId, user.id), eq(cartItems.bookId, id)))
      .limit(1);

    const total = (existing?.quantity ?? 0) + wanted;
    if (total > book.stock) {
      const room = Math.max(0, book.stock - (existing?.quantity ?? 0));
      return {
        ok: false as const,
        message: room > 0 ? `Only ${book.stock} in stock. You can add ${room} more.` : `You already have all ${book.stock} in your cart.`,
      };
    }
    if (total > MAX_PER_BOOK) return { ok: false as const, message: `You can order up to ${MAX_PER_BOOK} copies of one book at a time.` };

    if (existing) {
      await tx.update(cartItems).set({ quantity: total }).where(eq(cartItems.id, existing.id));
    } else {
      await tx.insert(cartItems).values({ userId: user.id, bookId: id, quantity: wanted });
    }
    return { ok: true as const, title: book.title };
  });

  if (!result.ok) return result;
  refresh();
  return { ok: true, message: "Added to cart.", count: await cartCount(user.id) };
}

export async function setCartQuantityAction(cartItemId: number, quantity: number): Promise<CartResult> {
  const user = await requireCustomer();
  const id = whole(cartItemId);
  const next = whole(quantity);
  if (!(id > 0) || Number.isNaN(next)) return { ok: false, message: "That cart item could not be found." };

  const db = getDb();
  if (next <= 0) {
    await db.delete(cartItems).where(and(eq(cartItems.id, id), eq(cartItems.userId, user.id)));
  } else {
    const [line] = await db
      .select({ stock: books.stock, title: books.title })
      .from(cartItems)
      .innerJoin(books, eq(cartItems.bookId, books.id))
      .where(and(eq(cartItems.id, id), eq(cartItems.userId, user.id)))
      .limit(1);
    if (!line) return { ok: false, message: "That cart item could not be found." };
    if (next > line.stock) return { ok: false, message: `Only ${line.stock} of ${line.title} in stock.` };
    if (next > MAX_PER_BOOK) return { ok: false, message: `You can order up to ${MAX_PER_BOOK} copies of one book at a time.` };
    await db.update(cartItems).set({ quantity: next }).where(and(eq(cartItems.id, id), eq(cartItems.userId, user.id)));
  }
  refresh();
  return { ok: true, message: "Cart updated.", count: await cartCount(user.id) };
}

export async function removeCartItemAction(cartItemId: number): Promise<CartResult> {
  return setCartQuantityAction(cartItemId, 0);
}
