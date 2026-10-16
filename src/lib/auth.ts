import "server-only";
import { cache } from "react";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import bcrypt from "bcryptjs";
import { eq } from "drizzle-orm";
import { getDb } from "@/db";
import { users, type User } from "@/db/schema";
import { withNotice } from "@/lib/notices";
import { readSession, SESSION_COOKIE, sessionCookieOptions, signSession } from "@/lib/session";

export type SessionUser = Pick<User, "id" | "email" | "firstName" | "lastName" | "role">;

const BCRYPT_COST = 12;

export function hashPassword(password: string): Promise<string> {
  return bcrypt.hash(password, BCRYPT_COST);
}

/** Works with hashes made by the old PHP store (`$2y$`) as well as new ones. */
export function verifyPassword(password: string, hash: string): Promise<boolean> {
  return bcrypt.compare(password, hash);
}

export async function startSession(userId: number) {
  const store = await cookies();
  store.set(SESSION_COOKIE, await signSession(userId), sessionCookieOptions);
}

export async function endSession() {
  const store = await cookies();
  store.delete(SESSION_COOKIE);
}

/** The signed-in person for this request, read fresh from the database. */
export const getCurrentUser = cache(async (): Promise<SessionUser | null> => {
  const store = await cookies();
  const id = await readSession(store.get(SESSION_COOKIE)?.value);
  if (!id) return null;
  const [row] = await getDb()
    .select({ id: users.id, email: users.email, firstName: users.firstName, lastName: users.lastName, role: users.role })
    .from(users)
    .where(eq(users.id, id))
    .limit(1);
  return row ?? null;
});

/** For pages in the shop: a signed-in customer. Admins are sent to their own panel. */
export async function requireCustomer(next?: string): Promise<SessionUser> {
  const user = await getCurrentUser();
  if (!user) redirect(withNotice(`/login${next ? `?next=${encodeURIComponent(next)}` : ""}`, "login_required"));
  if (user.role === "admin") redirect("/admin");
  return user;
}

export async function requireAdmin(): Promise<SessionUser> {
  const user = await getCurrentUser();
  if (!user) redirect(withNotice("/login", "login_required"));
  if (user.role !== "admin") redirect(withNotice("/catalog", "admin_only"));
  return user;
}

/** Where a person lands after signing in. Only same-site paths are honoured. */
export function safeNext(next: string | null | undefined, fallback: string): string {
  if (!next || !next.startsWith("/") || next.startsWith("//") || next.startsWith("/\\")) return fallback;
  return next;
}
