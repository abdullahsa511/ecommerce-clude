<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

use App\Core\Services\Payment\PaymentValidation;
use App\Core\System\Cache\Redis;

use function App\Core\System\utils\env;

/**
 * Signed payment intents, capture sessions, and idempotency keys (Redis-backed).
 */
class PaymentIntentRepository implements PaymentIntentRepositoryInterface
{
    private const NAMESPACE = 'payment';

    private string $signingSecret;

    public function __construct(private Redis $redis)
    {
        $this->signingSecret = (string) (env('PAYMENT_INTENT_SECRET') ?: env('APP_SECRET') ?: 'change-me-payment-intent-secret');
    }

    public function signPaymentIntent(array $payload): string
    {
        $amount = PaymentValidation::parseAmount($payload['amount'] ?? '');
        $reference = PaymentValidation::validateReference($payload['reference'] ?? '');
        if (!$amount['ok'] || !$reference['ok']) {
            throw new \InvalidArgumentException('Invalid payment intent payload.');
        }

        $ttl = max(300, (int) ($payload['ttl_seconds'] ?? 604800));
        $body = [
            'slug' => (string) ($payload['slug'] ?? 'pay'),
            'reference' => $reference['value'],
            'amount' => $amount['value'],
            'currency' => (string) ($payload['currency'] ?? 'AUD'),
            'email' => PaymentValidation::isValidEmail($payload['email'] ?? '') ? trim((string) $payload['email']) : '',
            'invoice_type' => (string) ($payload['invoice_type'] ?? 'manual'),
            'invoice_id' => (int) ($payload['invoice_id'] ?? 0),
            'exp' => time() + $ttl,
            'jti' => bin2hex(random_bytes(16)),
        ];

        $encoded = $this->base64urlEncode(json_encode($body, JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $encoded, $this->signingSecret);

        return $encoded . '.' . $signature;
    }

    public function activateSignedToken(string $token): ?array
    {
        $parts = explode('.', trim($token), 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$encoded, $signature] = $parts;
        $expected = hash_hmac('sha256', $encoded, $this->signingSecret);
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $decoded = json_decode($this->base64urlDecode($encoded), true);
        if (!is_array($decoded)) {
            return null;
        }

        if ((int) ($decoded['exp'] ?? 0) < time()) {
            return null;
        }

        $intentId = (string) ($decoded['jti'] ?? '');
        if ($intentId === '') {
            return null;
        }

        $intent = [
            'intent_id' => $intentId,
            'slug' => (string) ($decoded['slug'] ?? 'pay'),
            'reference' => PaymentValidation::sanitizeReference($decoded['reference'] ?? ''),
            'amount' => (string) ($decoded['amount'] ?? ''),
            'currency' => (string) ($decoded['currency'] ?? 'AUD'),
            'email' => (string) ($decoded['email'] ?? ''),
            'invoice_type' => (string) ($decoded['invoice_type'] ?? 'manual'),
            'invoice_id' => (int) ($decoded['invoice_id'] ?? 0),
            'exp' => (int) ($decoded['exp'] ?? 0),
            'paid' => false,
        ];

        $ttl = max(60, $intent['exp'] - time());
        $this->redis->set(self::NAMESPACE, 'intent:' . $intentId, $intent, $ttl);

        return $intent;
    }

    public function getIntent(string $intentId): ?array
    {
        $intentId = trim($intentId);
        if ($intentId === '') {
            return null;
        }

        $intent = $this->redis->get(self::NAMESPACE, 'intent:' . $intentId);

        return is_array($intent) ? $intent : null;
    }

    public function storeIntent(array $intent, int $ttlSeconds): void
    {
        $intentId = trim((string) ($intent['intent_id'] ?? ''));
        if ($intentId === '') {
            return;
        }

        $this->redis->set(self::NAMESPACE, 'intent:' . $intentId, $intent, max(60, $ttlSeconds));
    }

    public function markIntentPaid(string $intentId): void
    {
        $intent = $this->getIntent($intentId);
        if ($intent === null) {
            return;
        }

        $intent['paid'] = true;
        $ttl = max(60, (int) $intent['exp'] - time());
        $this->redis->set(self::NAMESPACE, 'intent:' . $intentId, $intent, $ttl);
    }

    public function storeCaptureSession(string $sessionId, array $session, int $ttlSeconds): void
    {
        $this->redis->set(self::NAMESPACE, 'capture:' . $sessionId, $session, max(60, $ttlSeconds));
    }

    public function getCaptureSession(string $sessionId): ?array
    {
        $sessionId = trim($sessionId);
        if ($sessionId === '') {
            return null;
        }

        $session = $this->redis->get(self::NAMESPACE, 'capture:' . $sessionId);

        return is_array($session) ? $session : null;
    }

    public function consumeIdempotencyKey(string $key, int $ttlSeconds = 86400): bool
    {
        $key = trim($key);
        if ($key === '' || strlen($key) > 128) {
            return false;
        }

        $existing = $this->redis->get(self::NAMESPACE, 'idem:' . $key);
        if ($existing !== null) {
            return false;
        }

        $this->redis->set(self::NAMESPACE, 'idem:' . $key, 1, max(60, $ttlSeconds));

        return true;
    }

    public function releaseIdempotencyKey(string $key): void
    {
        $key = trim($key);
        if ($key === '') {
            return;
        }

        $this->redis->delete(self::NAMESPACE, 'idem:' . $key);
    }

    private function base64urlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64urlDecode(string $value): string
    {
        $remainder = strlen($value) % 4;
        if ($remainder > 0) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode(strtr($value, '-_', '+/'), true);
    }
}
