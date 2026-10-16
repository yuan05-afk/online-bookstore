import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { AdminCover, StatusBadge } from "@/components/admin-ui";
import { Icon } from "@/components/icon";
import { ORDER_STATUSES } from "@/db/schema";
import { formatDateTime } from "@/lib/format";
import { formatCents } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { updateOrderAction } from "@/server/admin-actions";
import { adminOrder } from "@/server/queries";

export const metadata: Metadata = { title: "Order Details" };

const label = { marginBottom: "0.25rem" } as const;

export default async function AdminOrderPage({ params }: { params: Promise<{ id: string }> }) {
  const id = Number((await params).id);
  const found = await adminOrder(id);
  if (!found) redirect(withNotice("/admin/orders", "order_not_found"));
  const { order, items } = found;

  return (
    <>
      <Link href="/admin/orders" className="admin-back-link">
        <Icon name="arrow-left-linear" width={18} />
        Back to Orders
      </Link>

      <div className="admin-flex-between admin-mb-6">
        <h1 className="admin-page-title" style={{ margin: 0 }}>
          Order #{order.orderNumber}
        </h1>
        <StatusBadge status={order.status} large />
      </div>

      <div className="admin-grid admin-grid-cols-2 admin-mb-6">
        <div className="admin-card">
          <div className="admin-card-header">
            <h2 className="admin-card-title">Order Information</h2>
          </div>
          <div className="admin-card-body">
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Order Date
              </div>
              <div>{formatDateTime(order.createdAt)}</div>
            </div>
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Transaction ID
              </div>
              <div style={{ fontFamily: "monospace", fontSize: "var(--admin-font-size-sm)" }}>{order.transactionId}</div>
            </div>
            <div>
              <div className="admin-label" style={label}>
                Payment Method
              </div>
              <div>{order.paymentMethod}</div>
            </div>
          </div>
        </div>

        <div className="admin-card">
          <div className="admin-card-header">
            <h2 className="admin-card-title">Customer Information</h2>
          </div>
          <div className="admin-card-body">
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Name
              </div>
              <div>
                {order.userId ? (
                  <Link href={`/admin/accounts/${order.userId}`} style={{ color: "inherit", textDecoration: "underline" }}>
                    {order.customerName}
                  </Link>
                ) : (
                  <>
                    {order.customerName} <span className="admin-text-muted">(account deleted)</span>
                  </>
                )}
              </div>
            </div>
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Email
              </div>
              <div>{order.customerEmail}</div>
            </div>
            <div>
              <div className="admin-label" style={label}>
                Phone
              </div>
              <div>{order.shippingPhone || "Not provided"}</div>
            </div>
          </div>
        </div>
      </div>

      <div className="admin-grid admin-grid-cols-3 admin-mb-6">
        <div className="admin-card" style={{ gridColumn: "span 2" }}>
          <div className="admin-card-header">
            <h2 className="admin-card-title">Shipping Address</h2>
          </div>
          <div className="admin-card-body">{order.shippingAddress}</div>
        </div>
        <div className="admin-card">
          <div className="admin-card-header">
            <h2 className="admin-card-title">Order Total</h2>
          </div>
          <div className="admin-card-body">
            <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "0.5rem" }}>
              <span className="admin-text-secondary">Subtotal:</span>
              <span>{formatCents(order.subtotalCents)}</span>
            </div>
            <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "0.5rem" }}>
              <span className="admin-text-secondary">Tax:</span>
              <span>{formatCents(order.taxCents)}</span>
            </div>
            <div style={{ borderTop: "2px solid var(--admin-border-light)", paddingTop: "0.5rem", marginTop: "0.5rem", display: "flex", justifyContent: "space-between" }}>
              <strong>Total:</strong>
              <strong style={{ fontSize: "var(--admin-font-size-xl)", color: "var(--admin-primary)" }}>{formatCents(order.totalCents)}</strong>
            </div>
          </div>
        </div>
      </div>

      <div className="admin-card admin-mb-6">
        <div className="admin-card-header">
          <h2 className="admin-card-title">Order Items</h2>
        </div>
        <div className="admin-table-wrapper">
          <table className="admin-table">
            <thead>
              <tr>
                <th style={{ width: 60 }}>Cover</th>
                <th>Book Details</th>
                <th>ISBN</th>
                <th className="text-right">Price</th>
                <th className="text-right">Quantity</th>
                <th className="text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              {items.map((item) => (
                <tr key={item.id}>
                  <td>
                    <AdminCover src={item.coverImage} title={item.title} />
                  </td>
                  <td>
                    <div style={{ fontWeight: "var(--admin-font-weight-medium)", color: "var(--admin-text-primary)" }}>{item.title}</div>
                    <div style={{ fontSize: "var(--admin-font-size-xs)", color: "var(--admin-text-secondary)", marginTop: "0.25rem" }}>by {item.author}</div>
                  </td>
                  <td className="admin-text-secondary" style={{ fontFamily: "monospace", fontSize: "var(--admin-font-size-sm)" }}>
                    {item.isbn}
                  </td>
                  <td className="text-right">{formatCents(item.priceCents)}</td>
                  <td className="text-right">
                    <span className="admin-badge admin-badge-neutral">×{item.quantity}</span>
                  </td>
                  <td className="text-right">
                    <strong>{formatCents(item.priceCents * item.quantity)}</strong>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className="admin-card">
        <div className="admin-card-header">
          <h2 className="admin-card-title">Update Order Status</h2>
        </div>
        <div className="admin-card-body">
          <form action={updateOrderAction.bind(null, order.id)}>
            <div className="admin-grid admin-grid-cols-2">
              <div className="admin-form-group">
                <label htmlFor="status" className="admin-label">
                  Status
                </label>
                <select id="status" name="status" className="admin-select" defaultValue={order.status} required>
                  {ORDER_STATUSES.map((status) => (
                    <option key={status} value={status}>
                      {status}
                    </option>
                  ))}
                </select>
                <small className="admin-text-secondary" style={{ fontSize: "var(--admin-font-size-xs)", display: "block", marginTop: "0.5rem" }}>
                  Cancelling an order puts its books back in stock.
                </small>
              </div>
              <div className="admin-form-group">
                <label htmlFor="notes" className="admin-label">
                  Notes (visible to the customer when cancelled)
                </label>
                <textarea id="notes" name="notes" className="admin-textarea" rows={3} defaultValue={order.notes ?? ""} maxLength={1000} />
              </div>
            </div>
            <button type="submit" className="admin-btn admin-btn-primary">
              Update Status
            </button>
          </form>
        </div>
      </div>
    </>
  );
}
