-- OAuth2 `clients` seed (League oauth2-server)
-- Database: your app DB (e.g. mvc)
--
-- BEFORE YOU RUN:
-- 1) Replace plaintext secrets in comments below, then regenerate bcrypt with:
--    php -r "echo password_hash('7c15fe0285e9e8fe982fddabbb972fed5b2ffe1d56', PASSWORD_BCRYPT), PHP_EOL;"
--    and replace the `secret` column values.
-- 2) Or use the provided example plaintexts and matching hashes below, then rotate in production.
-- 3) If `clients` already has rows with id 1 or 2, delete or change the ids in this script.
--
-- After insert, set .env:
--   First-party (password grant, internal_sso, matches OAUTH_CLIENT_*):
--     OAUTH_CLIENT_ID=1
--     OAUTH_CLIENT_SECRET=<plaintext for client 1>
--   ERP uses client 2 credentials in POST /api/oauth/token (do not put ERP secret in OAUTH_* unless it is the same client).

-- Optional: remove existing test rows (only if no FK rows reference these ids)
-- DELETE FROM `access_tokens` WHERE `client_id` IN (1, 2);
-- DELETE FROM `clients` WHERE `id` IN (1, 2);

INSERT INTO `clients` (
    `id`,
    `secret`,
    `name`,
    `scopes`,
    `redirect_uri`,
    `revoked`,
    `is_confidential`,
    `created_at`
) VALUES
(
    1,
    '$2y$12$LnZA/WdcK6NBFKeLMD4c6Ov96HHhqz8YJbU.RpxuSLxF6J.FDEXfG',
    'First-party web application',
    '["basic","email","profile"]',
    'http://localhost:8089/',
    0,
    1,
    NOW()
),
(
    2,
    '$2y$12$f58yMnBy..3hUw86GiYjIeXd919Ad.2OSNlNralX2iaTP9zHfs8Iy',
    'ERP machine-to-machine',
    '["basic","email","profile"]',
    'urn:ietf:oauth:2.0:oob',
    0,
    1,
    NOW()
);

-- Plaintext secrets matching the hashes above (CHANGE IN PRODUCTION):
--   Client id 1: ChangeMe_FirstParty_Secret_2026
--   Client id 2: ChangeMe_ERP_M2M_Secret_2026

-- # Krost e-commerce API (this PHP app)
KROST_ECOMMERCE_BASE_URL=https://your-krost-site.example.com

# OAuth client registered in Krost DB (ERP row, e.g. id 2 from seed)
KROST_ECOMMERCE_CLIENT_ID=2
KROST_ECOMMERCE_CLIENT_SECRET=ChangeMe_ERP_M2M_Secret_2026

# Space-separated scopes (must exist in Krost ScopeRepository, e.g. basic, email, profile)
KROST_ECOMMERCE_CLIENT_SCOPE=basic email profile

POST {KROST_ECOMMERCE_BASE_URL}/api/oauth/token with JSON or form body including client_id, client_secret, and scope (same string as KROST_ECOMMERCE_CLIENT_SCOPE).