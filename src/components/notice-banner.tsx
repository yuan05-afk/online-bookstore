"use client";

import { useEffect, useState } from "react";
import { usePathname, useSearchParams } from "next/navigation";
import { resolveNotice } from "@/lib/notices";

/**
 * Shows the message for `?notice=code` once, then removes the code from the address bar
 * so a refresh does not repeat it. Only codes from lib/notices.ts are ever shown.
 */
export function NoticeBanner({ variant = "user" }: { variant?: "user" | "admin" | "auth" }) {
  const params = useSearchParams();
  const pathname = usePathname();
  const code = params.get("notice") ?? undefined;
  const [visible, setVisible] = useState<string | null>(null);
  const [seen, setSeen] = useState<string | undefined>(undefined);

  // Show a new code as soon as it arrives. Doing this while rendering avoids an extra render pass.
  if (code && code !== seen) {
    setSeen(code);
    setVisible(code);
  }

  useEffect(() => {
    if (!code) return;
    const next = new URLSearchParams(window.location.search);
    next.delete("notice");
    const query = next.toString();
    window.history.replaceState(null, "", query ? `${pathname}?${query}` : pathname);
    const timer = window.setTimeout(() => setVisible(null), 6000);
    return () => window.clearTimeout(timer);
  }, [code, pathname]);

  const notice = resolveNotice(visible ?? undefined);
  if (!notice) return null;

  if (variant === "auth") {
    return (
      <div role={notice.type === "error" ? "alert" : "status"} className={`auth-alert auth-alert-${notice.type === "error" ? "error" : notice.type === "info" ? "info" : "success"}`}>
        <p>{notice.text}</p>
      </div>
    );
  }

  if (variant === "admin") {
    return (
      <div
        role={notice.type === "error" ? "alert" : "status"}
        className="admin-card admin-mb-6"
        style={{ borderLeft: `4px solid ${notice.type === "success" ? "var(--admin-success)" : notice.type === "error" ? "var(--admin-danger)" : "var(--admin-primary)"}` }}
      >
        <div className="admin-card-body">{notice.text}</div>
      </div>
    );
  }

  return (
    <div role={notice.type === "error" ? "alert" : "status"} className={`notification notification-${notice.type === "info" ? "success" : notice.type} show`}>
      {notice.text}
    </div>
  );
}
