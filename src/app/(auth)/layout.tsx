import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { Inter } from "next/font/google";
import { AuthActions } from "@/components/auth-actions";
import { Icon } from "@/components/icon";
import { SITE_NAME, siteUrl } from "@/lib/site";
import "@/styles/auth.css";

const inter = Inter({ subsets: ["latin"], display: "swap", variable: "--font-inter" });

export const metadata: Metadata = {
  metadataBase: siteUrl(),
  title: { default: `Sign in - ${SITE_NAME}`, template: `%s - ${SITE_NAME}` },
  description: `${SITE_NAME}: an online bookstore. Sign in or create an account to browse the catalog and order books.`,
};

export default function AuthLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={inter.variable}>
      <body className="auth-page">
        <header className="auth-header">
          <div className="auth-header-container">
            <Link href="/" className="auth-logo">
              <div className="auth-logo-icon">
                <Icon name="book-2-linear" width={18} />
              </div>
              <span className="auth-logo-text">{SITE_NAME}</span>
            </Link>
            <div className="auth-center-logo">
              <Image src="/images/logo-long.png" alt={SITE_NAME} width={160} height={40} className="auth-brand-logo" priority />
            </div>
            <AuthActions />
          </div>
        </header>

        <main className="auth-main">
          {children}
        </main>

        <footer className="auth-footer">
          <div className="auth-footer-container">
            <p>
              &copy; {new Date().getFullYear()} {SITE_NAME}. All rights reserved.
            </p>
            <div className="auth-footer-links">
              <Link href="/terms">Terms of Service</Link>
              <Link href="/privacy">Privacy Policy</Link>
              <Link href="/contact">Contact</Link>
            </div>
          </div>
        </footer>
      </body>
    </html>
  );
}
