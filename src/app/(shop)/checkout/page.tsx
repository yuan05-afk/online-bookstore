import type { Metadata } from "next";
import { redirect } from "next/navigation";
import { eq } from "drizzle-orm";
import { getDb } from "@/db";
import { users } from "@/db/schema";
import { requireCustomer } from "@/lib/auth";
import { TAX_RATE, totals } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { cartLines } from "@/server/queries";
import { CheckoutForm } from "./checkout-form";

export const metadata: Metadata = { title: "Checkout" };

export default async function CheckoutPage() {
  const user = await requireCustomer("/checkout");
  const lines = await cartLines(user.id);
  if (lines.length === 0) redirect(withNotice("/cart", "cart_empty"));
  const [profile] = await getDb().select().from(users).where(eq(users.id, user.id)).limit(1);
  const sums = totals(lines);
  const year = new Date().getFullYear();

  return (
    <>
      <h1 className="user-page-title">Checkout</h1>
      <CheckoutForm
        defaults={{
          firstName: profile.firstName,
          lastName: profile.lastName,
          address: profile.address ?? "",
          city: profile.city ?? "",
          state: profile.state ?? "",
          zipCode: profile.zipCode ?? "",
          country: profile.country ?? "USA",
          phone: profile.phone ?? "",
        }}
        lines={lines.map((line) => ({ id: line.id, title: line.title, quantity: line.quantity, priceCents: line.priceCents }))}
        subtotalCents={sums.subtotalCents}
        taxCents={sums.taxCents}
        totalCents={sums.totalCents}
        taxLabel={`${(TAX_RATE * 100).toFixed(1).replace(/\.0$/, "")}%`}
        years={Array.from({ length: 10 }, (_, index) => year + index)}
      />
    </>
  );
}
