"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { after } from "next/server";
import { and, eq, gte, inArray, sql } from "drizzle-orm";
import { getDb } from "@/db";
import { books, cartItems, orderItems, orders, users } from "@/db/schema";
import { requireCustomer } from "@/lib/auth";
import { sendOrderConfirmation, type OrderEmail } from "@/lib/email";
import type { FormState } from "@/lib/form-state";
import { totals } from "@/lib/money";
import { orderNumber, processPayment } from "@/lib/payment";
import { fieldErrors, formText, paymentSchema, shippingSchema } from "@/lib/validation";

type CheckoutFormState = FormState;

class StockError extends Error {}

export async function placeOrderAction(_previous: CheckoutFormState, data: FormData): Promise<CheckoutFormState> {
  const user = await requireCustomer("/checkout");

  const rawShipping = {
    firstName: formText(data, "firstName"),
    lastName: formText(data, "lastName"),
    address: formText(data, "address"),
    city: formText(data, "city"),
    state: formText(data, "state"),
    zipCode: formText(data, "zipCode"),
    country: formText(data, "country"),
    phone: formText(data, "phone"),
  };
  // The card number and CVV are never sent back to the page.
  const values = { ...rawShipping, cardNumber: "", cvv: "", expiryMonth: formText(data, "expiryMonth"), expiryYear: formText(data, "expiryYear") };

  const shipping = shippingSchema.safeParse(rawShipping);
  const payment = paymentSchema.safeParse({
    cardNumber: formText(data, "cardNumber"),
    expiryMonth: values.expiryMonth,
    expiryYear: values.expiryYear,
    cvv: formText(data, "cvv"),
  });
  const errors = [
    ...(shipping.success ? [] : Object.values(fieldErrors(shipping.error))),
    ...(payment.success ? [] : Object.values(fieldErrors(payment.error))),
  ];
  if (!shipping.success || !payment.success) return { errors, values };

  const db = getDb();
  let placed: { orderNumber: string; mail: OrderEmail };
  try {
    placed = await db.transaction(async (tx) => {
      const lines = await tx
        .select({ id: cartItems.id, bookId: cartItems.bookId, quantity: cartItems.quantity, title: books.title, priceCents: books.priceCents })
        .from(cartItems)
        .innerJoin(books, eq(cartItems.bookId, books.id))
        .where(eq(cartItems.userId, user.id));
      if (lines.length === 0) throw new StockError("Your cart is empty.");

      // Lock the books being bought, so two checkouts cannot both take the last copy.
      const stockRows = await tx
        .select({ id: books.id, stock: books.stock, title: books.title })
        .from(books)
        .where(inArray(books.id, lines.map((line) => line.bookId)))
        .for("update");
      const stock = new Map(stockRows.map((row) => [row.id, row]));
      for (const line of lines) {
        const have = stock.get(line.bookId)?.stock ?? 0;
        if (have < line.quantity) {
          throw new StockError(have > 0 ? `Only ${have} of "${line.title}" left in stock. Update your cart and try again.` : `"${line.title}" is out of stock. Remove it from your cart and try again.`);
        }
      }

      const sums = totals(lines);
      const paid = processPayment({ ...payment.data });
      if (!paid.ok) throw new StockError(paid.message);

      const address = [shipping.data.address, shipping.data.city, shipping.data.state, shipping.data.zipCode, shipping.data.country].join(", ");
      const number = orderNumber();
      const [order] = await tx
        .insert(orders)
        .values({
          userId: user.id,
          customerName: `${shipping.data.firstName} ${shipping.data.lastName}`,
          customerEmail: user.email,
          orderNumber: number,
          subtotalCents: sums.subtotalCents,
          taxCents: sums.taxCents,
          totalCents: sums.totalCents,
          status: "Processing",
          shippingAddress: address,
          shippingCity: shipping.data.city,
          shippingState: shipping.data.state,
          shippingZip: shipping.data.zipCode,
          shippingCountry: shipping.data.country,
          shippingPhone: shipping.data.phone,
          paymentMethod: paid.method,
          transactionId: paid.transactionId,
        })
        .returning({ id: orders.id });

      await tx.insert(orderItems).values(lines.map((line) => ({ orderId: order.id, bookId: line.bookId, quantity: line.quantity, priceCents: line.priceCents })));

      for (const line of lines) {
        const updated = await tx
          .update(books)
          .set({ stock: sql`${books.stock} - ${line.quantity}`, updatedAt: new Date() })
          .where(and(eq(books.id, line.bookId), gte(books.stock, line.quantity)))
          .returning({ id: books.id });
        if (updated.length === 0) throw new StockError(`"${line.title}" just sold out. Update your cart and try again.`);
      }

      await tx.delete(cartItems).where(eq(cartItems.userId, user.id));

      // Remember the shipping details for next time.
      await tx
        .update(users)
        .set({
          address: shipping.data.address,
          city: shipping.data.city,
          state: shipping.data.state,
          zipCode: shipping.data.zipCode,
          country: shipping.data.country,
          phone: shipping.data.phone,
          updatedAt: new Date(),
        })
        .where(eq(users.id, user.id));

      const mail = {
        to: user.email,
        customerName: shipping.data.firstName,
        orderNumber: number,
        lines: lines.map((line) => ({ title: line.title, quantity: line.quantity, priceCents: line.priceCents })),
        subtotalCents: sums.subtotalCents,
        taxCents: sums.taxCents,
        totalCents: sums.totalCents,
        shippingAddress: address,
      };

      return { orderNumber: number, mail };
    });
  } catch (error) {
    if (error instanceof StockError) return { errors: [error.message], values };
    console.error("checkout failed", error);
    return { errors: ["Something went wrong while placing your order. You were not charged. Please try again."], values };
  }

  // Sent after the order is committed, so a slow or failing email never affects the order.
  after(() =>
    sendOrderConfirmation(placed.mail).then((result) => {
      if (!result.sent) console.info(`Order ${placed.orderNumber}: confirmation email not sent (${result.reason}).`);
    }),
  );
  revalidatePath("/", "layout");
  redirect(`/order-confirmation?order=${encodeURIComponent(placed.orderNumber)}`);
}
