/**
 * Client-side display/formatting helpers (no Node APIs — safe in the browser).
 */

export type CardBrand = 'visa' | 'mastercard' | 'amex' | '';

/** Format a numeric-ish value as AUD with 2 decimals and thousands separators. */
export function formatMoney(value: string | number): string {
  const num = parseFloat(String(value).replace(/[^0-9.]/g, ''));
  if (Number.isNaN(num)) return '0.00';
  return num.toLocaleString('en-AU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

/** Detect the card brand from a (possibly spaced) PAN, for the inline brand mark. */
export function detectBrand(cardNumber: string): CardBrand {
  const n = (cardNumber || '').replace(/\D/g, '');
  if (/^4/.test(n)) return 'visa';
  if (/^(5[1-5]|2[2-7])/.test(n)) return 'mastercard';
  if (/^3[47]/.test(n)) return 'amex';
  return '';
}

/** Group digits into blocks of four for the card-number field. */
export function formatCardNumber(value: string): string {
  return value
    .replace(/\D/g, '')
    .slice(0, 16)
    .replace(/(.{4})/g, '$1 ')
    .trim();
}

/** Turn raw digits into MM/YY. */
export function formatExpiry(value: string): string {
  const v = value.replace(/\D/g, '').slice(0, 4);
  if (v.length > 2) return v.slice(0, 2) + '/' + v.slice(2);
  return v;
}

/** Friendly long date, e.g. "18 June 2026". */
export function todayLabel(): string {
  return new Date().toLocaleDateString('en-AU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}
