import type { Metadata } from 'next';
import { BRAND } from '@/config/organisations';
import './globals.css';

export const metadata: Metadata = {
  title: `${BRAND.merchantName} — Secure payment`,
  description: `Pay ${BRAND.merchantName} securely by credit card. Powered by NAB.`,
  robots: { index: false, follow: false },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en-AU">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link
          href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap"
          rel="stylesheet"
        />
      </head>
      <body>{children}</body>
    </html>
  );
}
