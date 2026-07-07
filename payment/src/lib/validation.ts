/**
 * Pure validation / sanitisation helpers shared by the client UI and the server
 * API routes. The server ALWAYS re-validates — never trust values from the
 * browser, even though the UI validates first for a nicer experience.
 */

export interface ParsedAmount {
  ok: boolean;
  /** Normalised amount as a fixed 2dp string, e.g. "1250.00". */
  value?: string;
  error?: string;
}

/** Largest single transaction we will accept (sanity guard). */
export const MAX_AMOUNT = 1_000_000;

export function parseAmount(input: unknown): ParsedAmount {
  if (input === null || input === undefined || `${input}`.trim() === '') {
    return { ok: false, error: 'Enter an amount' };
  }
  // Strip currency symbols, thousands separators and whitespace.
  const raw = `${input}`.trim().replace(/[$,\s]/g, '');
  if (!/^\d+(\.\d{1,2})?$/.test(raw)) {
    return { ok: false, error: 'Enter a valid amount (e.g. 1250.00)' };
  }
  const num = Number(raw);
  if (!Number.isFinite(num) || num <= 0) {
    return { ok: false, error: 'Amount must be greater than zero' };
  }
  if (num > MAX_AMOUNT) {
    return { ok: false, error: 'Amount exceeds the maximum allowed' };
  }
  return { ok: true, value: num.toFixed(2) };
}

export interface ParsedReference {
  ok: boolean;
  value?: string;
  error?: string;
}

/**
 * References appear on the merchant statement, so we keep them tidy: letters,
 * numbers and a small set of safe punctuation, trimmed to 50 characters (the
 * NAB clientReferenceInformation.code limit).
 */
export function sanitizeReference(input: unknown): string {
  if (input === null || input === undefined) return '';
  return `${input}`
    .replace(/[^\w\s\-/.#:]/g, '')
    .replace(/\s+/g, ' ')
    .trim()
    .slice(0, 50);
}

export function validateReference(input: unknown): ParsedReference {
  const value = sanitizeReference(input);
  if (!value) return { ok: false, error: 'Enter a reference' };
  return { ok: true, value };
}

export function isValidEmail(input: unknown): boolean {
  if (typeof input !== 'string') return false;
  const value = input.trim();
  // Deliberately simple, permissive check — exact RFC validation is not useful here.
  return value.length <= 254 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}
