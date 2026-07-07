/**
 * NAB Gateway HTTP Signature authentication.
 *
 * NAB Gateway is built on the CyberSource platform, which authenticates REST
 * requests with an HTTP Signature: an HMAC-SHA256 of a small set of headers,
 * keyed by your *base64-decoded* shared secret.
 *
 * Signing string (POST):   host date (request-target) digest v-c-merchant-id
 * Signing string (GET):    host date (request-target) v-c-merchant-id
 *
 *   signature = base64( HMAC-SHA256( signingString, base64decode(sharedSecret) ) )
 *
 * Docs: https://developer.cybersource.com (HTTP Signature Authentication)
 *
 * This module is server-only — it relies on Node's crypto and must never be
 * imported into a client component.
 */

import crypto from 'crypto';

export interface SignatureInput {
  method: 'get' | 'post';
  /** Request path including any query string, e.g. "/up/v1/capture-contexts". */
  resource: string;
  host: string;
  /** The NAB Organisation ID (a.k.a. merchant id). */
  organisationId: string;
  keyId: string;
  /** Base64 shared secret issued by NAB. */
  sharedSecret: string;
  /** RFC 1123 GMT date, must match the request's Date header exactly. */
  date: string;
  /** Digest header value ("SHA-256=...") — required for POST/PUT. */
  digest?: string;
}

/** Build the `Digest` header value for a request body. */
export function buildDigest(body: string): string {
  const hash = crypto.createHash('sha256').update(body, 'utf8').digest('base64');
  return `SHA-256=${hash}`;
}

/** Build the value for the `Signature` header. */
export function buildSignatureHeader(input: SignatureInput): string {
  const signedHeaders =
    input.method === 'post'
      ? 'host date (request-target) digest v-c-merchant-id'
      : 'host date (request-target) v-c-merchant-id';

  const parts: string[] = [
    `host: ${input.host}`,
    `date: ${input.date}`,
    `(request-target): ${input.method} ${input.resource}`,
  ];
  if (input.method === 'post') {
    if (!input.digest) {
      throw new Error('A digest is required to sign a POST request.');
    }
    parts.push(`digest: ${input.digest}`);
  }
  parts.push(`v-c-merchant-id: ${input.organisationId}`);

  const signingString = parts.join('\n');
  const key = Buffer.from(input.sharedSecret, 'base64');
  const signature = crypto
    .createHmac('sha256', key)
    .update(signingString, 'utf8')
    .digest('base64');

  return [
    `keyid="${input.keyId}"`,
    `algorithm="HmacSHA256"`,
    `headers="${signedHeaders}"`,
    `signature="${signature}"`,
  ].join(', ');
}

/**
 * Assemble the complete set of headers for an authenticated NAB request.
 */
export function buildAuthHeaders(params: {
  method: 'get' | 'post';
  resource: string;
  host: string;
  organisationId: string;
  keyId: string;
  sharedSecret: string;
  body?: string;
}): Record<string, string> {
  const date = new Date().toUTCString();
  const headers: Record<string, string> = {
    'v-c-merchant-id': params.organisationId,
    Date: date,
    Host: params.host,
  };

  let digest: string | undefined;
  if (params.method === 'post') {
    digest = buildDigest(params.body ?? '');
    headers['Digest'] = digest;
    headers['Content-Type'] = 'application/json';
  }
  headers['Accept'] = 'application/json';

  headers['Signature'] = buildSignatureHeader({
    method: params.method,
    resource: params.resource,
    host: params.host,
    organisationId: params.organisationId,
    keyId: params.keyId,
    sharedSecret: params.sharedSecret,
    date,
    digest,
  });

  return headers;
}
