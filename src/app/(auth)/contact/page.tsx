import type { Metadata } from "next";
import Link from "next/link";
import { Icon } from "@/components/icon";

export const metadata: Metadata = { title: "Contact Us" };

export default function ContactPage() {
  return (
    <section className="auth-section">
      <div className="auth-card fade-in">
        <div className="auth-card-body">
          <div className="auth-card-header">
            <div className="auth-card-icon">
              <Icon name="chat-line-linear" width={24} />
            </div>
            <h1 className="auth-card-title">Get in Touch</h1>
            <p className="auth-card-subtitle">We&apos;d love to hear from you.</p>
          </div>
          <div className="auth-content-text" style={{ color: "var(--auth-zinc-600)", lineHeight: 1.6, fontSize: "0.9375rem", textAlign: "center" }}>
            <p style={{ marginBottom: "2rem" }}>
              Have questions about our bookstore or need help with an order? Reach out to customer support. This is a demo store,
              so these details are placeholders.
            </p>
            <div
              style={{
                background: "var(--auth-zinc-50)",
                padding: "1.5rem",
                borderRadius: "0.75rem",
                border: "1px solid var(--auth-zinc-200)",
                marginBottom: "2rem",
              }}
            >
              <div style={{ marginBottom: "1rem" }}>
                <Icon name="letter-linear" width={20} style={{ color: "var(--auth-zinc-900)", display: "block", margin: "0 auto 0.5rem" }} />
                <strong style={{ display: "block", color: "var(--auth-zinc-900)" }}>Email Support</strong>
                <span>support@bookstore.example</span>
              </div>
              <div>
                <Icon name="phone-linear" width={20} style={{ color: "var(--auth-zinc-900)", display: "block", margin: "1rem auto 0.5rem" }} />
                <strong style={{ display: "block", color: "var(--auth-zinc-900)" }}>Phone Support</strong>
                <span>(555) 123-4567</span>
              </div>
            </div>
            <div style={{ paddingTop: "1rem" }}>
              <Link href="/login" className="auth-btn-primary">
                Back to Sign In
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
