"use client";

import { useTransition } from "react";
import { Icon } from "@/components/icon";
import { deleteBookAction } from "@/server/admin-actions";

export function DeleteBookButton({ id, title }: { id: number; title: string }) {
  const [pending, start] = useTransition();
  return (
    <button
      type="button"
      className="admin-btn-icon"
      style={{ color: "var(--admin-danger)" }}
      title="Delete"
      aria-label={`Delete ${title}`}
      disabled={pending}
      onClick={() => {
        if (window.confirm(`Delete "${title}"? This cannot be undone.`)) start(() => deleteBookAction(id));
      }}
    >
      <Icon name="trash-bin-trash-linear" width={18} />
    </button>
  );
}
