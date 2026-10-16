/** `%`, `_` and `\` are wildcards in LIKE, so a search for "100%" must not match everything. */
export function likePattern(term: string): string {
  return `%${term.replace(/[\\%_]/g, (char) => `\\${char}`)}%`;
}

/** First integer in a query-string value, or the fallback. Ignores arrays beyond their first entry. */
export function toInt(value: string | string[] | undefined, fallback = 0): number {
  const raw = Array.isArray(value) ? value[0] : value;
  const parsed = Number.parseInt(raw ?? "", 10);
  return Number.isFinite(parsed) ? parsed : fallback;
}
