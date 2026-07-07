<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Repositories\Payment\PaymentOrganisationRepositoryInterface;
use App\Core\Services\Payment\NabGatewayException;
use App\Core\Services\Payment\PaymentDebugLog;
use App\Core\Services\Payment\PaymentValidation;
use RuntimeException;

use function App\Core\System\utils\env;

/**
 * NAB Gateway client (server-only).
 *
 * Provides capture-context creation and payment processing with HTTP Signature auth.
 * Falls back to mock mode when credentials are absent or NAB_MOCK=true.
 */
class NabPaymentService
{
    private const CAPTURE_CONTEXT_PATH = '/up/v1/capture-contexts';
    private const PAYMENTS_PATH = '/pts/v2/payments';

    public function __construct(
        private PaymentOrganisationRepositoryInterface $organisations
    ) {
    }

    /**
     * @return array{mock: bool, host: string, credentials: array{organisationId: string, keyId: string, sharedSecret: string}}
     */
    public function resolveMode(string $slug): array
    {
        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            throw new RuntimeException('Unknown payment organisation.');
        }

        $credentials = $this->organisations->readCredentials($org);
        $explicit = strtolower((string) (env('NAB_MOCK') ?: ''));
        $credsComplete = $credentials['organisationId'] !== ''
            && $credentials['keyId'] !== ''
            && $credentials['sharedSecret'] !== '';

        $mock = match ($explicit) {
            'true' => true,
            'false' => false,
            default => !$this->isLiveEnvironment() && !$credsComplete,
        };

