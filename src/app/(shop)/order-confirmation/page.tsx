import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { and, eq } from "drizzle-orm";
import { getDb } from "@/db";
import { orders } from "@/db/schema";
import { Icon } from "@/components/icon";
import { requireCustomer } from "@/lib/auth";
import { addDays, formatDate } from "@/lib/format";
import { formatCents } from "@/lib/money";

export const metadata: Metadata = { title: "Order Confirmation" };

export default async function OrderConfirmationPage({ searchParams }: { searchParams: Promise<{ order?: string }> }) {
  const user = await requireCustomer("/orders");
  const { order: number } = await searchParams;
  if (!number) redirect("/orders");

  // Only the buyer can open their confirmation.
  const [order] = await getDb()
    .select()
    .from(orders)
    .where(and(eq(orders.orderNumber, number), eq(orders.userId, user.id)))
    .limit(1);
  if (!order) redirect("/orders");

  return (
    <div className="user-confirmation-container">
      <div className="user-confirmation-content">
        <div className="user-confirmation-icon">
          <Icon name="check-circle-bold" width={64} style={{ color: "var(--user-emerald-500)" }} />
        </div>
        <h1 className="user-confirmation-title">Order Placed Successfully!</h1>
        <p className="user-confirmation-message">Thank you for your order. We&apos;ve received your payment and will process your order shortly.</p>

        <div className="user-confirmation-details">
          <div className="user-confirmation-row">
            <span>Order Number:</span>
            <strong>{order.orderNumber}</strong>
          </div>
          <div className="user-confirmation-row">
            <span>Transaction ID:</span>
            <strong>{order.transactionId}</strong>
          </div>
          <div className="user-confirmation-row">
            <span>Total Amount:</span>
            <strong style={{ fontSize: "1.125rem", color: "var(--user-zinc-900)" }}>{formatCents(order.totalCents)}</strong>
          </div>
          <div className="user-confirmation-row">
            <span>Estimated Delivery:</span>
            <strong>{formatDate(addDays(order.createdAt, 5))}</strong>
          </div>
        </div>

        <p className="user-confirmation-note">
          <Icon name="info-circle-linear" width={16} />
          A confirmation email is sent when email delivery is configured. You can always track your order in your order history.
        </p>

        <div className="user-confirmation-actions">
          <Link href={`/orders/${order.id}`} className="user-btn user-btn-primary">
            <Icon name="box-linear" width={20} />
            View This Order
          </Link>
          <Link href="/catalog" className="user-btn user-btn-secondary">
            Continue Shopping
          </Link>
        </div>
      </div>
    </div>
  );
}
