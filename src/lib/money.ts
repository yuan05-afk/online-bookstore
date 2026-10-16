/** Tax rate for the whole store (8.5%). Change it here only. */
export const TAX_RATE = 0.085;

/** "$12.50" from 1250 cents. */
export function formatCents(cents: number): string {
  const negative = cents < 0;
  const abs = Math.abs(Math.round(cents));
  const dollars = Math.floor(abs / 100)
    .toString()
    .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return `${negative ? "-" : ""}$${dollars}.${String(abs % 100).padStart(2, "0")}`;
}

/** Tax on a subtotal in cents, rounded to the nearest cent. */
export function taxCents(subtotalCents: number): number {
  return Math.round(subtotalCents * TAX_RATE);
}

export function totals(lines: readonly { priceCents: number; quantity: number }[]) {
  const subtotalCents = lines.reduce((sum, line) => sum + line.priceCents * line.quantity, 0);
  const tax = taxCents(subtotalCents);
  return { subtotalCents, taxCents: tax, totalCents: subtotalCents + tax };
}

/**
 * Parses what a person types into a price field ("12", "12.5", "$1,299.99") into cents.
 * Returns null for anything that is not a positive amount with at most two decimals.
 */
export function parseDollarsToCents(input: string): number | null {
  const cleaned = input.trim().replace(/^\$/, "").replace(/,/g, "");
  if (!/^\d+(\.\d{1,2})?$/.test(cleaned)) return null;
  const [whole, fraction = ""] = cleaned.split(".");
  const cents = Number(whole) * 100 + Number(fraction.padEnd(2, "0"));
  return cents > 0 ? cents : null;
}

/** "12.50" for an editable field. */
export function centsToInput(cents: number): string {
  return (cents / 100).toFixed(2);
}
