import type { Metadata } from "next";
import { LegalPage } from "@/components/legal-page";

export const metadata: Metadata = { title: "Terms of Service" };

const SECTIONS = [
  {
    title: "Introduction",
    body: "Welcome to Night Owl Books. By accessing our website and using our services, you agree to be bound by these Terms of Service. Please read them carefully.",
  },
  {
    title: "User Accounts",
    body: "To access certain features, you must create an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.",
  },
  {
    title: "Book Purchases",
    body: "All purchases are subject to availability. Prices are subject to change without notice. We reserve the right to refuse or cancel any order for any reason. Payments on this demo store are simulated and nothing is charged.",
  },
  {
    title: "Intellectual Property",
    body: "The content on this website, including text, graphics, logos, and book covers, is protected by copyright and other intellectual property laws.",
  },
  {
    title: "Limitation of Liability",
    body: "We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our services.",
  },
] as const;

export default function TermsPage() {
  return <LegalPage icon="document-text-linear" title="Terms of Service" subtitle="Please read these terms before using the store." sections={SECTIONS} />;
}
