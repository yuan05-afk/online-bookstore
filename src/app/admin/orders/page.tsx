import type { Metadata } from "next";
import Link from "next/link";
import { StatusBadge } from "@/components/admin-ui";
import { Icon } from "@/components/icon";
import { ORDER_STATUSES, type OrderStatus } from "@/db/schema";
import { formatShortDate } from "@/lib/format";
import { formatCents } from "@/lib/money";
import { adminOrders } from "@/server/queries";

export const metadata: Metadata = { title: "Manage Orders" };

export default async function AdminOrdersPage({ searchParams }: { searchParams: Promise<{ search?: string; status?: string }> }) {
  const params = await searchParams;
  const search = (params.search ?? "").trim().slice(0, 100);
  const status = (ORDER_STATUSES as readonly string[]).includes(params.status ?? "") ? (params.status as OrderStatus) : undefined;
  const orders = await adminOrders({ search, status });

  return (
    <>
      <h1 className="admin-page-title">Order Management</h1>

      <form method="GET" action="/admin/orders" className="admin-filter-bar" role="search">
        <div className="admin-input-icon">
          <Icon name="magnifer-linear" width={18} />
          <input type="search" name="search" className="admin-input" placeholder="Search by order #, customer..." defaultValue={search} aria-label="Search orders" />
        </div>
        <select name="status" className="admin-select" defaultValue={status ?? ""} aria-label="Status">
          <option value="">All Statuses</option>
          {ORDER_STATUSES.map((item) => (
            <option key={item} value={item}>
              {item}
            </option>
          ))}
        </select>
        <button type="submit" className="admin-btn admin-btn-primary">
          Filter
        </button>
        {search || status ? (
          <Link href="/admin/orders" className="admin-btn admin-btn-secondary">
            Clear
          </Link>
        ) : null}
      </form>

      <div className="admin-card">
        <div className="admin-table-wrapper">
          <table className="admin-table">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Date</th>
                <th className="text-right">Total</th>
                <th className="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              {orders.length === 0 ? (
                <tr>
                  <td colSpan={6} className="admin-text-center admin-text-muted" style={{ padding: "3rem" }}>
                    No orders found
                  </td>
                </tr>
              ) : (
                orders.map((order) => (
                  <tr key={order.id}>
                    <td>
                      <strong>{order.orderNumber}</strong>
                    </td>
                    <td>
                      <div style={{ fontWeight: "var(--admin-font-weight-medium)", color: "var(--admin-text-primary)" }}>{order.customerName}</div>
                      <div style={{ fontSize: "var(--admin-font-size-xs)", color: "var(--admin-text-secondary)", marginTop: "0.25rem" }}>{order.customerEmail}</div>
                    </td>
                    <td>
                      <StatusBadge status={order.status} />
                    </td>
                    <td className="admin-text-secondary">{formatShortDate(order.createdAt)}</td>
                    <td className="text-right">
                      <strong>{formatCents(order.totalCents)}</strong>
                    </td>
                    <td>
                      <div className="admin-table-actions">
                        <Link href={`/admin/orders/${order.id}`} className="admin-btn admin-btn-sm admin-btn-secondary">
                          View Details
                        </Link>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
