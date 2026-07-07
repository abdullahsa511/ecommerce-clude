/**
 * NAB Gateway client (server-only).
 *
 * Provides two operations used by the API routes:
 *   - createCaptureContext: starts a secure card-capture session (Flex Microform
 *     / Unified Checkout). The browser uses the returned context to tokenise the
 *     card so the raw PAN never reaches our server.
 *   - processPayment: authorises + captures a payment using the transient token.
 *
 * When credentials are absent or NAB_MOCK=true, both operations are simulated so
 * the whole app runs end-to-end with no NAB account. Swap in real credentials to
 * go live — no code changes required.
 */

import type { OrganisationConfig } from '@/config/organisations';
import { buildAuthHeaders } from './signature';

const CAPTURE_CONTEXT_PATH = '/up/v1/capture-contexts';
const PAYMENTS_PATH = '/pts/v2/payments';

export interface NabCredentials {
  organisationId: string;
  keyId: string;
  sharedSecret: string;
}

export interface ResolvedMode {
  mock: boolean;
  host: string;
  credentials: NabCredentials;
}

/** True when NAB_ENVIRONMENT selects the live/production gateway. */
function isLiveEnvironment(): boolean {
  const env = (process.env.NAB_ENVIRONMENT ?? 'test').toLowerCase();
  return env === 'live' || env === 'production';
}

/** completeMandate enables UC auto-processing; production/live or sandbox auto flag. */
function usesCompleteMandate(): boolean {
  if (isLiveEnvironment()) {
    return true;
  }
  return (process.env.NAB_UC_SANDBOX_AUTO_PROCESSING ?? 'false').toLowerCase() === 'true';
}

function captureContextHasCompleteMandate(jwt: string): boolean {
  const payload = decodeJwtPayload(jwt);
  if (!payload || Object.keys(payload).length === 0) return false;
  if (payload.completeMandate && typeof payload.completeMandate === 'object') return true;
  const ctx = payload.ctx;
  if (!Array.isArray(ctx)) return false;
  for (const entry of ctx) {
    if (!entry || typeof entry !== 'object') continue;
    if (entry.completeMandate && typeof entry.completeMandate === 'object') return true;
    const data = entry.data;
    if (data && typeof data === 'object' && data.completeMandate && typeof data.completeMandate === 'object') {
      return true;
    }
  }
  return false;
}

/** Read the active environment's API host. */
function apiHost(): string {
  return isLiveEnvironment()
    ? 'nabgateway-api.nab.com.au'
    : 'nabgateway-api-test.nab.com.au';
}

/** Read per-organisation credentials from the environment. */
function readCredentials(org: OrganisationConfig): NabCredentials {
  return {
    organisationId: process.env[`${org.envPrefix}_ORG_ID`] ?? '',
    keyId: process.env[`${org.envPrefix}_KEY_ID`] ?? '',
    sharedSecret: process.env[`${org.envPrefix}_SHARED_SECRET`] ?? '',
  };
}

/**
 * Decide whether to call NAB for real or run in mock mode.
 *
 * - `NAB_MOCK=true`  -> always mock (handy for demos/local testing).
 * - `NAB_MOCK=false` -> always call NAB for real.
 * - unset            -> auto-mock ONLY outside the live environment. In the live
 *   environment we never silently mock: if credentials are missing the real call
 *   is attempted and fails loudly, so production can never fake a "successful"
 *   payment that never actually charged the customer.
 */
export function resolveMode(org: OrganisationConfig): ResolvedMode {
  const credentials = readCredentials(org);
  const explicit = process.env.NAB_MOCK?.toLowerCase();
  const credsComplete = Boolean(
    credentials.organisationId && credentials.keyId && credentials.sharedSecret,
  );

  let mock: boolean;
  if (explicit === 'true') mock = true;
  else if (explicit === 'false') mock = false;
  else mock = !isLiveEnvironment() && !credsComplete;

  return { mock, host: apiHost(), credentials };
}

function base64url(value: string): string {
  return Buffer.from(value, 'utf8').toString('base64url');
}

function decodeJwtPayload(jwt: string): Record<string, unknown> {
  try {
    const payload = jwt.split('.')[1];
    if (!payload) return {};
    return JSON.parse(Buffer.from(payload, 'base64url').toString('utf8'));
  } catch {
    return {};
  }
}

export interface CaptureContextResult {
  mock: boolean;
  /** The capture context JWT to hand to the front-end SDK. */
  captureContext: string;
  /** Whether completeMandate was included (production only). */
  completeMandate: boolean;
  /** URL of the NAB JS library to load (live mode only). */
  clientLibrary?: string;
  /** Subresource-integrity hash for that library (live mode only). */
  clientLibraryIntegrity?: string;
}

export interface CapturePaymentInput {
  amount: string;
  reference: string;
  currency: string;
  targetOrigin: string;
}

/**
 * Create a capture context. In live mode this calls NAB; the JWT it returns
 * carries one-time keys plus the URL of the JS library used to tokenise the
 * card in the browser.
 */
