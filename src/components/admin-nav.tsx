"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Icon } from "@/components/icon";
import { logoutAction } from "@/server/auth-actions";

const ITEMS = [
  { href: "/admin", label: "Dashboard", icon: "widget-linear", exact: true },
  { href: "/admin/books", label: "Books", icon: "book-2-linear" },
  { href: "/admin/orders", label: "Orders", icon: "bag-3-linear" },
  { href: "/admin/accounts", label: "Accounts", icon: "users-group-rounded-linear" },
] as const;

export function AdminNav() {
  const pathname = usePathname();
  const active = (href: string, exact?: boolean) => (exact ? pathname === href : pathname === href || pathname.startsWith(`${href}/`));

  return (
    <aside className="admin-sidebar">
      <div className="admin-sidebar-header">
        <h2 className="admin-sidebar-title">Admin Panel</h2>
      </div>

      <nav className="admin-sidebar-nav" aria-label="Admin">
        {ITEMS.map((item) => (
          <Link key={item.href} href={item.href} className={`admin-nav-item${active(item.href, "exact" in item) ? " active" : ""}`} aria-current={active(item.href, "exact" in item) ? "page" : undefined}>
            <Icon name={item.icon} width={18} />
            {item.label}
          </Link>
        ))}

        <div className="admin-nav-divider" />

        <Link href="/admin/profile" className={`admin-nav-item${active("/admin/profile") ? " active" : ""}`} aria-current={active("/admin/profile") ? "page" : undefined}>
          <Icon name="user-circle-linear" width={18} />
          Profile
        </Link>

        <div className="admin-nav-divider" />

        <form action={logoutAction}>
          <button type="submit" className="admin-nav-item danger" style={{ width: "100%", background: "none", border: 0, font: "inherit", cursor: "pointer", textAlign: "left" }}>
            <Icon name="logout-2-linear" width={18} />
            Logout
          </button>
        </form>
      </nav>
    </aside>
  );
}
