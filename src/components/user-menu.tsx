"use client";

import Link from "next/link";
import { useEffect, useRef, useState } from "react";
import { Icon } from "@/components/icon";
import { logoutAction } from "@/server/auth-actions";

/** The name button in the header with its profile and sign-out dropdown. */
export function UserMenu({ firstName }: { firstName: string }) {
  const [open, setOpen] = useState(false);
  const root = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!open) return;
    const onPointer = (event: PointerEvent) => {
      if (!root.current?.contains(event.target as Node)) setOpen(false);
    };
    const onKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") setOpen(false);
    };
    document.addEventListener("pointerdown", onPointer);
    document.addEventListener("keydown", onKey);
    return () => {
      document.removeEventListener("pointerdown", onPointer);
      document.removeEventListener("keydown", onKey);
    };
  }, [open]);

  return (
    <div className="user-menu" ref={root}>
      <button type="button" className="user-menu-toggle" id="userMenuToggle" aria-expanded={open} aria-haspopup="menu" onClick={() => setOpen((value) => !value)}>
        <Icon name="user-circle-linear" width={18} />
        <span>{firstName}</span>
        <Icon name="alt-arrow-down-linear" width={14} />
      </button>
      <div className={`user-menu-dropdown${open ? " show" : ""}`} id="userMenuDropdown" role="menu">
        <Link href="/profile" className="user-menu-item" role="menuitem" onClick={() => setOpen(false)}>
          <Icon name="user-linear" width={18} />
          <span>My Profile</span>
        </Link>
        <form action={logoutAction}>
          <button type="submit" className="user-menu-item" role="menuitem" style={{ width: "100%", background: "none", border: 0, font: "inherit", cursor: "pointer", textAlign: "left" }}>
            <Icon name="logout-2-linear" width={18} />
            <span>Logout</span>
          </button>
        </form>
      </div>
    </div>
  );
}
