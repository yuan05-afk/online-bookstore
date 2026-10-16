import assert from "node:assert/strict";
import test from "node:test";
import { APPROVE_TEST_CARD, cardType, DECLINE_TEST_CARD, processPayment, validateCardNumber, validateCvv, validateExpiry } from "./payment";

const NOW = new Date("2026-10-16T00:00:00Z");

test("Luhn accepts real test numbers and rejects typos", () => {
  assert.equal(validateCardNumber(APPROVE_TEST_CARD), true);
  assert.equal(validateCardNumber("4532 0151 1283 0366"), true);
  assert.equal(validateCardNumber("4532015112830367"), false);
  assert.equal(validateCardNumber("1234"), false);
  assert.equal(validateCardNumber("abcd efgh ijkl mnop"), false);
});

test("expiry must be this month or later", () => {
  assert.equal(validateExpiry("10", "2026", NOW), true);
  assert.equal(validateExpiry("09", "2026", NOW), false);
  assert.equal(validateExpiry("01", "2027", NOW), true);
  assert.equal(validateExpiry("13", "2030", NOW), false);
  assert.equal(validateExpiry("12", "29", NOW), true);
  assert.equal(validateExpiry("", "", NOW), false);
});

test("CVV is three or four digits", () => {
  assert.equal(validateCvv("123"), true);
  assert.equal(validateCvv("1234"), true);
  assert.equal(validateCvv("12"), false);
  assert.equal(validateCvv("12a"), false);
});

test("card brands", () => {
  assert.equal(cardType("4111111111111111"), "Visa");
  assert.equal(cardType("5555555555554444"), "Mastercard");
  assert.equal(cardType("378282246310005"), "American Express");
  assert.equal(cardType("6011111111111117"), "Discover");
});

test("a valid card is approved and a decline is deterministic, not random", () => {
  const ok = processPayment({ cardNumber: APPROVE_TEST_CARD, expiryMonth: "12", expiryYear: "2030", cvv: "123" }, NOW);
  assert.equal(ok.ok, true);
  if (ok.ok) {
    assert.match(ok.transactionId, /^TXN-/);
    assert.equal(ok.method, "Visa ending in 0366");
  }
  for (let i = 0; i < 20; i += 1) {
    const declined = processPayment({ cardNumber: DECLINE_TEST_CARD, expiryMonth: "12", expiryYear: "2030", cvv: "123" }, NOW);
    assert.equal(declined.ok, false);
  }
});

test("bad details are explained in words", () => {
  assert.deepEqual(processPayment({ cardNumber: "1234", expiryMonth: "12", expiryYear: "2030", cvv: "123" }, NOW), { ok: false, message: "That card number is not valid." });
  const expired = processPayment({ cardNumber: APPROVE_TEST_CARD, expiryMonth: "01", expiryYear: "2020", cvv: "123" }, NOW);
  assert.equal(expired.ok, false);
  const cvv = processPayment({ cardNumber: APPROVE_TEST_CARD, expiryMonth: "12", expiryYear: "2030", cvv: "1" }, NOW);
  assert.equal(cvv.ok, false);
});
