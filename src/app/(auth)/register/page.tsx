import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { Icon } from "@/components/icon";
import { getCurrentUser } from "@/lib/auth";
import { RegisterForm } from "./register-form";

export const metadata: Metadata = { title: "Create Account" };

export default async function RegisterPage() {
  const user = await getCurrentUser();
  if (user) redirect(user.role === "admin" ? "/admin" : "/catalog");

  return (
    <section className="auth-section">
      <div className="auth-card auth-card-wide fade-in">
        <div className="auth-card-body">
          <div className="auth-card-header">
            <div className="auth-card-icon">
              <Icon name="users-group-rounded-linear" width={24} />
            </div>
            <h1 className="auth-card-title">Create an account</h1>
            <p className="auth-card-subtitle">Start your reading journey today.</p>
          </div>
          <RegisterForm />
        </div>
        <div className="auth-card-footer">
          Already have an account? <Link href="/login">Sign in</Link>
        </div>
      </div>
    </section>
  );
}
