import { config as loadEnv } from "dotenv";
import { defineConfig } from "drizzle-kit";

loadEnv({ path: ".env.local" });
loadEnv({ path: ".env" });

/**
 * Migrations use the direct Neon connection (no `-pooler` host).
 * The localhost URL is only a placeholder so `db:generate` can load this file before a
 * database exists. `db:generate` does not connect. `db:migrate` needs a real URL.
 */
const url = process.env.DATABASE_URL_UNPOOLED ?? process.env.DATABASE_URL ?? "postgresql://user:password@localhost:5432/bookstore";

export default defineConfig({
  schema: "./src/db/schema.ts",
  out: "./drizzle",
  dialect: "postgresql",
  dbCredentials: { url },
});
