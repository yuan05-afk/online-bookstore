import type { Metadata } from "next";
import { Inter } from "next/font/google";
import Link from "next/link";
import { Suspense } from "react";
import { Icon } from "@/components/icon";
import { NoticeBanner } from "@/components/notice-banner";
import { UserMenu } from "@/components/user-menu";
import { requireCustomer } from "@/lib/auth";
import { SITE_NAME, SITE_TAGLINE, siteUrl } from "@/lib/site";
import { cartCount } from "@/server/queries";
import "@/styles/user.css";

const inter = Inter({ subsets: ["latin"], display: "swap", variable: "--font-inter" });

export const metadata: Metadata = {
  metadataBase: siteUrl(),
  title: { default: SITE_NAME, template: `%s - ${SITE_NAME}` },
  description: SITE_TAGLINE,
  robots: { index: false, follow: false },
};

export default async function ShopLayout({ children }: { children: React.ReactNode }) {
  const user = await requireCustomer();
  const count = await cartCount(user.id);

  return (
    <html lang="en" className={inter.variable}>
      <body className="user-page">
        <header className="user-header">
          <div className="user-header-container">
            <Link href="/catalog" className="user-logo">
              <div className="user-logo-icon">
                <Icon name="book-2-linear" width={18} />
              </div>
              <span className="user-logo-text">{SITE_NAME}</span>
            </Link>

            <nav className="user-nav" aria-label="Main">
              <Link href="/catalog">Browse Books</Link>
              <Link href="/orders">My Orders</Link>
            </nav>

            <div className="user-header-actions">
              <Link href="/cart" className="user-cart-link" aria-label={count > 0 ? `Cart, ${count} items` : "Cart"}>
                <div className="user-cart-icon-wrapper">
                  <Icon name="cart-large-linear" width={20} />
                  {count > 0 ? (
                    <span className="user-cart-badge" id="cart-count">
                      {count}
                    </span>
                  ) : null}
                </div>
                <span className="user-cart-label">Cart</span>
              </Link>
              <UserMenu firstName={user.firstName} />
            </div>
          </div>
        </header>

        <main className="user-main" id="content">
          <div className="user-container">
            <Suspense>
              <NoticeBanner />
            </Suspense>
            {children}
          </div>
        </main>

        <footer className="user-footer">
          <div className="user-footer-content">
            <div className="user-footer-section">
              <div className="user-footer-logo">
                <Icon name="book-bold" width={24} />
                <span>{SITE_NAME}</span>
              </div>
              <p className="user-footer-tagline">{SITE_TAGLINE}</p>
            </div>

            <div className="user-footer-section">
              <h4>Quick Links</h4>
              <ul className="user-footer-links">
                <li>
                  <Link href="/catalog">
                    <Icon name="book-linear" width={16} />
                    Browse Books
                  </Link>
                </li>
                <li>
                  <Link href="/orders">
                    <Icon name="box-linear" width={16} />
                    My Orders
                  </Link>
                </li>
              </ul>
            </div>

            <div className="user-footer-section">
              <h4>Contact</h4>
              <ul className="user-footer-contact">
                <li>
                  <Icon name="letter-linear" width={16} />
                  <a href="mailto:support@bookstore.example">support@bookstore.example</a>
                </li>
                <li>
                  <Icon name="phone-linear" width={16} />
                  <span>(555) 123-4567</span>
                </li>
              </ul>
            </div>
          </div>

          <div className="user-footer-bottom">
            <p>
              &copy; {new Date().getFullYear()} {SITE_NAME}. All rights reserved. Demo store: payments are simulated.
            </p>
          </div>
        </footer>
      </body>
    </html>
  );
}
