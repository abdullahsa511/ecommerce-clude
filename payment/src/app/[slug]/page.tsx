import type { Metadata } from 'next';
import { BRAND, getOrganisation } from '@/config/organisations';
import { resolveMode } from '@/lib/nab/client';
import { parseAmount, sanitizeReference } from '@/lib/validation';
import PaymentExperience from './PaymentExperience';

type SearchParams = Promise<Record<string, string | string[] | undefined>>;
type Params = Promise<{ slug: string }>;

function firstValue(value: string | string[] | undefined): string {
  return Array.isArray(value) ? (value[0] ?? '') : (value ?? '');
}

export async function generateMetadata({ params }: { params: Params }): Promise<Metadata> {
  const { slug } = await params;
  const org = getOrganisation(slug);
  const title = org
    ? `Pay ${BRAND.merchantName} (${org.stateCode}) — Secure checkout`
    : `${BRAND.merchantName} — Secure payment`;
  return { title, robots: { index: false, follow: false } };
}

export default async function PaymentPage({
  params,
  searchParams,
}: {
  params: Params;
  searchParams: SearchParams;
}) {
  const { slug } = await params;
  const org = getOrganisation(slug);

  // Unknown slug -> show the branded "link no longer valid" screen.
  if (!org) {
    return (
      <PaymentExperience
        slug={slug}
        stateLabel=""
        currency="AUD"
        merchantName={BRAND.merchantName}
        supportEmail={BRAND.supportEmail}
        requireEmail={BRAND.requireEmail}
        mock
        initialAmount=""
        initialReference=""
        initialScreen="invalid"
      />
    );
  }

  const sp = await searchParams;
  const amountParam = parseAmount(firstValue(sp.amount));
  const prefillAmount = amountParam.ok ? amountParam.value! : '';
  const prefillReference = sanitizeReference(firstValue(sp.reference) || firstValue(sp.ref));

  const { mock } = resolveMode(org);

  return (
    <PaymentExperience
      slug={org.slug}
      stateLabel={org.label}
      currency={org.currency}
      merchantName={BRAND.merchantName}
      supportEmail={BRAND.supportEmail}
      requireEmail={BRAND.requireEmail}
      mock={mock}
      initialAmount={prefillAmount}
      initialReference={prefillReference}
      initialScreen="details"
    />
  );
}
