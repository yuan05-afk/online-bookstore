import Link from "next/link";
import { Icon } from "@/components/icon";

type Section = { title: string; body: string };

/** The Terms and Privacy pages share one layout: numbered sections inside the auth card. */
export function LegalPage({ icon, title, subtitle, sections }: { icon: string; title: string; subtitle: string; sections: readonly Section[] }) {
  return (
    <section className="auth-section">
      <div className="auth-card auth-card-wide fade-in">
        <div className="auth-card-body">
          <div className="auth-card-header">
            <div className="auth-card-icon">
              <Icon name={icon} width={24} />
            </div>
            <h1 className="auth-card-title">{title}</h1>
            <p className="auth-card-subtitle">{subtitle}</p>
          </div>
          <div className="auth-content-text" style={{ color: "var(--auth-zinc-600)", lineHeight: 1.6, fontSize: "0.9375rem" }}>
            {sections.map((section, index) => (
              <div key={section.title}>
                <h2 style={{ color: "var(--auth-zinc-900)", fontWeight: 600, fontSize: "1rem", marginBottom: "0.75rem" }}>
                  {index + 1}. {section.title}
                </h2>
                <p style={{ marginBottom: index === sections.length - 1 ? "2rem" : "1.5rem" }}>{section.body}</p>
              </div>
            ))}
            <div style={{ paddingTop: "1rem", borderTop: "1px solid var(--auth-zinc-200)" }}>
              <Link href="/login" className="auth-btn-primary" style={{ display: "inline-flex", width: "auto", padding: "0.5rem 1.5rem" }}>
                Back to Sign In
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
