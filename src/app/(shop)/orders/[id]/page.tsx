import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { BookCover } from "@/components/book-cover";
import { Icon } from "@/components/icon";
import { requireCustomer } from "@/lib/auth";
import { formatDateTime } from "@/lib/format";
import { formatCents } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { userOrder } from "@/server/queries";

export const metadata: Metadata = { title: "Order Details" };

export default async function OrderDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const user = await requireCustomer(`/orders/${id}`);
  const found = await userOrder(user.id, Number(id));
  if (!found) redirect(withNotice("/orders", "order_not_found"));
  const { order, items } = found;

  const cancelled = order.status === "Cancelled";
  const shipped = order.status === "Shipped" || order.status === "Delivered";
  const delivered = order.status === "Delivered";

  return (
    <>
      <Link href="/orders" className="user-back-link">
        <Icon name="alt-arrow-left-linear" width={16} />
        Back to Orders
      </Link>

      <div className="user-detail-header">
        <h1>Order #{order.orderNumber}</h1>
        <span className={`user-order-status ${order.status.toLowerCase()}`}>{order.status}</span>
      </div>

      {cancelled ? (
        <p style={{ color: "var(--user-red-600)", marginBottom: "1.5rem" }}>This order was cancelled.{order.notes ? ` Note from the store: ${order.notes}` : ""}</p>
      ) : (
        <div className="user-order-progress">
          <div className="user-progress-step completed">
            <div className="user-progress-icon">
              <Icon name="box-linear" width={20} />
            </div>
            <span>Processing</span>
          </div>
          <div className={`user-progress-line${shipped ? " completed" : ""}`} />
          <div className={`user-progress-step${shipped ? " completed" : " active"}`}>
            <div className="user-progress-icon">
              <Icon name="delivery-linear" width={20} />
            </div>
            <span>Shipped</span>
          </div>
          <div className={`user-progress-line${delivered ? " completed" : ""}`} />
          <div className={`user-progress-step${delivered ? " completed" : ""}`}>
            <div className="user-progress-icon">
              <Icon name="check-circle-linear" width={20} />
            </div>
            <span>Delivered</span>
          </div>
        </div>
      )}

      <section className="user-order-items-section">
        <h2>Order Items</h2>
        <div className="user-order-items-list">
          {items.map((item) => (
            <div key={item.id} className="user-order-item">
              <div className="user-order-item-image">
                <BookCover src={item.coverImage} title={item.title} />
              </div>
              <div className="user-order-item-details">
                <h4>
                  <Link href={`/books/${item.bookId}`} style={{ color: "inherit" }}>
                    {item.title}
                  </Link>
                </h4>
                <p>{item.author}</p>
                <p style={{ fontSize: "0.875rem", color: "var(--user-zinc-500)" }}>Quantity: {item.quantity}</p>
              </div>
              <div className="user-order-item-price">{formatCents(item.priceCents * item.quantity)}</div>
            </div>
          ))}
        </div>
      </section>

      <div className="user-order-info-grid">
        <div className="user-info-card">
          <h3>Order Information</h3>
          <p>
            <strong>Order Number:</strong> {order.orderNumber}
          </p>
          <p>
            <strong>Order Date:</strong> {formatDateTime(order.createdAt)}
          </p>
          <p>
            <strong>Transaction ID:</strong> {order.transactionId}
          </p>
        </div>
        <div className="user-info-card">
          <h3>Shipping Address</h3>
          <p>{order.shippingAddress}</p>
          {order.shippingPhone ? <p>{order.shippingPhone}</p> : null}
        </div>
        <div className="user-info-card">
          <h3>Payment Summary</h3>
          <p>
            <strong>Payment Method:</strong> {order.paymentMethod}
          </p>
          <p>
            <strong>Subtotal:</strong> {formatCents(order.subtotalCents)}
          </p>
          <p>
            <strong>Tax:</strong> {formatCents(order.taxCents)}
          </p>
          <p>
            <strong>Total:</strong>{" "}
            <span style={{ fontSize: "1.125rem", fontWeight: 600, color: "var(--user-zinc-900)" }}>{formatCents(order.totalCents)}</span>
          </p>
        </div>
      </div>
    </>
  );
}
