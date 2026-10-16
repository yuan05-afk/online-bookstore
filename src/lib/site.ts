export const SITE_NAME = "Night Owl Books";
export const SITE_TAGLINE = "Your trusted online bookstore for all your reading needs.";

/** Absolute origin for metadata. */
export function siteUrl(): URL {
  const raw = process.env.NEXT_PUBLIC_SITE_URL?.trim() || process.env.SITE_URL?.trim() || "http://localhost:3000";
  try {
    return new URL(raw);
  } catch {
    return new URL("http://localhost:3000");
  }
}
