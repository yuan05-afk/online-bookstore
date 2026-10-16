"use client";

import { useActionState, useState } from "react";
import { Icon } from "@/components/icon";
import { EMPTY_FORM } from "@/lib/form-state";
import { formatCents } from "@/lib/money";
import { placeOrderAction } from "@/server/order-actions";

type Defaults = { firstName: string; lastName: string; address: string; city: string; state: string; zipCode: string; country: string; phone: string };
type Line = { id: number; title: string; quantity: number; priceCents: number };

function formatCard(value: string): string {
  return value.replace(/\D/g, "").slice(0, 19).replace(/(.{4})/g, "$1 ").trim();
}

export function CheckoutForm({
  defaults,
  lines,
  subtotalCents,
  taxCents,
  totalCents,
  taxLabel,
  years,
}: {
  defaults: Defaults;
  lines: Line[];
  subtotalCents: number;
  taxCents: number;
  totalCents: number;
  taxLabel: string;
  years: number[];
}) {
  const [state, action, pending] = useActionState(placeOrderAction, EMPTY_FORM);
  const [card, setCard] = useState("");
  const v: Defaults & { expiryMonth?: string; expiryYear?: string } = { ...defaults, ...state.values };

  return (
    <form
      action={action}
      onSubmit={(event) => {
        if (!window.confirm("Place this order? This is a demo store, so nothing will be charged.")) event.preventDefault();
      }}
    >
      {state.errors.length > 0 ? (
        <div className="notification notification-error show" role="alert" style={{ position: "static", marginBottom: "1.5rem" }}>
          <ul style={{ margin: 0, paddingLeft: "1.1rem" }}>
            {state.errors.map((error) => (
              <li key={error}>{error}</li>
            ))}
          </ul>
        </div>
      ) : null}

      <div className="user-checkout-grid">
        <div>
          <section className="user-checkout-section">
            <h2>Shipping Information</h2>
            <div className="user-form-row">
              <div className="user-form-group">
                <label htmlFor="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" className="user-input" defaultValue={v.firstName} required autoComplete="given-name" />
              </div>
              <div className="user-form-group">
                <label htmlFor="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" className="user-input" defaultValue={v.lastName} required autoComplete="family-name" />
              </div>
            </div>
            <div className="user-form-group">
              <label htmlFor="address">Street Address *</label>
              <input type="text" id="address" name="address" className="user-input" defaultValue={v.address} required autoComplete="street-address" />
            </div>
            <div className="user-form-row">
              <div className="user-form-group">
                <label htmlFor="city">City *</label>
                <input type="text" id="city" name="city" className="user-input" defaultValue={v.city} required autoComplete="address-level2" />
              </div>
              <div className="user-form-group">
                <label htmlFor="state">State *</label>
                <input type="text" id="state" name="state" className="user-input" defaultValue={v.state} required autoComplete="address-level1" />
              </div>
            </div>
            <div className="user-form-row">
              <div className="user-form-group">
                <label htmlFor="zipCode">ZIP Code *</label>
                <input type="text" id="zipCode" name="zipCode" className="user-input" defaultValue={v.zipCode} required autoComplete="postal-code" />
              </div>
              <div className="user-form-group">
                <label htmlFor="country">Country *</label>
                <input type="text" id="country" name="country" className="user-input" defaultValue={v.country} required autoComplete="country-name" />
              </div>
            </div>
            <div className="user-form-group">
              <label htmlFor="phone">Phone Number *</label>
              <input type="tel" id="phone" name="phone" className="user-input" defaultValue={v.phone} required autoComplete="tel" />
            </div>
          </section>

          <section className="user-checkout-section">
            <h2>Payment Information</h2>
            <p className="user-payment-note">
              <Icon name="info-circle-linear" width={16} />
              This is a mock payment system. No actual charges will be made.
            </p>
            <div className="user-form-group">
              <label htmlFor="cardNumber">Card Number *</label>
              <input
                type="text"
                id="cardNumber"
                name="cardNumber"
                className="user-input"
                placeholder="1234 5678 9012 3456"
                inputMode="numeric"
                autoComplete="off"
                maxLength={23}
                value={card}
                onChange={(event) => setCard(formatCard(event.target.value))}
                required
              />
              <small>
                Test card: 4532 0151 1283 0366 (approved). 4000 0000 0000 0002 is always declined.
              </small>
            </div>
            <div className="user-form-row">
              <div className="user-form-group">
                <label htmlFor="expiryMonth">Expiry Month *</label>
                <select id="expiryMonth" name="expiryMonth" className="user-select" defaultValue={v.expiryMonth} required>
                  <option value="">Month</option>
                  {Array.from({ length: 12 }, (_, index) => String(index + 1).padStart(2, "0")).map((month) => (
                    <option key={month} value={month}>
                      {month}
                    </option>
                  ))}
                </select>
              </div>
              <div className="user-form-group">
                <label htmlFor="expiryYear">Expiry Year *</label>
                <select id="expiryYear" name="expiryYear" className="user-select" defaultValue={v.expiryYear} required>
                  <option value="">Year</option>
                  {years.map((year) => (
                    <option key={year} value={year}>
                      {year}
                    </option>
                  ))}
                </select>
              </div>
              <div className="user-form-group">
                <label htmlFor="cvv">CVV *</label>
                <input type="password" id="cvv" name="cvv" className="user-input" placeholder="123" inputMode="numeric" maxLength={4} autoComplete="off" required />
              </div>
            </div>
          </section>
        </div>

        <aside className="user-cart-summary">
          <h2 className="user-cart-summary-title">Order Summary</h2>
          <div className="user-summary-rows">
            {lines.map((line) => (
              <div key={line.id} className="user-summary-row" style={{ fontSize: "0.8125rem", padding: "0.5rem 0", borderBottom: "1px solid var(--user-zinc-100)" }}>
                <span>
                  {line.title} × {line.quantity}
                </span>
                <span>{formatCents(line.priceCents * line.quantity)}</span>
              </div>
            ))}
          </div>
          <div className="user-summary-rows" style={{ marginTop: "1rem" }}>
            <div className="user-summary-row">
              <span>Subtotal:</span>
              <span>{formatCents(subtotalCents)}</span>
            </div>
            <div className="user-summary-row">
              <span>Tax ({taxLabel}):</span>
              <span>{formatCents(taxCents)}</span>
            </div>
            <div className="user-summary-row user-summary-total">
              <span>Total:</span>
              <span>{formatCents(totalCents)}</span>
            </div>
          </div>
          <button type="submit" className="user-btn user-btn-primary user-btn-block user-btn-lg" style={{ marginTop: "1.5rem" }} disabled={pending} aria-busy={pending}>
            <Icon name="card-linear" width={20} />
            {pending ? "Placing order..." : "Place Order"}
          </button>
        </aside>
      </div>
    </form>
  );
}
