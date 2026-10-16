/**
 * One-line banners shown after a redirect. The URL carries a short code (`?notice=book_added`),
 * never the text, so nobody can put their own words on the page by editing a link.
 */

export type NoticeType = "success" | "error" | "info";

export const NOTICES = {
  account_created: { type: "success", text: "Account created. Please sign in." },
  signed_out: { type: "info", text: "You have been signed out." },
  login_required: { type: "error", text: "Please sign in to continue." },
  session_expired: { type: "error", text: "Your session has expired. Please sign in again." },
  admin_only: { type: "error", text: "Access denied. Administrator privileges are required." },
  book_not_found: { type: "error", text: "That book could not be found." },
  order_not_found: { type: "error", text: "That order could not be found." },
  account_not_found: { type: "error", text: "That account could not be found." },
  cart_empty: { type: "error", text: "Your cart is empty." },
  profile_saved: { type: "success", text: "Personal information updated." },
  address_saved: { type: "success", text: "Address updated." },
  password_saved: { type: "success", text: "Password changed." },
  book_added: { type: "success", text: "Book added." },
  book_updated: { type: "success", text: "Book updated." },
  book_deleted: { type: "success", text: "Book deleted." },
  book_in_orders: { type: "error", text: "That book is part of an order, so it cannot be deleted. Set its stock to 0 instead." },
  order_updated: { type: "success", text: "Order updated." },
  account_promoted: { type: "success", text: "User promoted to administrator." },
  account_deleted: { type: "success", text: "User account deleted." },
  account_self: { type: "error", text: "You cannot change your own account here." },
  admin_profile_saved: { type: "success", text: "Profile updated." },
} as const satisfies Record<string, { type: NoticeType; text: string }>;

export type NoticeCode = keyof typeof NOTICES;

export function resolveNotice(code: string | string[] | undefined): { type: NoticeType; text: string } | null {
  const value = Array.isArray(code) ? code[0] : code;
  if (!value || !Object.hasOwn(NOTICES, value)) return null;
  return NOTICES[value as NoticeCode];
}

/** Adds `?notice=code` (or `&notice=code`) to a path. */
export function withNotice(path: string, code: NoticeCode): string {
  return `${path}${path.includes("?") ? "&" : "?"}notice=${code}`;
}
