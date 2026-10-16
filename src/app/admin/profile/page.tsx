import type { Metadata } from "next";
import { eq } from "drizzle-orm";
import { getDb } from "@/db";
import { users } from "@/db/schema";
import { requireAdmin } from "@/lib/auth";
import { formatDate } from "@/lib/format";
import { AdminProfileForms } from "./profile-forms";

export const metadata: Metadata = { title: "My Profile" };

export default async function AdminProfilePage() {
  const me = await requireAdmin();
  const [admin] = await getDb().select().from(users).where(eq(users.id, me.id)).limit(1);

  return (
    <>
      <h1 className="admin-page-title">My Profile</h1>
      <AdminProfileForms account={{ firstName: admin.firstName, lastName: admin.lastName, email: admin.email, phone: admin.phone ?? "" }} />

      <div className="admin-card">
        <div className="admin-card-header">
          <h2 className="admin-card-title">Account Details</h2>
        </div>
        <div className="admin-card-body">
          <div className="admin-grid admin-grid-cols-3">
            <div>
              <div className="admin-label" style={{ marginBottom: "0.5rem" }}>
                Role
              </div>
              <span className="admin-badge admin-badge-info">Administrator</span>
            </div>
            <div>
              <div className="admin-label" style={{ marginBottom: "0.5rem" }}>
                Account Status
              </div>
              <span className="admin-badge admin-badge-success">Active</span>
            </div>
            <div>
              <div className="admin-label" style={{ marginBottom: "0.5rem" }}>
                Member Since
              </div>
              <div>{formatDate(admin.createdAt)}</div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