        return [
            'mock' => $mock,
            'host' => $this->apiHost(),
            'credentials' => $credentials,
        ];
    }

    /**
     * completeMandate enables UC auto-processing (show() returns payment-result JWT).
     * Production/live always. Sandbox only when portal payment processing is published
     * (NAB_UC_SANDBOX_AUTO_PROCESSING=true) — otherwise browser complete() is used.
     */
    public function usesCompleteMandate(): bool
    {
        if ($this->isLiveEnvironment()) {
            return true;
        }

        return filter_var(env('NAB_UC_SANDBOX_AUTO_PROCESSING') ?: 'false', FILTER_VALIDATE_BOOL);
    }

    public function nabEnvironmentLabel(): string
    {
        return strtolower((string) (env('NAB_ENVIRONMENT') ?: 'test'));
    }

    /**
     * Safe JWT summary for logs — never logs card numbers or full tokens.
     *
     * @return array<string, mixed>
     */
    public function describeTokenForLog(string $jwt): array
    {
        $payload = $this->decodeJwtPayload($jwt);
        $kind = 'unknown';
        if ($this->isPaymentResultJwt($jwt)) {
            $kind = 'payment_result';
        } elseif ($this->isTransientTokenJwt($jwt)) {
            $kind = 'transient';
        }

        return [
            'kind' => $kind,
            'type' => $payload['type'] ?? null,
            'status' => $this->extractPaymentStatus($payload),
            'transactionId' => $this->extractTransactionId($payload) ?: null,
            'exp' => isset($payload['exp']) ? (int) $payload['exp'] : null,
            'jti_prefix' => isset($payload['jti']) ? substr((string) $payload['jti'], 0, 8) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function describeCaptureContextForLog(string $jwt): array
    {
        $payload = $this->decodeJwtPayload($jwt);
        $data = [];
        if (is_array($payload['ctx'] ?? null) && isset($payload['ctx'][0]['data']) && is_array($payload['ctx'][0]['data'])) {
            $data = $payload['ctx'][0]['data'];
        }

        return [
            'session_complete_mandate' => $this->captureContextHasCompleteMandate($jwt),
            'session_portal_processing' => $this->captureContextHasPortalProcessing($jwt),
            'ctx_data_keys' => array_keys($data),
            'client_library' => isset($data['clientLibrary']) ? parse_url((string) $data['clientLibrary'], PHP_URL_HOST) : null,
        ];
    }

    /**
     * @param array{amount: string, reference: string, currency: string, targetOrigin: string, email?: string} $input
     * @return array{mock: bool, captureContext: string, completeMandate: bool, clientLibrary?: string, clientLibraryIntegrity?: string}
     */
    public function createCaptureContext(string $slug, array $input): array
    {
        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            throw new RuntimeException('Unknown payment organisation.');
        }

        $mode = $this->resolveMode($slug);

        if ($mode['mock']) {
            $captureContext = $this->encodeMockJwt([
                'mock' => true,
                'iat' => time(),
                'data' => [
                    'currency' => $input['currency'],
                    'amount' => $input['amount'],
                    'reference' => $input['reference'],
                ],
            ]);

            return ['mock' => true, 'captureContext' => $captureContext, 'completeMandate' => false];
        }

        $payload = [
            'targetOrigins' => [$input['targetOrigin']],
            'allowedCardNetworks' => ['VISA', 'MASTERCARD', 'AMEX'],
            'allowedPaymentTypes' => ['PANENTRY'],
            'country' => 'AU',
            'locale' => 'en_AU',
            'captureMandate' => [
                'billingType' => 'NONE',
                'requestEmail' => false,
                'requestPhone' => false,
                'requestShipping' => false,
                'showAcceptedNetworkIcons' => true,
                'showConfirmationStep' => false,
            ],
            'orderInformation' => [
                'amountDetails' => [
                    'totalAmount' => $input['amount'],
                    'currency' => $input['currency'],
                ],
            ],
        ];

        if ($this->usesCompleteMandate()) {
            $payload['completeMandate'] = ['type' => 'CAPTURE'];
        }

        $email = trim((string) ($input['email'] ?? ''));
        if ($email !== '' && PaymentValidation::isValidEmail($email)) {
            $payload['orderInformation']['billTo'] = [
                'email' => $email,
                'firstName' => 'Customer',
                'lastName' => 'Payment',
                'address1' => '1 George Street',
                'buildingNumber' => '1',
                'locality' => 'Brisbane',
                'administrativeArea' => 'QLD',
                'postalCode' => '4000',
                'country' => 'AU',
            ];
        }

        PaymentDebugLog::log('capture_context.request', [
            'slug' => $slug,
            'nab_environment' => $this->nabEnvironmentLabel(),
            'mock' => false,
            'requested_complete_mandate' => $this->usesCompleteMandate(),
            'target_origin' => $input['targetOrigin'],
            'amount' => $input['amount'],
            'currency' => $input['currency'],
            'reference' => $input['reference'],
            'has_email' => $email !== '',
        ]);

        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        $jwt = $this->nabPost($mode, self::CAPTURE_CONTEXT_PATH, $body, 'text');
        $decoded = $this->decodeJwtPayload($jwt);
        $ctx = $decoded['ctx'] ?? [];
        $data = is_array($ctx) && isset($ctx[0]['data']) && is_array($ctx[0]['data'])
            ? $ctx[0]['data']
            : [];

        PaymentDebugLog::log('capture_context.response', array_merge(
            ['slug' => $slug],
            $this->describeCaptureContextForLog($jwt)
        ));

        return [
            'mock' => false,
            'captureContext' => $jwt,
            'completeMandate' => $this->captureContextHasCompleteMandate($jwt),
            'clientLibrary' => isset($data['clientLibrary']) ? (string) $data['clientLibrary'] : '',
            'clientLibraryIntegrity' => isset($data['clientLibraryIntegrity']) ? (string) $data['clientLibraryIntegrity'] : '',
        ];
    }

    /**
     * True when the capture-context JWT enables UC auto-processing (completeMandate in session).
     */
    public function captureContextHasCompleteMandate(string $jwt): bool
    {
        $payload = $this->decodeJwtPayload($jwt);
        if ($payload === []) {
            return false;
        }

        if (isset($payload['completeMandate']) && is_array($payload['completeMandate'])) {
            return true;
        }

        $ctx = $payload['ctx'] ?? null;
        if (!is_array($ctx)) {
            return false;
        }

        foreach ($ctx as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            if (isset($entry['completeMandate']) && is_array($entry['completeMandate'])) {
                return true;
            }
            $data = $entry['data'] ?? null;
            if (is_array($data) && isset($data['completeMandate']) && is_array($data['completeMandate'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * True when NAB portal has published payment processing (paymentConfigurations non-empty).
     */
    public function captureContextHasPortalProcessing(string $jwt): bool
    {
        $payload = $this->decodeJwtPayload($jwt);
        $data = [];
        if (is_array($payload['ctx'] ?? null) && isset($payload['ctx'][0]['data']) && is_array($payload['ctx'][0]['data'])) {
            $data = $payload['ctx'][0]['data'];
        }

        $configs = $data['paymentConfigurations'] ?? null;
        if (!is_array($configs)) {
            return false;
        }

        return $configs !== [];
    }

    /**
     * @param array{amount: string, reference: string, currency: string, email?: string, token: string} $input
     * @return array{approved: bool, status: string, transactionId: string, message: string, last4?: string, brand?: string}
     */
    public function processPayment(string $slug, array $input): array
    {
        $mode = $this->resolveMode($slug);

        if ($mode['mock']) {
            return $this->processMockPayment($input['token']);
        }

        if ($this->isTransientTokenExpired($input['token'])) {
            return [
                'approved' => false,
                'status' => 'INVALID_REQUEST',
                'transactionId' => '',
                'message' => 'Your card session has expired. Go back and enter your card details again.',
            ];
        }

        $orderInformation = [
            'amountDetails' => [
                'totalAmount' => $input['amount'],
                'currency' => $input['currency'],
            ],
        ];

        if (!empty($input['email'])) {
            $orderInformation['billTo'] = ['email' => $input['email']];
        }

        // NAB UC docs: authorise with transient token, then capture as a follow-on service.
        $authBody = json_encode([
            'clientReferenceInformation' => ['code' => $input['reference']],
            'processingInformation' => ['commerceIndicator' => 'internet'],
            'orderInformation' => $orderInformation,
            'tokenInformation' => ['transientTokenJwt' => $input['token']],
        ], JSON_THROW_ON_ERROR);

        $authResponse = $this->nabPost($mode, self::PAYMENTS_PATH, $authBody, 'json');
        $authStatus = (string) ($authResponse['status'] ?? 'UNKNOWN');
        $transactionId = (string) ($authResponse['id'] ?? '');

        if (!in_array($authStatus, ['AUTHORIZED', 'PARTIAL_AUTHORIZED'], true)) {
            return [
                'approved' => false,
                'status' => $authStatus,
                'transactionId' => $transactionId,
                'message' => 'Your card issuer declined this transaction.',
            ];
        }

        $captureBody = json_encode([
            'clientReferenceInformation' => ['code' => $input['reference']],
            'orderInformation' => [
                'amountDetails' => [
                    'totalAmount' => $input['amount'],
                    'currency' => $input['currency'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $captureResponse = $this->nabPost(
            $mode,
            self::PAYMENTS_PATH . '/' . rawurlencode($transactionId) . '/captures',
            $captureBody,
            'json'
        );
        $captureStatus = (string) ($captureResponse['status'] ?? $authStatus);
        $approved = in_array($captureStatus, ['PENDING', 'SETTLED', 'TRANSMITTED', 'AUTHORIZED'], true)
            || in_array($authStatus, ['AUTHORIZED', 'PARTIAL_AUTHORIZED'], true);

        return [
            'approved' => $approved,
            'status' => $captureStatus,
            'transactionId' => $transactionId,
            'message' => $approved ? 'Approved' : 'Your card issuer declined this transaction.',
        ];
    }

    public function isTransientTokenExpired(string $token): bool
    {
        $payload = $this->decodeJwtPayload($token);
        $exp = (int) ($payload['exp'] ?? 0);

        return $exp > 0 && $exp < time();
    }

    /**
     * True when the JWT is a Flex / UC transient card token (not a payment-result JWT).
     */
    public function isTransientTokenJwt(string $jwt): bool
    {
        if ($this->isPaymentResultJwt($jwt)) {
            return false;
        }

        $payload = $this->decodeJwtPayload($jwt);
        if ($payload === []) {
            return true;
        }

        $type = strtolower((string) ($payload['type'] ?? ''));
        if ($type !== '' && (str_contains($type, 'gda') || str_contains($type, 'transient'))) {
            return true;
        }

        return isset($payload['content']['paymentInformation']);
    }

    /**
     * True when the JWT is a UC payment-result (post-capture), not a transient card token.
     */
    public function isPaymentResultJwt(string $jwt): bool
    {
        $payload = $this->decodeJwtPayload($jwt);
        if ($payload === []) {
            return false;
        }

        $type = strtolower((string) ($payload['type'] ?? ''));
        if ($type !== '' && (str_contains($type, 'gda') || str_contains($type, 'transient') || str_contains($type, 'flex/'))) {
            return false;
        }

        if (
            isset($payload['content']['paymentInformation'])
            && $this->extractPaymentStatus($payload) === null
        ) {
            return false;
        }

        if ($this->extractPaymentStatus($payload) !== null) {
            return true;
        }

        return $this->extractTransactionId($payload) !== '';
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractPaymentStatus(array $payload): ?string
    {
        $candidates = [
            $payload['status'] ?? null,
            $payload['outcome'] ?? null,
            $payload['paymentStatus'] ?? null,
            $payload['details']['status'] ?? null,
            $payload['content']['status'] ?? null,
            $payload['completeResponse']['status'] ?? null,
            $payload['completeResponse']['outcome'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractTransactionId(array $payload): string
    {
        $candidates = [
            $payload['id'] ?? null,
            $payload['details']['id'] ?? null,
            $payload['processorInformation']['transactionId'] ?? null,
            $payload['completeResponse']['id'] ?? null,
            $payload['completeResponse']['details']['id'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return '';
    }

    /**
     * @return list<string>
     */
    private function approvedPaymentStatuses(): array
    {
        return [
            'AUTHORIZED',
            'PARTIAL_AUTHORIZED',
            'PENDING',
            'PENDING_CAPTURE',
            'SETTLED',
            'TRANSMITTED',
            'CAPTURED',
            'COMPLETED',
            'SUCCESS',
        ];
    }

    /**
     * Parse the payment-result JWT returned by unifiedPayments.complete() or auto-processing show().
     *
     * @param array{amount: string, currency: string} $expected
     * @return array{approved: bool, status: string, transactionId: string, message: string}
     */
    public function parseCompletionResult(string $jwt, array $expected): array
    {
        PaymentDebugLog::log('parse_completion.request', array_merge(
            ['expected_amount' => $expected['amount'], 'expected_currency' => $expected['currency']],
            $this->describeTokenForLog($jwt)
        ));

        $payload = $this->decodeJwtPayload($jwt);
        if ($payload === []) {
            return [
                'approved' => false,
                'status' => 'INVALID_REQUEST',
                'transactionId' => '',
                'message' => 'Payment result could not be read. Please try again.',
            ];
        }

        $status = $this->extractPaymentStatus($payload) ?? 'UNKNOWN';
        $transactionId = $this->extractTransactionId($payload);
        $approved = in_array($status, $this->approvedPaymentStatuses(), true);

        $resultAmount = $this->extractAmountFromPayload($payload);
        if ($resultAmount !== null && $resultAmount !== $expected['amount']) {
            return [
                'approved' => false,
                'status' => 'INVALID_REQUEST',
                'transactionId' => $transactionId,
                'message' => 'Payment amount mismatch.',
            ];
        }

        $message = 'Approved';
        if (!$approved) {
            $message = $status === 'UNKNOWN'
                ? 'Payment could not be verified. Please try again.'
                : 'Your card issuer declined this transaction.';
        }

        $result = [
            'approved' => $approved,
            'status' => $status,
            'transactionId' => $transactionId,
            'message' => $message,
        ];

        PaymentDebugLog::log('parse_completion.response', $result);

        return $result;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractAmountFromPayload(array $payload): ?string
    {
        $paths = [
            ['details', 'orderInformation', 'amountDetails', 'totalAmount'],
            ['orderInformation', 'amountDetails', 'totalAmount'],
            ['content', 'orderInformation', 'amountDetails', 'totalAmount'],
        ];

        foreach ($paths as $path) {
            $value = $payload;
            foreach ($path as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    $value = null;
                    break;
                }
                $value = $value[$segment];
            }
            if (is_string($value) || is_numeric($value)) {
                $parsed = PaymentValidation::parseAmount($value);
                if ($parsed['ok']) {
                    return $parsed['value'];
                }
            }
            if (is_array($value) && isset($value['value'])) {
                $parsed = PaymentValidation::parseAmount($value['value']);
                if ($parsed['ok']) {
                    return $parsed['value'];
                }
            }
        }

        return null;
    }

    /**
     * Decode a JWT payload without verifying the signature (used for amount cross-check only).
     *
     * @return array<string, mixed>
     */
    public function decodeJwtPayload(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) < 2) {
            return [];
        }

        $decoded = $this->base64urlDecode($parts[1]);
        if ($decoded === false) {
            return [];
        }

        $payload = json_decode($decoded, true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * Extract amount from a transient token JWT when present.
     */
    public function extractTokenAmount(string $token): ?string
    {
        $payload = $this->decodeJwtPayload($token);
        $paths = [
            ['content', 'orderInformation', 'amountDetails', 'totalAmount'],
            ['orderInformation', 'amountDetails', 'totalAmount'],
            ['data', 'amount'],
        ];

        foreach ($paths as $path) {
            $value = $payload;
            foreach ($path as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    $value = null;
                    break;
                }
                $value = $value[$segment];
            }
            if (is_string($value) || is_numeric($value)) {
                $parsed = PaymentValidation::parseAmount($value);
                if ($parsed['ok']) {
                    return $parsed['value'];
                }
            }
            if (is_array($value) && isset($value['value']) && (is_string($value['value']) || is_numeric($value['value']))) {
                $parsed = PaymentValidation::parseAmount($value['value']);
                if ($parsed['ok']) {
                    return $parsed['value'];
                }
            }
        }

        return null;
    }

    public function tokenLast4(string $jwt): string
    {
        $payload = $this->decodeJwtPayload($jwt);
        $card = $payload['content']['paymentInformation']['card'] ?? null;
        if (!is_array($card)) {
            return '0000';
        }

        $masked = (string) ($card['number']['maskedValue'] ?? $card['number']['bin'] ?? $payload['data']['number'] ?? '');
        if (preg_match('/(\d{4})(?!.*\d)/', $masked, $matches)) {
            return $matches[1];
        }

        return '0000';
    }

    /**
     * @return array{approved: bool, status: string, transactionId: string, message: string, last4?: string, brand?: string}
     */
    private function processMockPayment(string $token): array
    {
        $transactionId = 'NAB' . substr((string) time(), -8);
        $mockToken = $this->readMockToken($token);

        if ($mockToken === null) {
            return [
                'approved' => false,
                'status' => 'INVALID_REQUEST',
                'transactionId' => $transactionId,
                'message' => 'Card details could not be read. Please try again.',
            ];
        }

        if (!empty($mockToken['declined'])) {
            return [
                'approved' => false,
                'status' => 'DECLINED',
                'transactionId' => $transactionId,
                'message' => 'Your card issuer declined this transaction.',
                'last4' => (string) ($mockToken['last4'] ?? ''),
                'brand' => (string) ($mockToken['brand'] ?? ''),
            ];
        }

        return [
            'approved' => true,
            'status' => 'AUTHORIZED',
            'transactionId' => $transactionId,
            'message' => 'Approved',
            'last4' => (string) ($mockToken['last4'] ?? ''),
            'brand' => (string) ($mockToken['brand'] ?? ''),
        ];
    }

    /**
     * @return array{mock: true, last4?: string, brand?: string, declined?: bool}|null
     */
    private function readMockToken(string $token): ?array
    {
        $decoded = $this->base64urlDecode($token);
        if ($decoded === false) {
            return null;
        }

        $parsed = json_decode($decoded, true);
        if (!is_array($parsed) || empty($parsed['mock'])) {
            return null;
        }

        return $parsed;
    }

    /**
     * @param array{mock: bool, host: string, credentials: array{organisationId: string, keyId: string, sharedSecret: string}} $mode
     */
    private function nabPost(array $mode, string $resource, string $body, string $expect): mixed
    {
        $headers = $this->buildAuthHeaders(
            'post',
            $resource,
            $mode['host'],
            $mode['credentials'],
            $body
        );

        $headerLines = [];
        foreach ($headers as $name => $value) {
            $headerLines[] = $name . ': ' . $value;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headerLines),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 30,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $url = 'https://' . $mode['host'] . $resource;
        $text = @file_get_contents($url, false, $context);
        if ($text === false) {
            throw new RuntimeException('Unable to reach NAB Gateway.');
        }

        $statusCode = 0;
        if (isset($http_response_header[0]) && preg_match('/\d{3}/', $http_response_header[0], $matches)) {
            $statusCode = (int) $matches[0];
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            PaymentDebugLog::log('nab.http_error', [
                'resource' => $resource,
                'status' => $statusCode,
                'body_preview' => substr($text, 0, 500),
            ]);
            throw new NabGatewayException($resource, $statusCode, substr($text, 0, 2000));
        }

        if ($expect === 'text') {
            return $text;
        }

        $json = json_decode($text, true);
        if (!is_array($json)) {
            throw new RuntimeException('NAB ' . $resource . ' returned a non-JSON response.');
        }

        return $json;
    }

    /**
     * @param array{organisationId: string, keyId: string, sharedSecret: string} $credentials
     * @return array<string, string>
     */
    private function buildAuthHeaders(
        string $method,
        string $resource,
        string $host,
        array $credentials,
        string $body = ''
    ): array {
        $date = gmdate('D, d M Y H:i:s') . ' GMT';
        $digest = $this->buildDigest($body);

        $headers = [
            'v-c-merchant-id' => $credentials['organisationId'],
            'Date' => $date,
            'Host' => $host,
            'Digest' => $digest,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $headers['Signature'] = $this->buildSignatureHeader(
            $method,
            $resource,
            $host,
            $credentials,
            $date,
            $digest
        );

        return $headers;
    }

    private function buildDigest(string $body): string
    {
        return 'SHA-256=' . base64_encode(hash('sha256', $body, true));
    }

    /**
     * @param array{organisationId: string, keyId: string, sharedSecret: string} $credentials
     */
    private function buildSignatureHeader(
        string $method,
        string $resource,
        string $host,
        array $credentials,
        string $date,
        string $digest
    ): string {
        $parts = [
            'host: ' . $host,
            'date: ' . $date,
            '(request-target): ' . $method . ' ' . $resource,
            'digest: ' . $digest,
            'v-c-merchant-id: ' . $credentials['organisationId'],
        ];

        $signingString = implode("\n", $parts);
        $key = base64_decode($credentials['sharedSecret'], true);
        if ($key === false) {
            throw new RuntimeException('Invalid NAB shared secret.');
        }

        $signature = base64_encode(hash_hmac('sha256', $signingString, $key, true));

        return sprintf(
            'keyid="%s", algorithm="HmacSHA256", headers="host date (request-target) digest v-c-merchant-id", signature="%s"',
            $credentials['keyId'],
            $signature
        );
    }

    private function isLiveEnvironment(): bool
    {
        $env = strtolower((string) (env('NAB_ENVIRONMENT') ?: 'test'));

        return in_array($env, ['live', 'production'], true);
    }

    private function apiHost(): string
    {
        return $this->isLiveEnvironment()
            ? 'nabgateway-api.nab.com.au'
            : 'nabgateway-api-test.nab.com.au';
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function encodeMockJwt(array $payload): string
    {
        return $this->base64urlEncode(json_encode(['alg' => 'none', 'typ' => 'JWT'], JSON_THROW_ON_ERROR))
            . '.'
            . $this->base64urlEncode(json_encode($payload, JSON_THROW_ON_ERROR))
            . '.';
    }

    private function base64urlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64urlDecode(string $value): string|false
    {
        $remainder = strlen($value) % 4;
        if ($remainder > 0) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($value, '-_', '+/'), true);
    }
}
