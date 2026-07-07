/**
 * Multi-organisation configuration.
 *
 * Each URL slug maps to a single NAB Gateway *Organisation ID* so that the same
 * application can take payments for several entities (QLD / NSW / VIC) just by
 * changing the path:
 *
 *   /pay          -> Queensland
 *   /payment      -> New South Wales
 *   /makepayment  -> Victoria
 *
 * To add a state, add another entry here and the matching environment variables
 * (see `.env.example`). Credentials are NEVER stored in this file — they are read
 * from the environment at request time on the server only.
 */

export interface OrganisationConfig {
  /** URL path that activates this organisation, e.g. "pay". */
  slug: string;
  /** Short state/entity code shown in titles and receipts, e.g. "QLD". */
  stateCode: string;
  /** Human label, e.g. "Queensland". */
  label: string;
  /** ISO currency for transactions. NAB AU merchants use AUD. */
  currency: string;
  /**
   * Prefix for this organisation's environment variables. For prefix "NAB_QLD"
   * the app reads NAB_QLD_ORG_ID, NAB_QLD_KEY_ID and NAB_QLD_SHARED_SECRET.
   */
  envPrefix: string;
}

/** Brand-level configuration shared across every organisation. */
export const BRAND = {
  merchantName: 'Krost',
  supportEmail: 'accounts@krost.com.au',
  /** Whether the "Email for receipt" field is required. */
  requireEmail: true,
} as const;

/**
 * The slug -> organisation map. Keyed by slug for O(1) lookup.
 * Edit the slugs / states here to suit your business.
 */
export const ORGANISATIONS: Record<string, OrganisationConfig> = {
  pay: {
    slug: 'pay',
    stateCode: 'QLD',
    label: 'Queensland',
    currency: 'AUD',
    envPrefix: 'NAB_QLD',
  },
  payment: {
    slug: 'payment',
    stateCode: 'NSW',
    label: 'New South Wales',
    currency: 'AUD',
    envPrefix: 'NAB_NSW',
  },
  makepayment: {
    slug: 'makepayment',
    stateCode: 'VIC',
    label: 'Victoria',
    currency: 'AUD',
    envPrefix: 'NAB_VIC',
  },
};

export function getOrganisation(slug: string): OrganisationConfig | undefined {
  return ORGANISATIONS[slug];
}

export function listOrganisations(): OrganisationConfig[] {
  return Object.values(ORGANISATIONS);
}
