import { randomInt } from "node:crypto";

/**
 * Mock card payment. Nothing is charged and nothing leaves the server.
 * It checks the card the way a real form would (Luhn, expiry, CVV) so the checkout feels real.
 *
 * The old PHP version declined 10% of valid cards at random, which made the demo feel broken.
 * Here a decline is deterministic: the test card 4000 0000 0000 0002 is always declined, and
 * every other valid card is approved.
 */

export const DECLINE_TEST_CARD = "4000000000000002";
export const APPROVE_TEST_CARD = "4532015112830366";

export function digitsOnly(value: string): string {
  return value.replace(/\D/g, "");
}

export function validateCardNumber(raw: string): boolean {
  const number = digitsOnly(raw);
  if (!/^\d{13,19}$/.test(number)) return false;
  let sum = 0;
  let double = false;
  for (let i = number.length - 1; i >= 0; i -= 1) {
    let digit = Number(number[i]);
    if (double) {
      digit *= 2;
      if (digit > 9) digit -= 9;
    }
    sum += digit;
    double = !double;
  }
  return sum % 10 === 0;
}

export function validateExpiry(month: string, year: string, now: Date = new Date()): boolean {
  const m = Number(month);
  let y = Number(year);
  if (!Number.isInteger(m) || !Number.isInteger(y) || m < 1 || m > 12) return false;
  if (y < 100) y += 2000;
  const currentYear = now.getFullYear();
  const currentMonth = now.getMonth() + 1;
  return y > currentYear || (y === currentYear && m >= currentMonth);
}

export function validateCvv(cvv: string): boolean {
  return /^\d{3,4}$/.test(cvv.trim());
}

export function cardType(raw: string): string {
  const number = digitsOnly(raw);
  if (/^4/.test(number)) return "Visa";
  if (/^5[1-5]/.test(number)) return "Mastercard";
  if (/^3[47]/.test(number)) return "American Express";
  if (/^6(?:011|5)/.test(number)) return "Discover";
  return "Card";
}

export type PaymentInput = { cardNumber: string; expiryMonth: string; expiryYear: string; cvv: string };

export type PaymentResult =
  | { ok: true; transactionId: string; method: string }
  | { ok: false; message: string };

function transactionId(): string {
  return `TXN-${Date.now().toString(36).toUpperCase()}-${randomInt(10000, 99999)}`;
}

export function processPayment(input: PaymentInput, now: Date = new Date()): PaymentResult {
  const number = digitsOnly(input.cardNumber);
  if (!validateCardNumber(number)) return { ok: false, message: "That card number is not valid." };
  if (!validateExpiry(input.expiryMonth, input.expiryYear, now)) return { ok: false, message: "That card has expired or the date is not valid." };
  if (!validateCvv(input.cvv)) return { ok: false, message: "The CVV must be 3 or 4 digits." };
  if (number === DECLINE_TEST_CARD) {
    return { ok: false, message: "Payment declined. Try a different card." };
  }
  return { ok: true, transactionId: transactionId(), method: `${cardType(number)} ending in ${number.slice(-4)}` };
}

export function orderNumber(): string {
  return `ORD-${Date.now().toString(36).toUpperCase()}-${randomInt(1000, 9999)}`;
}
