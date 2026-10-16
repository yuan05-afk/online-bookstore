"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

/** Sign In and Get Started, minus the one for the page you are already on. */
export function AuthActions() {
  const pathname = usePathname();
  return (
    <div className="auth-actions">
      {pathname !== "/login" ? (
        <Link href="/login" className="auth-btn-signin">
          Sign In
        </Link>
      ) : null}
      {pathname !== "/register" ? (
        <Link href="/register" className="auth-btn-primary">
          Get Started
        </Link>
      ) : null}
    </div>
  );
}
