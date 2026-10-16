import type { Metadata } from "next";
import { eq } from "drizzle-orm";
import { getDb } from "@/db";
import { users } from "@/db/schema";
import { requireCustomer } from "@/lib/auth";
import { ProfileForms } from "./profile-forms";

export const metadata: Metadata = { title: "My Profile" };

export default async function ProfilePage() {
  const user = await requireCustomer("/profile");
  const [row] = await getDb().select().from(users).where(eq(users.id, user.id)).limit(1);

  return (
    <>
      <h1 className="user-page-title">My Profile</h1>
      <ProfileForms
        account={{
          firstName: row.firstName,
          lastName: row.lastName,
          email: row.email,
          phone: row.phone ?? "",
          address: row.address ?? "",
          city: row.city ?? "",
          state: row.state ?? "",
          zipCode: row.zipCode ?? "",
          country: row.country ?? "USA",
        }}
      />
    </>
  );
}
