"use client";

import Link from "next/link";
import { useActionState } from "react";
import { Icon } from "@/components/icon";
import { EMPTY_FORM } from "@/lib/form-state";
import { registerAction } from "@/server/auth-actions";

function Field({
  label,
  name,
  type = "text",
  icon,
  placeholder,
  defaultValue,
  required,
  autoComplete,
}: {
  label: string;
  name: string;
  type?: string;
  icon: string;
  placeholder: string;
  defaultValue?: string;
  required?: boolean;
  autoComplete?: string;
}) {
  return (
    <div className="auth-form-group">
      <label className="auth-form-label" htmlFor={name}>
        {label}
      </label>
      <div className="auth-input-wrapper">
        <Icon name={icon} className="auth-input-icon" width={18} />
        <input
          id={name}
          type={type}
          name={name}
          defaultValue={defaultValue}
          required={required}
          autoComplete={autoComplete}
          className="auth-input"
          placeholder={placeholder}
        />
      </div>
    </div>
  );
}

export function RegisterForm() {
  const [state, action, pending] = useActionState(registerAction, EMPTY_FORM);
  const v = state.values;

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
        <div className="auth-grid-2">
          <Field label="First Name" name="firstName" icon="user-linear" placeholder="John" defaultValue={v.firstName} required autoComplete="given-name" />
          <Field label="Last Name" name="lastName" icon="user-linear" placeholder="Doe" defaultValue={v.lastName} required autoComplete="family-name" />
        </div>
        <Field label="Email address" name="email" type="email" icon="letter-linear" placeholder="john@example.com" defaultValue={v.email} required autoComplete="email" />
        <Field label="Phone (Optional)" name="phone" type="tel" icon="phone-linear" placeholder="(555) 123-4567" defaultValue={v.phone} autoComplete="tel" />
        <div className="auth-grid-2">
          <Field label="Password" name="password" type="password" icon="lock-password-linear" placeholder="At least 8 characters" required autoComplete="new-password" />
          <Field label="Confirm Password" name="confirmPassword" type="password" icon="lock-password-linear" placeholder="Confirm password" required autoComplete="new-password" />
        </div>

        <div className="auth-pt-2">
          <button type="submit" className="auth-submit" disabled={pending} aria-busy={pending}>
            {pending ? "Creating account..." : "Create Account"}
          </button>
          <p className="auth-text-xs auth-text-center auth-mt-3" style={{ color: "var(--auth-zinc-400)" }}>
            By registering, you agree to our{" "}
            <Link href="/terms" className="auth-link">
              Terms
            </Link>{" "}
            and{" "}
            <Link href="/privacy" className="auth-link">
              Privacy Policy
            </Link>
            .
          </p>
        </div>
      </form>
    </>
  );
}
