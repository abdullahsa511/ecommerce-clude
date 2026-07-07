import { NextRequest, NextResponse } from 'next/server';
import { BRAND, getOrganisation } from '@/config/organisations';
import { processPayment } from '@/lib/nab/client';
import { isValidEmail, parseAmount, validateReference } from '@/lib/validation';

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

/**
 * POST /api/pay
 * Body: { slug, amount, reference, email?, token }
 *
 * `token` is a transient token representing the card (never the raw PAN). The
 * server re-validates every field before charging.
 */
export async function POST(req: NextRequest) {
  let payload: any;
  try {
    payload = await req.json();
  } catch {
    return NextResponse.json({ ok: false, error: 'Invalid request.' }, { status: 400 });
  }

  const org = getOrganisation(String(payload?.slug ?? ''));
  if (!org) {
    return NextResponse.json({ ok: false, error: 'Unknown payment page.' }, { status: 404 });
  }

  const amount = parseAmount(payload?.amount);
  if (!amount.ok) {
    return NextResponse.json({ ok: false, error: amount.error }, { status: 400 });
  }
  const reference = validateReference(payload?.reference);
  if (!reference.ok) {
    return NextResponse.json({ ok: false, error: reference.error }, { status: 400 });
  }

  const email = typeof payload?.email === 'string' ? payload.email.trim() : '';
  if (BRAND.requireEmail && !isValidEmail(email)) {
    return NextResponse.json({ ok: false, error: 'Enter a valid email.' }, { status: 400 });
  }

  const token = typeof payload?.token === 'string' ? payload.token : '';
  if (!token) {
    return NextResponse.json({ ok: false, error: 'Missing card details.' }, { status: 400 });
  }

  try {
    const result = await processPayment(org, {
      amount: amount.value!,
      reference: reference.value!,
      currency: org.currency,
      email: email || undefined,
      token,
    });

    return NextResponse.json({
      ok: result.approved,
      status: result.status,
      transactionId: result.transactionId,
      message: result.message,
      amount: amount.value,
      reference: reference.value,
      currency: org.currency,
      last4: result.last4,
    });
  } catch (err) {
    console.error('payment failed:', err);
    return NextResponse.json(
      { ok: false, error: 'We could not process your payment. No funds have been taken.' },
      { status: 502 },
    );
  }
}
