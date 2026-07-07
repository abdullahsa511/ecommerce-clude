/**
 * NAB Gateway Unified Checkout — browser integration.
 *
 * Unified Checkout is NAB's embedded card-capture widget (built on CyberSource).
 * The flow:
 *   1. The server creates a capture context (`/api/capture-context`). That JWT
 *      carries one-time keys, the amount, and the URL of NAB's JS library.
 *   2. We load that library, initialise `Accept(captureContext)`, and mount the
 *      payment widget into a container element.
 *   3. The customer enters their card inside NAB's secure iframe — the raw PAN
 *      never touches our page or our server.
 *   4. The widget returns a *transient token* (a JWT) representing the card,
 *      which we send to `/api/pay` to authorise + capture.
 *
 * Docs: https://developer.cybersource.com (Unified Checkout)
 */

declare global {
  interface Window {
    Accept?: (captureContext: string) => Promise<UnifiedAccept>;
  }
}

interface UnifiedAccept {
  unifiedPayments: (showPaymentScreen?: boolean) => Promise<UnifiedPayments>;
}

interface UnifiedPayments {
  show: (options: {
    containers: { paymentSelection: string; paymentScreen?: string };
  }) => Promise<string>;
}

export interface MountOptions {
  captureContext: string;
  clientLibrary: string;
  clientLibraryIntegrity?: string;
  /** DOM id of the element the payment buttons render into. */
  containerId: string;
}

/** Inject NAB's Unified Checkout script once. */
function loadLibrary(src: string, integrity?: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const existing = document.querySelector<HTMLScriptElement>('script[data-nab-uc]');
    if (existing) {
      if (window.Accept) resolve();
      else existing.addEventListener('load', () => resolve());
      return;
    }
    const script = document.createElement('script');
    script.src = src;
    script.async = true;
    script.dataset.nabUc = '1';
    if (integrity) {
      script.integrity = integrity;
      script.crossOrigin = 'anonymous';
    }
    script.onload = () => resolve();
    script.onerror = () => reject(new Error('Could not load the secure payment library.'));
    document.head.appendChild(script);
  });
}

/**
 * Mount Unified Checkout and resolve with the transient token once the customer
 * completes card entry. Rejects if they cancel or the widget errors.
 */
export async function mountUnifiedCheckout(options: MountOptions): Promise<string> {
  await loadLibrary(options.clientLibrary, options.clientLibraryIntegrity);
  if (!window.Accept) {
    throw new Error('The secure payment library is unavailable.');
  }
  const accept = await window.Accept(options.captureContext);
  const unifiedPayments = await accept.unifiedPayments();
  return unifiedPayments.show({
    containers: { paymentSelection: `#${options.containerId}` },
  });
}

/** Pull the last four card digits out of a transient token (for the review screen). */
export function tokenLast4(jwt: string): string {
  try {
    const part = jwt.split('.')[1];
    if (!part) return '0000';
    const payload = JSON.parse(
      decodeURIComponent(
        atob(part.replace(/-/g, '+').replace(/_/g, '/'))
          .split('')
          .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
          .join(''),
      ),
    );
    const card = payload?.content?.paymentInformation?.card;
    const masked: string =
      card?.number?.maskedValue ?? card?.number?.bin ?? payload?.data?.number ?? '';
    const match = String(masked).match(/(\d{4})(?!.*\d)/);
    return match ? match[1] : '0000';
  } catch {
    return '0000';
  }
}
