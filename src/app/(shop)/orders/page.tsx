import type { Metadata } from "next";
import Link from "next/link";
import { Icon } from "@/components/icon";
import { requireCustomer } from "@/lib/auth";
import { formatDate } from "@/lib/format";
import { formatCents } from "@/lib/money";
import { userOrders } from "@/server/queries";

export const metadata: Metadata = { title: "Order History" };

export default async function OrdersPage() {
  const user = await requireCustomer("/orders");
  const orders = await userOrders(user.id);

  return (
    <>
      <h1 className="user-page-title">Order History</h1>

      {orders.length === 0 ? (
        <div className="user-orders-empty">
          <Icon name="box-linear" width={64} style={{ color: "var(--user-zinc-300)" }} />
          <p>You haven&apos;t placed any orders yet.</p>
          <Link href="/catalog" className="user-btn user-btn-primary">
            Start Shopping
          </Link>
        </div>
      ) : (
        <div className="user-orders-list">
          {orders.map((order) => (
            <Link key={order.id} href={`/orders/${order.id}`} className="user-order-card" style={{ textDecoration: "none", color: "inherit" }}>
              <div className="user-order-header">
                <div className="user-order-icon">
                  <Icon name="box-linear" width={24} />
                </div>
                <div className="user-order-info">
                  <h3>Order #{order.orderNumber}</h3>
                  <p>{formatDate(order.createdAt)}</p>
                </div>
              </div>
              <div className="user-order-body">
                <div className="user-order-meta">
                  <span className={`user-order-status ${order.status.toLowerCase()}`}>{order.status}</span>
                  <span className="user-order-total">{formatCents(order.totalCents)}</span>
                </div>
                <Icon name="alt-arrow-right-linear" className="user-order-arrow" width={20} />
              </div>
            </Link>
          ))}
        </div>
      )}
    </>
  );
}
