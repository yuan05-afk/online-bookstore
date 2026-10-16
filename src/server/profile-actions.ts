"use server";

import { revalidatePath } from "next/cache";
import { and, eq, ne } from "drizzle-orm";
import { getDb } from "@/db";
import { users } from "@/db/schema";
import { getCurrentUser, hashPassword, verifyPassword } from "@/lib/auth";
import type { FormState } from "@/lib/form-state";
import { addressSchema, fieldErrors, formText, isUniqueViolation, passwordChangeSchema, profileSchema } from "@/lib/validation";

/** Profile actions work for any signed-in person, customer or admin. */
async function signedIn() {
  const user = await getCurrentUser();
  if (!user) throw new Error("Not signed in");
  return user;
}

export async function updateProfileAction(_previous: FormState, data: FormData): Promise<FormState> {
  const user = await signedIn();
  const values = {
    firstName: formText(data, "firstName"),
    lastName: formText(data, "lastName"),
    email: formText(data, "email"),
    phone: formText(data, "phone"),
  };
  const parsed = profileSchema.safeParse(values);
  if (!parsed.success) return { errors: Object.values(fieldErrors(parsed.error)), values };

  const db = getDb();
  const [taken] = await db.select({ id: users.id }).from(users).where(and(eq(users.email, parsed.data.email), ne(users.id, user.id))).limit(1);
  if (taken) return { errors: ["That email is already used by another account."], values };

  try {
    await db.update(users).set({ ...parsed.data, updatedAt: new Date() }).where(eq(users.id, user.id));
  } catch (error) {
    if (isUniqueViolation(error)) return { errors: ["That email is already used by another account."], values };
    throw error;
  }
  revalidatePath("/", "layout");
  return { errors: [], values: parsed.data, message: "Personal information updated." };
}

export async function updateAddressAction(_previous: FormState, data: FormData): Promise<FormState> {
  const user = await signedIn();
  const values = {
    address: formText(data, "address"),
    city: formText(data, "city"),
    state: formText(data, "state"),
    zipCode: formText(data, "zipCode"),
    country: formText(data, "country"),
  };
  const parsed = addressSchema.safeParse(values);
  if (!parsed.success) return { errors: Object.values(fieldErrors(parsed.error)), values };
  await getDb().update(users).set({ ...parsed.data, updatedAt: new Date() }).where(eq(users.id, user.id));
  revalidatePath("/", "layout");
  return { errors: [], values: parsed.data, message: "Address updated." };
}

export async function changePasswordAction(_previous: FormState, data: FormData): Promise<FormState> {
  const user = await signedIn();
  const parsed = passwordChangeSchema.safeParse({
    currentPassword: formText(data, "currentPassword"),
    newPassword: formText(data, "newPassword"),
    confirmPassword: formText(data, "confirmPassword"),
  });
  if (!parsed.success) return { errors: Object.values(fieldErrors(parsed.error)), values: {} };

  const db = getDb();
  const [row] = await db.select({ passwordHash: users.passwordHash }).from(users).where(eq(users.id, user.id)).limit(1);
  if (!row || !(await verifyPassword(parsed.data.currentPassword, row.passwordHash))) {
    return { errors: ["Your current password is incorrect."], values: {} };
  }
  await db.update(users).set({ passwordHash: await hashPassword(parsed.data.newPassword), updatedAt: new Date() }).where(eq(users.id, user.id));
  return { errors: [], values: {}, message: "Password changed." };
}
