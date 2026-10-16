import assert from "node:assert/strict";
import test from "node:test";
import bcrypt from "bcryptjs";
import { SignJWT } from "jose";
import { readSession, signSession } from "./session";
import { normalizeIsbn, registerSchema } from "./validation";

process.env.SESSION_SECRET = "a-test-secret-that-is-at-least-32-characters-long";

test("passwords hashed by the old PHP store still verify", async () => {
  // Taken from the original database dump: password_hash() output ($2y$).
  assert.equal(await bcrypt.compare("admin123", "$2y$12$x0WRDYb8rDAJU5uHUlaeDu.cTLuPfivogkPTFZKOs3WXq5xKGk062"), true);
  assert.equal(await bcrypt.compare("user123", "$2y$12$FcJDfAw.k0wr1lsU25yTwO4nVPYZtNaav0YKpu18j93X4MpSW5c4O"), true);
  assert.equal(await bcrypt.compare("wrong", "$2y$12$FcJDfAw.k0wr1lsU25yTwO4nVPYZtNaav0YKpu18j93X4MpSW5c4O"), false);
});

test("a session token round-trips to the user id", async () => {
  const token = await signSession(42);
  assert.equal(await readSession(token), 42);
});

test("a tampered, expired or missing token is refused", async () => {
  const token = await signSession(7);
  assert.equal(await readSession(token + "x"), null);
  assert.equal(await readSession(undefined), null);
  assert.equal(await readSession("not.a.token"), null);

  const old = await signSession(7, Date.now() - 3 * 60 * 60 * 1000);
  assert.equal(await readSession(old), null);

  const forged = await new SignJWT({}).setProtectedHeader({ alg: "HS256" }).setSubject("1").setExpirationTime("2h").sign(new TextEncoder().encode("some-other-secret-that-is-also-long-enough"));
  assert.equal(await readSession(forged), null);
});

test("registration needs matching passwords of at least 8 characters and a real email", () => {
  const base = { firstName: "Ana", lastName: "Cruz", email: "ANA@Example.com ", password: "longenough", confirmPassword: "longenough" };
  const ok = registerSchema.safeParse(base);
  assert.equal(ok.success, true);
  if (ok.success) assert.equal(ok.data.email, "ana@example.com");
  assert.equal(registerSchema.safeParse({ ...base, password: "short", confirmPassword: "short" }).success, false);
  assert.equal(registerSchema.safeParse({ ...base, confirmPassword: "different1" }).success, false);
  assert.equal(registerSchema.safeParse({ ...base, email: "nope" }).success, false);
  assert.equal(registerSchema.safeParse({ ...base, firstName: "  " }).success, false);
});

test("ISBNs accept dashes and spaces but not letters", () => {
  assert.equal(normalizeIsbn("978-0-515-12344-3"), "9780515123443");
  assert.equal(normalizeIsbn("0 306 40615 2"), "0306406152");
  assert.equal(normalizeIsbn("080442957x"), "080442957X");
  assert.equal(normalizeIsbn("12345"), null);
  assert.equal(normalizeIsbn("abcdefghij"), null);
});
