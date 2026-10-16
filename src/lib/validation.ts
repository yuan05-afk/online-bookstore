import { z } from "zod";

/** Shared input rules. Messages are written to be shown to a customer as they are. */

export const MIN_PASSWORD_LENGTH = 8;

const text = (label: string, max: number) =>
  z
    .string()
    .trim()
    .min(1, `${label} is required.`)
    .max(max, `${label} must be ${max} characters or fewer.`);

const optionalText = (label: string, max: number) =>
  z
    .string()
    .trim()
    .max(max, `${label} must be ${max} characters or fewer.`)
    .default("");

export const emailField = z
  .string()
  .trim()
  .toLowerCase()
  .min(1, "Email is required.")
  .max(255, "Email is too long.")
  .pipe(z.email("Please enter a valid email address."));

export const passwordField = z
  .string()
  .min(MIN_PASSWORD_LENGTH, `Password must be at least ${MIN_PASSWORD_LENGTH} characters long.`)
  .max(72, "Password must be 72 characters or fewer.");

export const loginSchema = z.object({
  email: emailField,
  password: z.string().min(1, "Password is required."),
});

export const registerSchema = z
  .object({
    firstName: text("First name", 100),
    lastName: text("Last name", 100),
    email: emailField,
    phone: optionalText("Phone", 30),
    address: optionalText("Address", 255),
    city: optionalText("City", 100),
    state: optionalText("State", 100),
    zipCode: optionalText("ZIP code", 20),
    password: passwordField,
    confirmPassword: z.string(),
  })
  .refine((value) => value.password === value.confirmPassword, {
    message: "Passwords do not match.",
    path: ["confirmPassword"],
  });

export const profileSchema = z.object({
  firstName: text("First name", 100),
  lastName: text("Last name", 100),
  email: emailField,
  phone: optionalText("Phone", 30),
});

export const addressSchema = z.object({
  address: optionalText("Address", 255),
  city: optionalText("City", 100),
  state: optionalText("State", 100),
  zipCode: optionalText("ZIP code", 20),
  country: optionalText("Country", 100),
});

export const passwordChangeSchema = z
  .object({
    currentPassword: z.string().min(1, "Current password is required."),
    newPassword: passwordField,
    confirmPassword: z.string(),
  })
  .refine((value) => value.newPassword === value.confirmPassword, {
    message: "New passwords do not match.",
    path: ["confirmPassword"],
  });

export const shippingSchema = z.object({
  firstName: text("First name", 100),
  lastName: text("Last name", 100),
  address: text("Street address", 255),
  city: text("City", 100),
  state: text("State", 100),
  zipCode: text("ZIP code", 20),
  country: text("Country", 100),
  phone: text("Phone number", 30),
});

export const paymentSchema = z.object({
  cardNumber: z.string().min(1, "Card number is required."),
  expiryMonth: z.string().min(1, "Choose the expiry month."),
  expiryYear: z.string().min(1, "Choose the expiry year."),
  cvv: z.string().min(1, "CVV is required."),
});

/** ISBN-10 or ISBN-13 with or without dashes and spaces. Checks the length only, like the old store. */
export function normalizeIsbn(value: string): string | null {
  const compact = value.replace(/[-\s]/g, "");
  return /^\d{10}(\d{3})?$/.test(compact) || /^\d{9}[\dXx]$/.test(compact) ? compact.toUpperCase() : null;
}

export const bookSchema = z.object({
  isbn: z
    .string()
    .trim()
    .transform((value, ctx) => {
      const isbn = normalizeIsbn(value);
      if (!isbn) {
        ctx.addIssue({ code: "custom", message: "Enter a 10 or 13 digit ISBN." });
        return z.NEVER;
      }
      return isbn;
    }),
  title: text("Title", 255),
  author: text("Author", 255),
  price: z.string().trim().min(1, "Price is required."),
  stock: z.coerce.number().int("Stock must be a whole number.").min(0, "Stock cannot be negative.").max(100000, "Stock is too large."),
  categoryId: z.coerce.number().int().positive("Choose a category."),
  description: optionalText("Description", 5000),
  coverImage: z
    .string()
    .trim()
    .max(500, "The image link is too long.")
    .refine((value) => value === "" || /^https?:\/\//i.test(value), "The image link must start with http:// or https://")
    .default(""),
});

export function fieldErrors(error: z.ZodError): Record<string, string> {
  const result: Record<string, string> = {};
  for (const issue of error.issues) {
    const key = String(issue.path[0] ?? "form");
    if (!result[key]) result[key] = issue.message;
  }
  return result;
}

/** Plain text for a form field read from FormData. */
export function formText(data: FormData, name: string): string {
  const value = data.get(name);
  return typeof value === "string" ? value : "";
}

/** Postgres unique violation (23505). Drizzle wraps the driver error, so look through `cause` too. */
export function isUniqueViolation(error: unknown): boolean {
  const seen = new Set<unknown>();
  let current: unknown = error;
  while (current && typeof current === "object" && !seen.has(current)) {
    seen.add(current);
    if ((current as { code?: unknown }).code === "23505") return true;
    current = (current as { cause?: unknown }).cause;
  }
  return false;
}
