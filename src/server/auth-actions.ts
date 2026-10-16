"use server";

import { redirect } from "next/navigation";
import { eq } from "drizzle-orm";
import { getDb } from "@/db";
import { users } from "@/db/schema";
import { endSession, hashPassword, safeNext, startSession, verifyPassword } from "@/lib/auth";
import type { FormState } from "@/lib/form-state";
import { withNotice } from "@/lib/notices";
import { fieldErrors, formText, isUniqueViolation, loginSchema, registerSchema } from "@/lib/validation";

type AuthFormState = FormState;

// A real hash of nothing in particular, so a wrong email costs the same time as a wrong password.
let dummyHash: Promise<string> | undefined;
function dummy(): Promise<string> {
  dummyHash ??= hashPassword("not-a-real-password");
  return dummyHash;
}

export async function loginAction(_previous: AuthFormState, data: FormData): Promise<AuthFormState> {
  const values = { email: formText(data, "email") };
  const parsed = loginSchema.safeParse({ email: values.email, password: formText(data, "password") });
  if (!parsed.success) {
    return { errors: Object.values(fieldErrors(parsed.error)), values };
  }

  let user;
  try {
    [user] = await getDb().select().from(users).where(eq(users.email, parsed.data.email)).limit(1);
  } catch (error) {
    console.error("login lookup failed", error);
    return { errors: ["Something went wrong. Please try again in a moment."], values };
  }

  const ok = await verifyPassword(parsed.data.password, user?.passwordHash ?? (await dummy()));
  if (!user || !ok) return { errors: ["Invalid email or password."], values };

  await startSession(user.id);
  redirect(user.role === "admin" ? "/admin" : safeNext(formText(data, "next"), "/catalog"));
}

export async function registerAction(_previous: AuthFormState, data: FormData): Promise<AuthFormState> {
  const raw = {
    firstName: formText(data, "firstName"),
    lastName: formText(data, "lastName"),
    email: formText(data, "email"),
    phone: formText(data, "phone"),
    address: formText(data, "address"),
    city: formText(data, "city"),
    state: formText(data, "state"),
    zipCode: formText(data, "zipCode"),
    password: formText(data, "password"),
    confirmPassword: formText(data, "confirmPassword"),
  };
  const values = { ...raw, password: "", confirmPassword: "" };
  const parsed = registerSchema.safeParse(raw);
  if (!parsed.success) return { errors: Object.values(fieldErrors(parsed.error)), values };

  const { password, confirmPassword: _confirm, ...profile } = parsed.data;
  void _confirm;
  const db = getDb();
  try {
    const [existing] = await db.select({ id: users.id }).from(users).where(eq(users.email, profile.email)).limit(1);
    if (existing) return { errors: ["An account with this email already exists."], values };
    await db.insert(users).values({ ...profile, passwordHash: await hashPassword(password), role: "user" });
  } catch (error) {
    // Two people registering the same address at once: the unique index catches the second.
    if (isUniqueViolation(error)) {
      return { errors: ["An account with this email already exists."], values };
    }
    console.error("registration failed", error);
    return { errors: ["Something went wrong while creating your account. Please try again."], values };
  }

  redirect(withNotice("/login", "account_created"));
}

export async function logoutAction() {
  await endSession();
  redirect(withNotice("/login", "signed_out"));
}
