"use client";

import { useActionState, useEffect, useRef } from "react";
import { Icon } from "@/components/icon";
import { EMPTY_FORM, type FormState } from "@/lib/form-state";
import { changePasswordAction, updateProfileAction } from "@/server/profile-actions";

type Account = { firstName: string; lastName: string; email: string; phone: string };

function Feedback({ state }: { state: FormState }) {
  if (state.errors.length === 0 && !state.message) return null;
  const error = state.errors.length > 0;
  return (
    <div role={error ? "alert" : "status"} className="admin-card admin-mb-6" style={{ borderLeft: `4px solid ${error ? "var(--admin-danger)" : "var(--admin-success)"}` }}>
      <div className="admin-card-body">{error ? state.errors.join(" ") : state.message}</div>
    </div>
  );
}

export function AdminProfileForms({ account }: { account: Account }) {
  const [profile, profileAction, profilePending] = useActionState(updateProfileAction, EMPTY_FORM);
  const [password, passwordAction, passwordPending] = useActionState(changePasswordAction, EMPTY_FORM);
  const passwordForm = useRef<HTMLFormElement>(null);

  useEffect(() => {
    if (password.message) passwordForm.current?.reset();
  }, [password]);

  const v = { ...account, ...profile.values };

  return (
    <div className="admin-grid admin-grid-cols-2 admin-mb-6">
      <div className="admin-card">
        <div className="admin-card-header">
          <h2 className="admin-card-title">Profile Information</h2>
        </div>
        <div className="admin-card-body">
          <Feedback state={profile} />
          <form action={profileAction}>
            <div className="admin-form-group">
              <label htmlFor="firstName" className="admin-label">
                First Name *
              </label>
              <input type="text" id="firstName" name="firstName" className="admin-input" defaultValue={v.firstName} required />
            </div>
            <div className="admin-form-group">
              <label htmlFor="lastName" className="admin-label">
                Last Name *
              </label>
              <input type="text" id="lastName" name="lastName" className="admin-input" defaultValue={v.lastName} required />
            </div>
            <div className="admin-form-group">
              <label htmlFor="email" className="admin-label">
                Email *
              </label>
              <input type="email" id="email" name="email" className="admin-input" defaultValue={v.email} required />
              <small className="admin-text-secondary" style={{ fontSize: "var(--admin-font-size-xs)", display: "block", marginTop: "0.5rem" }}>
                Used for login and notifications
              </small>
            </div>
            <div className="admin-form-group">
              <label htmlFor="phone" className="admin-label">
                Phone
              </label>
              <input type="tel" id="phone" name="phone" className="admin-input" defaultValue={v.phone} />
            </div>
            <button type="submit" className="admin-btn admin-btn-primary" disabled={profilePending}>
              <Icon name="check-circle-linear" width={18} />
              {profilePending ? "Saving..." : "Update Profile"}
            </button>
          </form>
        </div>
      </div>

      <div className="admin-card">
        <div className="admin-card-header">
          <h2 className="admin-card-title">Change Password</h2>
        </div>
        <div className="admin-card-body">
          <Feedback state={password} />
          <form ref={passwordForm} action={passwordAction}>
            <div className="admin-form-group">
              <label htmlFor="currentPassword" className="admin-label">
                Current Password *
              </label>
              <input type="password" id="currentPassword" name="currentPassword" className="admin-input" required autoComplete="current-password" />
            </div>
            <div className="admin-form-group">
              <label htmlFor="newPassword" className="admin-label">
                New Password *
              </label>
              <input type="password" id="newPassword" name="newPassword" className="admin-input" required minLength={8} autoComplete="new-password" />
              <small className="admin-text-secondary" style={{ fontSize: "var(--admin-font-size-xs)", display: "block", marginTop: "0.5rem" }}>
                Minimum 8 characters
              </small>
            </div>
            <div className="admin-form-group">
              <label htmlFor="confirmPassword" className="admin-label">
                Confirm New Password *
              </label>
              <input type="password" id="confirmPassword" name="confirmPassword" className="admin-input" required minLength={8} autoComplete="new-password" />
            </div>
            <button type="submit" className="admin-btn admin-btn-primary" disabled={passwordPending}>
              <Icon name="lock-password-linear" width={18} />
              {passwordPending ? "Changing..." : "Change Password"}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
