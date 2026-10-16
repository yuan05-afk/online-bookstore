import { attachDatabasePool } from "@vercel/functions";
import { drizzle } from "drizzle-orm/node-postgres";
import { Pool } from "pg";
import * as schema from "@/db/schema";

type Database = ReturnType<typeof drizzle<typeof schema>>;

let pool: Pool | undefined;
let database: Database | undefined;

/**
 * Runtime client for Vercel Fluid.
 * Uses the pooled Neon connection string (`DATABASE_URL`, hostname contains `-pooler`).
 * Drizzle Kit migrations use the direct string (`DATABASE_URL_UNPOOLED`) instead.
 *
 * The pool is created on first use so `next build` does not open a connection.
 */
export function isDatabaseConfigured(): boolean {
  return Boolean(process.env.DATABASE_URL?.trim());
}

export function getDb(): Database {
  const connectionString = process.env.DATABASE_URL?.trim();
  if (!connectionString) {
    throw new Error(
      "DATABASE_URL is not set. Copy .env.example to .env.local and add the Neon pooled connection string.",
    );
  }

  if (!database || !pool) {
    pool = new Pool({
      connectionString,
      max: 10,
    });
    attachDatabasePool(pool);
    database = drizzle(pool, { schema });
  }

  return database;
}
