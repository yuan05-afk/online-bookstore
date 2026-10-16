import { jwtVerify, SignJWT } from "jose";

/**
 * Signed session cookie. The token only says who the person is (`sub`) and when it expires.
 * Role and name are always read from the database, so a demotion or a deleted account
 * takes effect on the next request instead of when the cookie runs out.
 */

export const SESSION_COOKIE = "nob_session";
export const SESSION_SECONDS = 60 * 60 * 2; // two hours, same as the old store

function secretKey(): Uint8Array {
  const secret = process.env.SESSION_SECRET?.trim();
  if (secret && secret.length >= 32) return new TextEncoder().encode(secret);
  if (process.env.NODE_ENV === "production") {
    throw new Error("SESSION_SECRET must be set to at least 32 characters.");
  }
  return new TextEncoder().encode("development-only-secret-do-not-use-in-production");
}

export async function signSession(userId: number, now: number = Date.now()): Promise<string> {
  return new SignJWT({})
    .setProtectedHeader({ alg: "HS256" })
    .setSubject(String(userId))
    .setIssuedAt(Math.floor(now / 1000))
    .setExpirationTime(Math.floor(now / 1000) + SESSION_SECONDS)
    .sign(secretKey());
}

/** The user id inside a valid, unexpired token, or null. */
export async function readSession(token: string | undefined): Promise<number | null> {
  if (!token) return null;
  try {
    const { payload } = await jwtVerify(token, secretKey(), { algorithms: ["HS256"] });
    const id = Number(payload.sub);
    return Number.isInteger(id) && id > 0 ? id : null;
  } catch {
    return null;
  }
}

export const sessionCookieOptions = {
  httpOnly: true,
  sameSite: "lax" as const,
  secure: process.env.NODE_ENV === "production",
  path: "/",
  maxAge: SESSION_SECONDS,
};
