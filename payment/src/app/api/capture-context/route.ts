import { NextRequest, NextResponse } from 'next/server';
import { getOrganisation } from '@/config/organisations';
import { createCaptureContext } from '@/lib/nab/client';
import { parseAmount, validateReference } from '@/lib/validation';

// Card-capture sessions must run on the Node.js runtime (uses crypto + env).
export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

/**
 * POST /api/capture-context
 * Body: { slug, amount, reference }
 * Returns a NAB capture context the browser uses to tokenise the card.
 */
export async function POST(req: NextRequest) {
  let payload: any;
  try {
    payload = await req.json();
  } catch {
    return NextResponse.json({ error: 'Invalid request.' }, { status: 400 });
  }

  const org = getOrganisation(String(payload?.slug ?? ''));
  if (!org) {
    return NextResponse.json({ error: 'Unknown payment page.' }, { status: 404 });
  }

  const amount = parseAmount(payload?.amount);
  if (!amount.ok) {
    return NextResponse.json({ error: amount.error }, { status: 400 });
  }
  const reference = validateReference(payload?.reference);
  if (!reference.ok) {
    return NextResponse.json({ error: reference.error }, { status: 400 });
  }

  try {
    const result = await createCaptureContext(org, {
      amount: amount.value!,
      reference: reference.value!,
      currency: org.currency,
      targetOrigin: req.nextUrl.origin,
    });
    return NextResponse.json(result);
  } catch (err) {
    console.error('capture-context failed:', err);
    return NextResponse.json(
      { error: 'Unable to start a secure payment session. Please try again.' },
      { status: 502 },
    );
  }
}
