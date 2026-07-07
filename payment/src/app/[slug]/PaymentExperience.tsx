'use client';

import { useEffect, useRef, useState } from 'react';
import {
  detectBrand,
  formatCardNumber,
  formatExpiry,
  formatMoney,
  todayLabel,
} from '@/lib/format';
import { mountUnifiedCheckout, tokenLast4 } from '@/lib/nab/unified-checkout-client';

type Screen = 'details' | 'card' | 'confirm' | 'success' | 'failure' | 'invalid';

interface Props {
  slug: string;
  stateLabel: string;
  currency: string;
  merchantName: string;
  supportEmail: string;
  requireEmail: boolean;
  mock: boolean;
  initialAmount: string;
  initialReference: string;
  initialScreen: Screen;
}

interface FieldErrors {
  amount?: string;
  reference?: string;
  email?: string;
  cardName?: string;
  cardNumber?: string;
  expiry?: string;
  cvc?: string;
}

const UC_CONTAINER_ID = 'uc-paymentSelection';

function base64url(value: string): string {
  return btoa(unescape(encodeURIComponent(value)))
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '');
}

export default function PaymentExperience(props: Props) {
  const [screen, setScreen] = useState<Screen>(props.initialScreen);
  const [amount, setAmount] = useState(props.initialAmount);
  const [reference, setReference] = useState(props.initialReference);
  const [email, setEmail] = useState('');
  const [cardName, setCardName] = useState('');
  const [cardNumber, setCardNumber] = useState('');
  const [expiry, setExpiry] = useState('');
  const [cvc, setCvc] = useState('');
  const [errors, setErrors] = useState<FieldErrors>({});
  const [processing, setProcessing] = useState(false);
  const [token, setToken] = useState('');
  const [last4, setLast4] = useState('');
  const [txnId, setTxnId] = useState('');
  const [payError, setPayError] = useState('');
  const [ucError, setUcError] = useState('');

  const ucMounted = useRef(false);

  const brand = detectBrand(cardNumber);
  const amountLabel = formatMoney(amount);

  function clearError(field: keyof FieldErrors) {
    setErrors((prev) => ({ ...prev, [field]: undefined }));
  }

  /** Validate the non-card fields shared by both modes. */
  function validateOrderFields(): FieldErrors {
    const next: FieldErrors = {};
    if (!amount || parseFloat(amount) <= 0) next.amount = 'Enter an amount';
    if (!reference.trim()) next.reference = 'Enter a reference';
    if (props.requireEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
      next.email = 'Enter a valid email';
    return next;
  }

  // ---- MOCK flow: card fields live on the details screen ----
  async function goConfirmMock() {
    const next = validateOrderFields();
    if (!cardName.trim()) next.cardName = 'Enter the name on card';
    if (cardNumber.replace(/\s/g, '').length < 15) next.cardNumber = 'Enter a valid card number';
    if (!/^\d{2}\/\d{2}$/.test(expiry)) next.expiry = 'MM/YY';
    if (cvc.length < 3) next.cvc = 'CVC';
    if (Object.keys(next).length) {
      setErrors(next);
      return;
    }
    const pan = cardNumber.replace(/\D/g, '');
    const tail = pan.slice(-4);
    // Demo rule: a card ending in 0000 simulates an issuer decline.
    const declined = pan.endsWith('0000');
    setToken(base64url(JSON.stringify({ mock: true, last4: tail, brand: brand || 'card', declined })));
    setLast4(tail);
    setPayError('');
    setScreen('confirm');
  }

  // ---- LIVE flow: collect the card in NAB's Unified Checkout widget ----
  async function continueToCard() {
    const next = validateOrderFields();
    if (Object.keys(next).length) {
      setErrors(next);
      return;
    }
    setUcError('');
    setPayError('');
    ucMounted.current = false;
    setScreen('card');
  }

  // Mount Unified Checkout once we are on the card screen.
  useEffect(() => {
    if (screen !== 'card' || props.mock || ucMounted.current) return;
    ucMounted.current = true;
    let cancelled = false;

    (async () => {
      try {
        const res = await fetch('/api/capture-context', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ slug: props.slug, amount, reference }),
        });
        const ctx = await res.json();
        if (!res.ok || !ctx.captureContext) {
          throw new Error(ctx.error || 'Could not start a secure payment session.');
        }
        const transientToken = await mountUnifiedCheckout({
          captureContext: ctx.captureContext,
          clientLibrary: ctx.clientLibrary,
          clientLibraryIntegrity: ctx.clientLibraryIntegrity,
          containerId: UC_CONTAINER_ID,
        });
        if (cancelled) return;
        setToken(transientToken);
        setLast4(tokenLast4(transientToken));
        setScreen('confirm');
      } catch (err) {
        if (cancelled) return;
        ucMounted.current = false;
        setUcError(err instanceof Error ? err.message : 'Could not load the payment form.');
      }
    })();

    return () => {
      cancelled = true;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [screen]);

  async function pay() {
    setProcessing(true);
    setPayError('');
    try {
      const res = await fetch('/api/pay', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          slug: props.slug,
          amount,
          reference,
          email: props.requireEmail ? email : undefined,
          token,
        }),
      });
      const data = await res.json();
      if (data.ok) {
        setTxnId(data.transactionId || '');
        setScreen('success');
      } else {
        setPayError(data.error || data.message || '');
        setScreen('failure');
      }
    } catch {
      setPayError('We could not reach the payment service. No funds have been taken.');
      setScreen('failure');
    } finally {
      setProcessing(false);
    }
  }

  function backToDetails() {
    ucMounted.current = false;
    setUcError('');
    setScreen('details');
    setProcessing(false);
  }

  return (
    <div className="page">
      <div className="shell">
        <div className="card">
          {/* Header */}
          <div className="brandRow">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img className="brandLogo" src="/krost-logo.png" alt={props.merchantName} />
            <span className="secureBadge">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                <path d="M7 10V7a5 5 0 0 1 10 0v3" stroke="#857F77" strokeWidth="2" strokeLinecap="round" />
                <rect x="4.5" y="10" width="15" height="10.5" rx="2.5" fill="#857F77" />
              </svg>
              Secure checkout
            </span>
          </div>

          {/* ===== DETAILS ===== */}
          {screen === 'details' && (
            <div className="screen">
              <div className="eyebrow">Amount due</div>
              <div className="amountWrap">
                <span className="amountPrefix">$</span>
                <input
                  inputMode="decimal"
                  className="amountInput"
                  value={amount}
                  onChange={(e) => {
                    setAmount(e.target.value.replace(/[^0-9.]/g, ''));
                    clearError('amount');
                  }}
                  placeholder="0.00"
                  aria-label="Amount due"
                />
                {errors.amount && <span className="errorText" style={{ marginTop: 6, display: 'block' }}>{errors.amount}</span>}
              </div>

              <div className="fieldStack">
                <div className="field">
                  <label className="label">Reference / Invoice number</label>
                  <input
                    className="input"
                    value={reference}
                    onChange={(e) => {
                      setReference(e.target.value);
                      clearError('reference');
                    }}
                    placeholder="INV-00000"
                  />
                  {errors.reference && <span className="errorText">{errors.reference}</span>}
                </div>

                {props.requireEmail && (
                  <div className="field">
                    <label className="label">Email for receipt</label>
                    <input
                      type="email"
                      className="input"
                      value={email}
                      onChange={(e) => {
                        setEmail(e.target.value);
                        clearError('email');
                      }}
                      placeholder="you@company.com.au"
                    />
                    {errors.email && <span className="errorText">{errors.email}</span>}
                  </div>
                )}
              </div>

              {/* Card fields are only collected inline in mock/demo mode. In live
                  mode the card is captured in NAB's Unified Checkout on the next step. */}
              {props.mock && (
                <>
                  <div className="divider">
                    <div className="divider__line" />
                    <span className="divider__text">Card details</span>
                    <div className="divider__line" />
                  </div>

                  <div className="fieldStack">
                    <div className="field">
                      <label className="label">Name on card</label>
                      <input
                        className="input"
                        value={cardName}
                        onChange={(e) => {
                          setCardName(e.target.value);
                          clearError('cardName');
                        }}
                        placeholder="J. Smith"
                      />
                      {errors.cardName && <span className="errorText">{errors.cardName}</span>}
                    </div>

                    <div className="field">
                      <label className="label">Card number</label>
                      <div className="inputWrap">
                        <input
                          inputMode="numeric"
                          className="input input--card"
                          value={cardNumber}
                          onChange={(e) => {
                            setCardNumber(formatCardNumber(e.target.value));
                            clearError('cardNumber');
                          }}
                          placeholder="0000 0000 0000 0000"
                        />
                        <div className="cardMark">
                          {brand === 'visa' && <span className="brand-visa">VISA</span>}
                          {brand === 'mastercard' && (
                            <span className="brand-mc">
                              <span className="brand-mc__a" />
                              <span className="brand-mc__b" />
                            </span>
                          )}
                          {brand === 'amex' && <span className="brand-amex">AMEX</span>}
                          {brand === '' && (
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                              <rect x="2.5" y="5.5" width="19" height="13" rx="2.5" stroke="#C9C2B8" strokeWidth="1.6" />
                              <path d="M2.5 9.5h19" stroke="#C9C2B8" strokeWidth="1.6" />
                            </svg>
                          )}
                        </div>
                      </div>
                      {errors.cardNumber && <span className="errorText">{errors.cardNumber}</span>}
                    </div>

                    <div className="row">
                      <div className="field">
                        <label className="label">Expiry</label>
                        <input
                          inputMode="numeric"
                          className="input"
                          value={expiry}
                          onChange={(e) => {
                            setExpiry(formatExpiry(e.target.value));
                            clearError('expiry');
                          }}
                          placeholder="MM/YY"
                        />
                        {errors.expiry && <span className="errorText">{errors.expiry}</span>}
                      </div>
                      <div className="field">
                        <label className="label">CVC</label>
                        <input
                          inputMode="numeric"
                          className="input"
                          value={cvc}
                          onChange={(e) => {
                            setCvc(e.target.value.replace(/\D/g, '').slice(0, 4));
                            clearError('cvc');
                          }}
                          placeholder="123"
                        />
                        {errors.cvc && <span className="errorText">{errors.cvc}</span>}
                      </div>
                    </div>
                  </div>
                </>
              )}

              {payError && <div className="alert">{payError}</div>}

              <button
                className="btn-primary btn-primary--spaced"
                onClick={props.mock ? goConfirmMock : continueToCard}
              >
                {props.mock ? 'Review payment' : 'Continue to payment'}
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                  <path d="M5 12h14M13 6l6 6-6 6" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
              </button>
            </div>
          )}

          {/* ===== CARD (Unified Checkout, live mode) ===== */}
          {screen === 'card' && (
            <div className="screen">
              <div className="eyebrow">Card details</div>
              <div className="totalRow" style={{ margin: '0 2px 18px' }}>
                <span className="totalRow__label">Amount</span>
                <span className="totalRow__value" style={{ fontSize: 22 }}>${amountLabel}</span>
              </div>

              {ucError ? (
                <div className="alert" style={{ marginTop: 0 }}>{ucError}</div>
              ) : (
                <div style={{ minHeight: 90, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  <div id={UC_CONTAINER_ID} style={{ width: '100%' }} />
                </div>
              )}

              <button className="btn-ghost" onClick={backToDetails}>
                Back to edit
              </button>
            </div>
          )}

          {/* ===== CONFIRM ===== */}
          {screen === 'confirm' && (
            <div className="screen">
              <div className="heading" style={{ fontSize: 21 }}>Review your payment</div>
              <div className="subheading">Please confirm the details below before paying.</div>

              <div className="panel">
                <div className="panel__row">
                  <span className="panel__label">Reference</span>
                  <span className="panel__value">{reference || '—'}</span>
                </div>
                {props.requireEmail && (
                  <div className="panel__row">
                    <span className="panel__label">Receipt to</span>
                    <span className="panel__value">{email}</span>
                  </div>
                )}
                <div className="panel__row">
                  <span className="panel__label">Card</span>
                  <span className="panel__value">•••• {last4 || '0000'}</span>
                </div>
              </div>

              <div className="totalRow">
                <span className="totalRow__label">Total</span>
                <span className="totalRow__value">${amountLabel}</span>
              </div>
              <div className="totalNote">{props.currency} · No surcharge applied</div>

              <button className="btn-primary" onClick={pay} disabled={processing}>
                {processing ? (
                  <>
                    <span className="spinner" /> Processing…
                  </>
                ) : (
                  <span>Pay ${amountLabel}</span>
                )}
              </button>
              <button className="btn-ghost" onClick={backToDetails}>
                Back to edit
              </button>
            </div>
          )}

          {/* ===== SUCCESS ===== */}
          {screen === 'success' && (
            <div className="screen screen--center">
              <div className="statusIcon statusIcon--success">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                  <path d="M5 13l4 4L19 7" stroke="#1C7C54" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
              </div>
              <div className="heading">Payment successful</div>
              <div className="subheading">
                {props.requireEmail
                  ? 'A receipt has been sent to your email.'
                  : 'Thank you — your payment is complete.'}
              </div>

              <div className="panel panel--left">
                <div className="panel__row">
                  <span className="panel__label">Amount paid</span>
                  <span className="panel__value panel__value--strong">${amountLabel}</span>
                </div>
                <div className="panel__row">
                  <span className="panel__label">Reference</span>
                  <span className="panel__value">{reference || '—'}</span>
                </div>
                <div className="panel__row">
                  <span className="panel__label">Transaction ID</span>
                  <span className="panel__value">{txnId}</span>
                </div>
                <div className="panel__row">
                  <span className="panel__label">Date</span>
                  <span className="panel__value">{todayLabel()}</span>
                </div>
              </div>

              <button className="btn-outline" onClick={() => window.print()}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" stroke="#16130F" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
                Download receipt
              </button>
            </div>
          )}

          {/* ===== FAILURE ===== */}
          {screen === 'failure' && (
            <div className="screen screen--center">
              <div className="statusIcon statusIcon--error">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                  <path d="M7 7l10 10M17 7L7 17" stroke="#C13B2E" strokeWidth="2.4" strokeLinecap="round" />
                </svg>
              </div>
              <div className="heading">Payment declined</div>
              <div className="subheading subheading--narrow">
                {payError ||
                  'Your card issuer declined this transaction. No funds have been taken. Please check your details or try another card.'}
              </div>

              <div className="panel panel--left" style={{ marginBottom: 22 }}>
                <div className="panel__row">
                  <span className="panel__label">Reference</span>
                  <span className="panel__value">{reference || '—'}</span>
                </div>
                <div className="panel__row">
                  <span className="panel__label">Amount</span>
                  <span className="panel__value">${amountLabel}</span>
                </div>
              </div>

              <button className="btn-primary" onClick={backToDetails}>
                Try again
              </button>
            </div>
          )}

          {/* ===== INVALID ===== */}
          {screen === 'invalid' && (
            <div className="screen screen--center">
              <div className="statusIcon statusIcon--neutral">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="9" stroke="#A39C92" strokeWidth="2" />
                  <path d="M12 7.5v5" stroke="#A39C92" strokeWidth="2" strokeLinecap="round" />
                  <circle cx="12" cy="16.3" r="1.2" fill="#A39C92" />
                </svg>
              </div>
              <div className="heading">This payment link is no longer valid</div>
              <div className="subheading subheading--narrow">
                The link may have expired or already been paid. Please use the link on your most
                recent invoice or statement, or contact us for a new one.
              </div>
              <a className="btn-outline" href={`mailto:${props.supportEmail}`}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="5" width="18" height="14" rx="2.5" stroke="#16130F" strokeWidth="1.8" />
                  <path d="M4 7l8 6 8-6" stroke="#16130F" strokeWidth="1.8" strokeLinecap="round" />
                </svg>
                Contact {props.supportEmail}
              </a>
            </div>
          )}

          {/* Footer / trust */}
          <div className="footer">
            <div className="footer__marks">
              <span className="footer__visa">VISA</span>
              <span className="footer__mc">
                <span className="footer__mc-a" />
                <span className="footer__mc-b" />
              </span>
              <span className="footer__amex">AMEX</span>
              <span className="footer__sep" />
              <span className="footer__powered">
                Powered by <span className="footer__nab">nab</span>
              </span>
            </div>
            <div className="footer__fine">
              Payments are encrypted and processed securely.
              <br />
              Questions? {props.supportEmail}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
