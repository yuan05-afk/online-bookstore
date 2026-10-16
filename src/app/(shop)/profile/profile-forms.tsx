"use client";

import { useActionState, useEffect, useRef } from "react";
import { Icon } from "@/components/icon";
import { EMPTY_FORM, type FormState } from "@/lib/form-state";
import { changePasswordAction, updateAddressAction, updateProfileAction } from "@/server/profile-actions";

type Account = {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state: string;
  zipCode: string;
  country: string;
};

function Feedback({ state }: { state: FormState }) {
  if (state.errors.length > 0) {
    return (
      <div className="notification notification-error show" role="alert" style={{ position: "static", marginBottom: "1rem" }}>
        {state.errors.join(" ")}
      </div>
    );
  }
  if (state.message) {
    return (
      <div className="notification notification-success show" role="status" style={{ position: "static", marginBottom: "1rem" }}>
        {state.message}
      </div>
    );
  }
  return null;
}

export function ProfileForms({ account }: { account: Account }) {
  const [personal, personalAction, personalPending] = useActionState(updateProfileAction, EMPTY_FORM);
  const [address, addressAction, addressPending] = useActionState(updateAddressAction, EMPTY_FORM);
  const [password, passwordAction, passwordPending] = useActionState(changePasswordAction, EMPTY_FORM);
  const passwordForm = useRef<HTMLFormElement>(null);

  // A changed password should not stay in the fields.
  useEffect(() => {
    if (password.message) passwordForm.current?.reset();
  }, [password]);

  const p = { ...account, ...personal.values };
  const a = { ...account, ...address.values };

  return (
    <div className="user-profile-container">
      <form action={personalAction} className="user-profile-section" onSubmit={(event) => !window.confirm("Update your personal information?") && event.preventDefault()}>
        <section className="user-checkout-section">
          <h2>Personal Information</h2>
          <Feedback state={personal} />
          <div className="user-form-row">
            <div className="user-form-group">
              <label htmlFor="firstName">First Name *</label>
              <input type="text" id="firstName" name="firstName" className="user-input" defaultValue={p.firstName} required autoComplete="given-name" />
            </div>
            <div className="user-form-group">
              <label htmlFor="lastName">Last Name *</label>
              <input type="text" id="lastName" name="lastName" className="user-input" defaultValue={p.lastName} required autoComplete="family-name" />
            </div>
          </div>
          <div className="user-form-group">
            <label htmlFor="email">Email Address *</label>
            <input type="email" id="email" name="email" className="user-input" defaultValue={p.email} required autoComplete="email" />
          </div>
          <div className="user-form-group">
            <label htmlFor="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" className="user-input" defaultValue={p.phone} autoComplete="tel" />
          </div>
          <button type="submit" className="user-btn user-btn-primary" disabled={personalPending}>
            <Icon name="diskette-linear" width={18} />
            {personalPending ? "Saving..." : "Save Personal Information"}
          </button>
        </section>
      </form>

      <form action={addressAction} className="user-profile-section" onSubmit={(event) => !window.confirm("Update your address?") && event.preventDefault()}>
        <section className="user-checkout-section">
          <h2>Address Information</h2>
          <Feedback state={address} />
          <div className="user-form-group">
            <label htmlFor="address">Street Address</label>
            <input type="text" id="address" name="address" className="user-input" defaultValue={a.address} autoComplete="street-address" />
          </div>
          <div className="user-form-row">
            <div className="user-form-group">
              <label htmlFor="city">City</label>
              <input type="text" id="city" name="city" className="user-input" defaultValue={a.city} autoComplete="address-level2" />
            </div>
            <div className="user-form-group">
              <label htmlFor="state">State</label>
              <input type="text" id="state" name="state" className="user-input" defaultValue={a.state} autoComplete="address-level1" />
            </div>
          </div>
          <div className="user-form-row">
            <div className="user-form-group">
              <label htmlFor="zipCode">ZIP Code</label>
              <input type="text" id="zipCode" name="zipCode" className="user-input" defaultValue={a.zipCode} autoComplete="postal-code" />
            </div>
            <div className="user-form-group">
              <label htmlFor="country">Country</label>
              <input type="text" id="country" name="country" className="user-input" defaultValue={a.country} autoComplete="country-name" />
            </div>
          </div>
          <button type="submit" className="user-btn user-btn-primary" disabled={addressPending}>
            <Icon name="diskette-linear" width={18} />
            {addressPending ? "Saving..." : "Save Address"}
          </button>
        </section>
      </form>

      <form ref={passwordForm} action={passwordAction} className="user-profile-section" onSubmit={(event) => !window.confirm("Change your password?") && event.preventDefault()}>
        <section className="user-checkout-section">
          <h2>Change Password</h2>
          <Feedback state={password} />
          <div className="user-form-group">
            <label htmlFor="currentPassword">Current Password *</label>
            <input type="password" id="currentPassword" name="currentPassword" className="user-input" required autoComplete="current-password" />
          </div>
          <div className="user-form-row">
            <div className="user-form-group">
              <label htmlFor="newPassword">New Password *</label>
              <input type="password" id="newPassword" name="newPassword" className="user-input" required minLength={8} autoComplete="new-password" />
              <small>At least 8 characters</small>
            </div>
            <div className="user-form-group">
              <label htmlFor="confirmPassword">Confirm New Password *</label>
              <input type="password" id="confirmPassword" name="confirmPassword" className="user-input" required minLength={8} autoComplete="new-password" />
            </div>
          </div>
          <button type="submit" className="user-btn user-btn-primary" disabled={passwordPending}>
            <Icon name="lock-password-linear" width={18} />
            {passwordPending ? "Changing..." : "Change Password"}
          </button>
        </section>
      </form>
    </div>
  );
}
