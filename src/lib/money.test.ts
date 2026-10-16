import assert from "node:assert/strict";
import test from "node:test";
import { centsToInput, formatCents, parseDollarsToCents, taxCents, totals } from "./money";

test("cents format as dollars with thousands separators", () => {
  assert.equal(formatCents(0), "$0.00");
  assert.equal(formatCents(5), "$0.05");
  assert.equal(formatCents(1999), "$19.99");
  assert.equal(formatCents(123456789), "$1,234,567.89");
  assert.equal(formatCents(-250), "-$2.50");
});

test("tax is 8.5% rounded to the nearest cent", () => {
  assert.equal(taxCents(1000), 85);
  assert.equal(taxCents(1599), 136); // 135.915 -> 136, the same as the old PHP round()
  assert.equal(taxCents(1), 0);
});

test("totals add up in whole cents", () => {
  const result = totals([
    { priceCents: 1595, quantity: 4 },
    { priceCents: 799, quantity: 1 },
  ]);
  assert.equal(result.subtotalCents, 7179);
  assert.equal(result.taxCents, 610);
  assert.equal(result.totalCents, 7789);
});

test("a typed price becomes cents, or is refused", () => {
  assert.equal(parseDollarsToCents("12"), 1200);
  assert.equal(parseDollarsToCents("12.5"), 1250);
  assert.equal(parseDollarsToCents("$1,299.99"), 129999);
  assert.equal(parseDollarsToCents("0"), null);
  assert.equal(parseDollarsToCents("-3"), null);
  assert.equal(parseDollarsToCents("1.234"), null);
  assert.equal(parseDollarsToCents("abc"), null);
  assert.equal(parseDollarsToCents(""), null);
  assert.equal(centsToInput(1250), "12.50");
});