export async function createCaptureContext(
  org: OrganisationConfig,
  input: CapturePaymentInput,
): Promise<CaptureContextResult> {
  const mode = resolveMode(org);

  if (mode.mock) {
    const captureContext = [
      base64url(JSON.stringify({ alg: 'none', typ: 'JWT' })),
      base64url(
        JSON.stringify({
          mock: true,
          iat: Math.floor(Date.now() / 1000),
          data: {
            currency: input.currency,
            amount: input.amount,
            reference: input.reference,
          },
        }),
      ),
      '',
    ].join('.');
    return { mock: true, captureContext, completeMandate: false };
  }

  const payload: Record<string, unknown> = {
    targetOrigins: [input.targetOrigin],
    clientVersion: '0.24',
    allowedCardNetworks: ['VISA', 'MASTERCARD', 'AMEX'],
    allowedPaymentTypes: ['PANENTRY'],
    country: 'AU',
    locale: 'en_AU',
    captureMandate: {
      billingType: 'NONE',
      requestEmail: false,
      requestPhone: false,
      requestShipping: false,
      showAcceptedNetworkIcons: true,
      showConfirmationStep: false,
    },
    orderInformation: {
      amountDetails: { totalAmount: input.amount, currency: input.currency },
    },
  };

  if (usesCompleteMandate()) {
    payload.completeMandate = { type: 'CAPTURE' };
  }

  const body = JSON.stringify(payload);

  const jwt = await nabPost(mode, CAPTURE_CONTEXT_PATH, body, 'text');
  const decoded = decodeJwtPayload(jwt);
  const data = (decoded?.ctx as Array<{ data?: Record<string, string> }>)?.[0]?.data ?? {};
  return {
    mock: false,
    captureContext: jwt,
    completeMandate: captureContextHasCompleteMandate(jwt),
    clientLibrary: data.clientLibrary,
    clientLibraryIntegrity: data.clientLibraryIntegrity,
  };
}

export interface ProcessPaymentInput {
  amount: string;
  reference: string;
  currency: string;
  email?: string;
  /** Transient token representing the card (from the browser SDK or mock). */
  token: string;
}

export interface PaymentResult {
  approved: boolean;
  status: string;
  transactionId: string;
  message: string;
  last4?: string;
  brand?: string;
}

interface MockToken {
  mock: true;
  last4?: string;
  brand?: string;
  declined?: boolean;
}

function readMockToken(token: string): MockToken | null {
  try {
    const parsed = JSON.parse(Buffer.from(token, 'base64url').toString('utf8'));
    return parsed && parsed.mock ? (parsed as MockToken) : null;
  } catch {
    return null;
  }
}

/**
 * Authorise + capture a payment. In mock mode the outcome is taken from the
 * (client-built) mock token so test "decline" cards behave predictably.
 */
export async function processPayment(
  org: OrganisationConfig,
  input: ProcessPaymentInput,
): Promise<PaymentResult> {
  const mode = resolveMode(org);

  if (mode.mock) {
    const mockToken = readMockToken(input.token);
    const transactionId = 'NAB' + Date.now().toString().slice(-8);
    if (!mockToken) {
      return {
        approved: false,
        status: 'INVALID_REQUEST',
        transactionId,
        message: 'Card details could not be read. Please try again.',
      };
    }
    if (mockToken.declined) {
      return {
        approved: false,
        status: 'DECLINED',
        transactionId,
        message: 'Your card issuer declined this transaction.',
        last4: mockToken.last4,
        brand: mockToken.brand,
      };
    }
    return {
      approved: true,
      status: 'AUTHORIZED',
      transactionId,
      message: 'Approved',
      last4: mockToken.last4,
      brand: mockToken.brand,
    };
  }

  const body = JSON.stringify({
    clientReferenceInformation: { code: input.reference },
    processingInformation: { commerceIndicator: 'internet' },
    orderInformation: {
      amountDetails: { totalAmount: input.amount, currency: input.currency },
      ...(input.email ? { billTo: { email: input.email } } : {}),
    },
    tokenInformation: { transientTokenJwt: input.token },
  });

  const authResponse = await nabPost(mode, PAYMENTS_PATH, body, 'json');
  const authStatus = String(authResponse?.status ?? 'UNKNOWN');
  const transactionId = String(authResponse?.id ?? '');

  if (authStatus !== 'AUTHORIZED' && authStatus !== 'PARTIAL_AUTHORIZED') {
    return {
      approved: false,
      status: authStatus,
      transactionId,
      message: 'Your card issuer declined this transaction.',
    };
  }

  const captureBody = JSON.stringify({
    clientReferenceInformation: { code: input.reference },
    orderInformation: {
      amountDetails: { totalAmount: input.amount, currency: input.currency },
    },
  });

  const captureResponse = await nabPost(
    mode,
    `${PAYMENTS_PATH}/${encodeURIComponent(transactionId)}/captures`,
    captureBody,
    'json',
  );
  const captureStatus = String(captureResponse?.status ?? authStatus);
  const approved =
    captureStatus === 'PENDING' ||
    captureStatus === 'SETTLED' ||
    captureStatus === 'TRANSMITTED' ||
    captureStatus === 'AUTHORIZED' ||
    authStatus === 'AUTHORIZED' ||
    authStatus === 'PARTIAL_AUTHORIZED';

  return {
    approved,
    status: captureStatus,
    transactionId,
    message: approved ? 'Approved' : 'Your card issuer declined this transaction.',
  };
}

/** Perform a signed POST to NAB and return parsed JSON or raw text. */
async function nabPost(
  mode: ResolvedMode,
  resource: string,
  body: string,
  expect: 'json' | 'text',
): Promise<any> {
  const headers = buildAuthHeaders({
    method: 'post',
    resource,
    host: mode.host,
    organisationId: mode.credentials.organisationId,
    keyId: mode.credentials.keyId,
    sharedSecret: mode.credentials.sharedSecret,
    body,
  });

  const res = await fetch(`https://${mode.host}${resource}`, {
    method: 'POST',
    headers,
    body,
  });

  const text = await res.text();
  if (!res.ok) {
    // Never log the request body (it may contain payment data).
    throw new Error(`NAB ${resource} responded ${res.status}: ${text.slice(0, 300)}`);
  }
  if (expect === 'text') return text;
  try {
    return JSON.parse(text);
  } catch {
    throw new Error(`NAB ${resource} returned a non-JSON response.`);
  }
}
