import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { Suspense } from "react";
import { Icon } from "@/components/icon";
import { NoticeBanner } from "@/components/notice-banner";
import { getCurrentUser } from "@/lib/auth";
import { LoginForm } from "./login-form";

export const metadata: Metadata = { title: "Sign In" };

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ next?: string }> }) {
  const user = await getCurrentUser();
  if (user) redirect(user.role === "admin" ? "/admin" : "/catalog");
  const { next } = await searchParams;

  return (
    <section className="auth-section">
      <div className="auth-card fade-in">
        <div className="auth-card-body">
          <div className="auth-card-header">
            <div className="auth-card-icon">
              <Icon name="user-circle-linear" width={24} />
            </div>
            <h1 className="auth-card-title">Welcome back</h1>
            <p className="auth-card-subtitle">Please enter your details to sign in.</p>
          </div>

          <Suspense>
            <NoticeBanner variant="auth" />
          </Suspense>

          <div className="auth-alert auth-alert-info">
            <p>Demo account:</p>
            <div>
              <p>
                <strong>Customer:</strong> user@bookstore.com / user123
              </p>
            </div>
          </div>

          <LoginForm next={next} />
        </div>
        <div className="auth-card-footer">
          Don&apos;t have an account? <Link href="/register">Create an account</Link>
        </div>
      </div>
    </section>
  );
}
