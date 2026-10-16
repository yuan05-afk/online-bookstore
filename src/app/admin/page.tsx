import type { Metadata } from "next";
import Link from "next/link";
import { StatusBadge } from "@/components/admin-ui";
import { Icon } from "@/components/icon";
import { formatCents } from "@/lib/money";
import { adminStats, lowStockBooks, recentOrders } from "@/server/queries";

export const metadata: Metadata = { title: "Dashboard" };

export default async function AdminDashboardPage() {
  const [stats, recent, lowStock] = await Promise.all([adminStats(), recentOrders(10), lowStockBooks(10)]);

  const cards = [
    { label: "Total Books", icon: "book-2-linear", value: stats.totalBooks.toLocaleString("en-US"), trend: "Inventory status", positive: false },
    { label: "Total Orders", icon: "bag-3-linear", value: stats.totalOrders.toLocaleString("en-US"), trend: `${stats.weekOrders} in the last 7 days`, positive: stats.weekOrders > 0 },
    { label: "Total Users", icon: "users-group-rounded-linear", value: stats.totalUsers.toLocaleString("en-US"), trend: `${stats.weekUsers} new in the last 7 days`, positive: stats.weekUsers > 0 },
    { label: "Total Revenue", icon: "dollar-linear", value: formatCents(stats.revenueCents), trend: `${formatCents(stats.weekRevenueCents)} in the last 7 days`, positive: stats.weekRevenueCents > 0 },
  ];

  return (
    <>
      <h1 className="admin-page-title">Dashboard Overview</h1>

      <div className="admin-grid admin-grid-cols-4 admin-mb-8">
        {cards.map((card) => (
          <div key={card.label} className="admin-stats-card">
            <div className="admin-stats-header">
              <span className="admin-stats-label">{card.label}</span>
              <div className="admin-stats-icon">
                <Icon name={card.icon} width={20} />
              </div>
            </div>
            <div className="admin-stats-value">{card.value}</div>
            <div className={`admin-stats-trend ${card.positive ? "positive" : "admin-text-muted"}`}>
              {card.positive ? <Icon name="trending-up-linear" width={14} /> : null}
              {card.trend}
            </div>
          </div>
        ))}
      </div>

      <div className="admin-grid admin-grid-cols-3">
        <div style={{ gridColumn: "span 2" }}>
          <div className="admin-card">
            <div className="admin-card-header">
              <h2 className="admin-card-title">Recent Orders</h2>
              <Link href="/admin/orders" className="admin-btn admin-btn-sm admin-btn-secondary">
                View All
              </Link>
            </div>
            <div className="admin-table-wrapper">
              {recent.length === 0 ? (
                <div className="admin-card-body">
                  <p className="admin-text-muted">No orders yet.</p>
                </div>
              ) : (
                <table className="admin-table">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Customer</th>
                      <th>Status</th>
                      <th className="text-right">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    {recent.map((order) => (
                      <tr key={order.id}>
                        <td>
                          <Link href={`/admin/orders/${order.id}`} style={{ color: "inherit" }}>
                            <strong>{order.orderNumber}</strong>
                          </Link>
                        </td>
                        <td>{order.customerName}</td>
                        <td>
                          <StatusBadge status={order.status} />
                        </td>
                        <td className="text-right">
                          <strong>{formatCents(order.totalCents)}</strong>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
          </div>
        </div>

        <div>
          <div className="admin-card">
            <div className="admin-card-header">
              <h2 className="admin-card-title">Low Stock Alert</h2>
            </div>
            {lowStock.length === 0 ? (
              <div className="admin-card-body">
                <p className="admin-text-muted">All books are well stocked.</p>
              </div>
            ) : (
              <>
                <div style={{ maxHeight: 400, overflowY: "auto" }}>
                  {lowStock.map((book) => (
                    <div key={book.id} style={{ padding: "1rem 1.5rem", borderBottom: "1px solid var(--admin-border-light)", display: "flex", alignItems: "center", gap: "1rem" }}>
                      <div style={{ flex: 1, minWidth: 0 }}>
                        <div style={{ fontSize: "var(--admin-font-size-base)", fontWeight: "var(--admin-font-weight-medium)", color: "var(--admin-text-primary)", overflow: "hidden", textOverflow: "ellipsis", whiteSpace: "nowrap" }}>
                          {book.title}
                        </div>
                        <div style={{ fontSize: "var(--admin-font-size-xs)", color: book.stock === 0 ? "var(--admin-danger)" : "var(--admin-warning)", marginTop: "0.25rem" }}>
                          {book.stock === 0 ? "Out of stock" : `${book.stock} units left`}
                        </div>
                      </div>
                      <Link href={`/admin/books/${book.id}/edit`} className="admin-btn-icon" aria-label={`Edit ${book.title}`}>
                        <Icon name="pen-linear" width={18} />
                      </Link>
                    </div>
                  ))}
                </div>
                <div className="admin-card-footer admin-text-center">
                  <Link href="/admin/books" className="admin-text-secondary" style={{ fontSize: "var(--admin-font-size-sm)", fontWeight: "var(--admin-font-weight-medium)", textDecoration: "none" }}>
                    Manage Inventory
                  </Link>
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </>
  );
}

