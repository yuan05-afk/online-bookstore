"use client";

import Link from "next/link";
import { useActionState } from "react";
import { Icon } from "@/components/icon";
import { EMPTY_FORM } from "@/lib/form-state";
import { loginAction } from "@/server/auth-actions";

export function LoginForm({ next }: { next?: string }) {
  const [state, action, pending] = useActionState(loginAction, EMPTY_FORM);

  return (
    <>
      {state.errors.length > 0 ? (
        <div className="auth-alert auth-alert-error" role="alert">
          <ul>
            {state.errors.map((error) => (
              <li key={error}>{error}</li>
            ))}
          </ul>
        </div>
      ) : null}

      <form action={action} className="auth-form">
        {next ? <input type="hidden" name="next" value={next} /> : null}

        <div className="auth-form-group">
          <label className="auth-form-label" htmlFor="email">
            Email address
          </label>
          <div className="auth-input-wrapper">
            <Icon name="letter-linear" className="auth-input-icon" width={18} />
            <input
              id="email"
              type="email"
              name="email"
              defaultValue={state.values.email ?? ""}
              required
              autoComplete="email"
              className="auth-input"
              placeholder="Enter your email"
            />
          </div>
        </div>

        <div className="auth-form-group">
          <label className="auth-form-label" htmlFor="password">
            Password
          </label>
          <div className="auth-input-wrapper">
            <Icon name="lock-password-linear" className="auth-input-icon" width={18} />
            <input
              id="password"
              type="password"
              name="password"
              required
              autoComplete="current-password"
              className="auth-input"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div style={{ textAlign: "right", paddingTop: "0.25rem" }}>
          <Link href="/contact" className="auth-forgot-link">
            Forgot password?
          </Link>
        </div>

        <button type="submit" className="auth-submit" disabled={pending} aria-busy={pending}>
          {pending ? "Signing in..." : "Sign in"}
        </button>
      </form>
    </>
  );
}
