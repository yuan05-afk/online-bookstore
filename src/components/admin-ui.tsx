"use client";

import { useState } from "react";
import { Icon } from "@/components/icon";
import type { OrderStatus } from "@/db/schema";

/** Small cover thumbnail for admin tables, with the same fallback tile when the link is dead. */
export function AdminCover({ src, title }: { src: string | null; title: string }) {
  const [failed, setFailed] = useState(false);
  const box = { width: "2.5rem", height: "3.5rem", borderRadius: "var(--admin-radius-sm)", flexShrink: 0 } as const;
  if (!src || failed) {
    return (
      <div style={{ ...box, background: "var(--admin-bg-tertiary)", display: "flex", alignItems: "center", justifyContent: "center" }}>
        <Icon name="book-2-linear" width={20} style={{ color: "var(--admin-text-muted)" }} />
      </div>
    );
  }
  return (
    // eslint-disable-next-line @next/next/no-img-element -- remote covers on many hosts
    <img src={src} alt={title} loading="lazy" decoding="async" referrerPolicy="no-referrer" onError={() => setFailed(true)} style={{ ...box, objectFit: "cover", border: "1px solid var(--admin-border-light)" }} />
  );
}

const STATUS_BADGE: Record<OrderStatus, string> = {
  Processing: "warning",
  Shipped: "info",
  Delivered: "success",
  Cancelled: "danger",
};

export function StatusBadge({ status, large = false }: { status: OrderStatus; large?: boolean }) {
  return (
    <span className={`admin-badge admin-badge-${STATUS_BADGE[status]}`} style={large ? { fontSize: "var(--admin-font-size-base)", padding: "var(--admin-space-2) var(--admin-space-4)" } : undefined}>
      {status}
    </span>
  );
}
