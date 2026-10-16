import type { Metadata } from "next";
import { Inter } from "next/font/google";
import { Suspense } from "react";
import { AdminNav } from "@/components/admin-nav";
import { NoticeBanner } from "@/components/notice-banner";
import { requireAdmin } from "@/lib/auth";
import { SITE_NAME, siteUrl } from "@/lib/site";
import "@/styles/admin.css";

const inter = Inter({ subsets: ["latin"], display: "swap", variable: "--font-inter" });

export const metadata: Metadata = {
  metadataBase: siteUrl(),
  title: { default: `Admin - ${SITE_NAME}`, template: `%s - Admin - ${SITE_NAME}` },
  robots: { index: false, follow: false },
};

export default async function AdminLayout({ children }: { children: React.ReactNode }) {
  await requireAdmin();

  return (
    <html lang="en" className={inter.variable}>
      <body className="admin-panel">
        <AdminNav />
        <main className="admin-content" id="content">
          <div className="admin-content-wrapper">
            <Suspense>
              <NoticeBanner variant="admin" />
            </Suspense>
            {children}
          </div>
        </main>
      </body>
    </html>
  );
}
