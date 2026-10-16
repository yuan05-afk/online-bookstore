import type { Metadata } from "next";
import { LegalPage } from "@/components/legal-page";

export const metadata: Metadata = { title: "Privacy Policy" };

const SECTIONS = [
  {
    title: "Information Collection",
    body: "We collect information you provide directly to us, such as when you create an account, make a purchase, or communicate with us.",
  },
  {
    title: "Use of Information",
    body: "We use your information to provide, maintain, and improve our services, process transactions, and send you related information.",
  },
  {
    title: "Use of Cookies",
    body: "We use a single, strictly necessary cookie to keep you signed in. We do not use advertising or tracking cookies.",
  },
  {
    title: "Data Security",
    body: "We implement reasonable security measures to protect your personal information. Passwords are stored as salted bcrypt hashes.",
  },
  {
    title: "Third-Party Services",
    body: "Our website may contain links to third-party websites. We are not responsible for the privacy practices of those third parties.",
  },
] as const;

export default function PrivacyPage() {
  return <LegalPage icon="shield-check-linear" title="Privacy Policy" subtitle="How we handle your information." sections={SECTIONS} />;
}
