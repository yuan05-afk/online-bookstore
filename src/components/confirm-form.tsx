"use client";

import { useTransition } from "react";

/** A button that asks before running a server action, such as deleting an account. */
export function ConfirmButton({
  action,
  message,
  className,
  children,
  style,
}: {
  action: () => Promise<void>;
  message: string;
  className?: string;
  children: React.ReactNode;
  style?: React.CSSProperties;
}) {
  const [pending, start] = useTransition();
  return (
    <button
      type="button"
      className={className}
      style={style}
      disabled={pending}
      onClick={() => {
        if (window.confirm(message)) start(() => action());
      }}
    >
      {children}
    </button>
  );
}
