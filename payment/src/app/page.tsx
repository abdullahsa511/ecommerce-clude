import Link from 'next/link';
import { BRAND, listOrganisations } from '@/config/organisations';

/**
 * Simple branded directory of the available payment entry points. Handy for
 * staff; customers normally arrive on a specific /pay, /payment or /makepayment
 * link straight from an invoice.
 */
export default function Home() {
  const orgs = listOrganisations();
  return (
    <div className="page">
      <div className="shell">
        <div className="card">
          <div className="brandRow">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img className="brandLogo" src="/krost-logo.png" alt={BRAND.merchantName} />
            <span className="secureBadge">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                <path d="M7 10V7a5 5 0 0 1 10 0v3" stroke="#857F77" strokeWidth="2" strokeLinecap="round" />
                <rect x="4.5" y="10" width="15" height="10.5" rx="2.5" fill="#857F77" />
              </svg>
              Secure checkout
            </span>
          </div>

          <div className="index-title">Payments</div>
          <div className="index-sub">Choose the entity you are paying.</div>

          {orgs.map((org) => (
            <Link key={org.slug} href={`/${org.slug}`} className="index-link">
              <span className="index-link__state">{org.label}</span>
              <span className="index-link__path">/{org.slug}</span>
            </Link>
          ))}

          <div className="footer">
            <div className="footer__powered">
              Powered by <span className="footer__nab">nab</span>
            </div>
            <div className="footer__fine">
              Payments are encrypted and processed securely.
              <br />
              Questions? {BRAND.supportEmail}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
