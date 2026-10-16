import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { StatusBadge } from "@/components/admin-ui";
import { ConfirmButton } from "@/components/confirm-form";
import { Icon } from "@/components/icon";
import { requireAdmin } from "@/lib/auth";
import { formatDateTime, formatShortDate } from "@/lib/format";
import { formatCents } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { deleteAccountAction, promoteAccountAction } from "@/server/admin-actions";
import { accountSummary } from "@/server/queries";

export const metadata: Metadata = { title: "Account Details" };

const label = { marginBottom: "0.25rem" } as const;

export default async function AdminAccountPage({ params }: { params: Promise<{ id: string }> }) {
  const me = await requireAdmin();
  const id = Number((await params).id);
  const summary = Number.isInteger(id) && id > 0 ? await accountSummary(id) : null;
  if (!summary) redirect(withNotice("/admin/accounts", "account_not_found"));
  const { account, recent, orderCount, spentCents } = summary;
  const isMe = account.id === me.id;

  return (
    <>
      <Link href="/admin/accounts" className="admin-back-link">
        <Icon name="arrow-left-linear" width={18} />
        Back to Accounts
      </Link>

      <div className="admin-flex-between admin-mb-6">
        <h1 className="admin-page-title" style={{ margin: 0 }}>
          {account.firstName} {account.lastName}
        </h1>
        <span className={`admin-badge admin-badge-${account.role === "admin" ? "info" : "neutral"}`} style={{ fontSize: "var(--admin-font-size-base)", padding: "var(--admin-space-2) var(--admin-space-4)" }}>
          {account.role === "admin" ? "Administrator" : "User"}
        </span>
      </div>

      <div className="admin-grid admin-grid-cols-2 admin-mb-6">
        <div className="admin-card">
          <div className="admin-card-header">
            <h2 className="admin-card-title">Account Information</h2>
          </div>
          <div className="admin-card-body">
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                User ID
              </div>
              <div>#{account.id}</div>
            </div>
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Email
              </div>
              <div>{account.email}</div>
            </div>
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Phone
              </div>
              <div>{account.phone || "Not provided"}</div>
            </div>
            <div style={{ marginBottom: "1rem" }}>
              <div className="admin-label" style={label}>
                Registered
              </div>
              <div>{formatDateTime(account.createdAt)}</div>
            </div>
            <div>
              <div className="admin-label" style={label}>
                Orders
              </div>
              <div>
                {orderCount} placed, {formatCents(spentCents)} spent
              </div>
            </div>
          </div>
        </div>

        <div className="admin-card">
          <div className="admin-card-header">
            <h2 className="admin-card-title">Manage User</h2>
          </div>
          <div className="admin-card-body">
            {isMe ? (
              <div className="admin-card" style={{ background: "var(--admin-warning-bg)", border: "1px solid var(--admin-warning)", padding: "1rem" }}>
                <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", color: "var(--admin-warning)" }}>
                  <Icon name="info-circle-linear" width={20} />
                  <strong>This is your account</strong>
                </div>
                <p style={{ margin: "0.5rem 0 0 0", fontSize: "var(--admin-font-size-sm)" }}>You cannot change your own role or delete yourself. Use the Profile page to update your details.</p>
              </div>
            ) : account.role === "admin" ? (
              <div className="admin-card" style={{ background: "var(--admin-info-bg)", border: "1px solid var(--admin-primary)", padding: "1rem" }}>
                <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", color: "var(--admin-primary)" }}>
                  <Icon name="shield-user-linear" width={20} />
                  <strong>Administrator Account</strong>
                </div>
                <p style={{ margin: "0.5rem 0 0 0", fontSize: "var(--admin-font-size-sm)" }}>Admin accounts cannot be demoted here, to prevent losing admin access by accident.</p>
              </div>
            ) : (
              <>
                <div className="admin-label" style={{ marginBottom: "0.5rem" }}>
                  Current Role
                </div>
                <div style={{ marginBottom: "1rem" }}>
                  <span className="admin-badge admin-badge-neutral">User</span>
                </div>
                <ConfirmButton
                  action={promoteAccountAction.bind(null, account.id)}
                  message="Promote this user to Administrator? They will have full access to the admin panel."
                  className="admin-btn admin-btn-primary"
                  style={{ width: "100%" }}
                >
                  <Icon name="shield-user-linear" width={18} />
                  Promote to Administrator
                </ConfirmButton>
                <p style={{ margin: "0.75rem 0 1.25rem", fontSize: "var(--admin-font-size-xs)", color: "var(--admin-text-secondary)" }}>This action asks for confirmation and cannot be undone from this screen.</p>
                <ConfirmButton
                  action={deleteAccountAction.bind(null, account.id)}
                  message={`Delete ${account.firstName} ${account.lastName}? Their order history is kept. This cannot be undone.`}
                  className="admin-btn admin-btn-danger"
                  style={{ width: "100%" }}
                >
                  <Icon name="trash-bin-trash-linear" width={18} />
                  Delete Account
                </ConfirmButton>
              </>
            )}
          </div>
        </div>
      </div>

      <div className="admin-card">
        <div className="admin-card-header">
          <h2 className="admin-card-title">Recent Orders (Last 10)</h2>
        </div>
        {recent.length === 0 ? (
          <div className="admin-card-body">
            <p className="admin-text-muted">No orders found for this user.</p>
          </div>
        ) : (
          <div className="admin-table-wrapper">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th className="text-right">Total</th>
                  <th className="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                {recent.map((order) => (
                  <tr key={order.id}>
                    <td>
                      <strong>{order.orderNumber}</strong>
                    </td>
                    <td className="admin-text-secondary">{formatShortDate(order.createdAt)}</td>
                    <td>
                      <StatusBadge status={order.status} />
                    </td>
                    <td className="text-right">
                      <strong>{formatCents(order.totalCents)}</strong>
                    </td>
                    <td>
                      <div className="admin-table-actions">
                        <Link href={`/admin/orders/${order.id}`} className="admin-btn admin-btn-sm admin-btn-secondary">
                          View Order
                        </Link>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </>
  );
}
