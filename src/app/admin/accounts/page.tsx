import type { Metadata } from "next";
import Link from "next/link";
import { ConfirmButton } from "@/components/confirm-form";
import { Icon } from "@/components/icon";
import { requireAdmin } from "@/lib/auth";
import { formatShortDate } from "@/lib/format";
import { deleteAccountAction } from "@/server/admin-actions";
import { adminAccounts } from "@/server/queries";

export const metadata: Metadata = { title: "User Accounts" };

export default async function AdminAccountsPage({ searchParams }: { searchParams: Promise<{ search?: string; role?: string }> }) {
  const me = await requireAdmin();
  const params = await searchParams;
  const search = (params.search ?? "").trim().slice(0, 100);
  const role = params.role === "admin" || params.role === "user" ? params.role : undefined;
  const accounts = await adminAccounts({ search, role });

  return (
    <>
      <h1 className="admin-page-title">User Accounts</h1>

      <form method="GET" action="/admin/accounts" className="admin-filter-bar" role="search">
        <div className="admin-input-icon">
          <Icon name="magnifer-linear" width={18} />
          <input type="search" name="search" className="admin-input" placeholder="Search by name or email..." defaultValue={search} aria-label="Search accounts" />
        </div>
        <select name="role" className="admin-select" defaultValue={role ?? ""} aria-label="Role">
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
        <button type="submit" className="admin-btn admin-btn-primary">
          Filter
        </button>
        {search || role ? (
          <Link href="/admin/accounts" className="admin-btn admin-btn-secondary">
            Clear
          </Link>
        ) : null}
      </form>

      <div className="admin-card">
        <div className="admin-table-wrapper">
          <table className="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Registered</th>
                <th className="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              {accounts.length === 0 ? (
                <tr>
                  <td colSpan={7} className="admin-text-center admin-text-muted" style={{ padding: "3rem" }}>
                    No users found
                  </td>
                </tr>
              ) : (
                accounts.map((account) => (
                  <tr key={account.id}>
                    <td className="admin-text-secondary">#{account.id}</td>
                    <td>
                      <div style={{ fontWeight: "var(--admin-font-weight-medium)", color: "var(--admin-text-primary)" }}>
                        {account.firstName} {account.lastName}
                      </div>
                    </td>
                    <td className="admin-text-secondary">{account.email}</td>
                    <td className="admin-text-secondary">{account.phone || "-"}</td>
                    <td>{account.role === "admin" ? <span className="admin-badge admin-badge-info">Admin</span> : <span className="admin-badge admin-badge-neutral">User</span>}</td>
                    <td className="admin-text-secondary">{formatShortDate(account.createdAt)}</td>
                    <td>
                      <div className="admin-table-actions">
                        <Link href={`/admin/accounts/${account.id}`} className="admin-btn admin-btn-sm admin-btn-secondary">
                          View Details
                        </Link>
                        {account.id !== me.id ? (
                          <ConfirmButton
                            action={deleteAccountAction.bind(null, account.id)}
                            message={`Delete ${account.firstName} ${account.lastName}? Their order history is kept. This cannot be undone.`}
                            className="admin-btn admin-btn-sm admin-btn-danger"
                          >
                            Delete
                          </ConfirmButton>
                        ) : null}
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
