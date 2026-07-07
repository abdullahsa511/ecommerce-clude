<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

/**
 * @phpstan-type PaymentIntent array{
 *   intent_id: string,
 *   slug: string,
 *   reference: string,
 *   amount: string,
 *   currency: string,
 *   email: string,
 *   invoice_type: string,
 *   invoice_id: int,
 *   exp: int,
 *   paid: bool
 * }
 *
 * @phpstan-type CaptureSession array{
 *   intent_id: string,
 *   slug: string,
 *   reference: string,
 *   amount: string,
 *   currency: string,
 *   email: string,
 *   capture_context_hash: string,
 *   exp: int
 * }
 */
interface PaymentIntentRepositoryInterface
{
    /**
     * Create a signed, time-limited payment link token.
     *
     * @param array{slug: string, reference: string, amount: string, currency?: string, email?: string, invoice_type?: string, invoice_id?: int, ttl_seconds?: int} $payload
     */
    public function signPaymentIntent(array $payload): string;

    /**
     * Verify a signed payment token and persist the intent server-side.
     *
     * @return PaymentIntent|null
     */
    public function activateSignedToken(string $token): ?array;

    /**
     * @return PaymentIntent|null
     */
    public function getIntent(string $intentId): ?array;

    /**
     * @param PaymentIntent $intent
     */
    public function storeIntent(array $intent, int $ttlSeconds): void;

    public function markIntentPaid(string $intentId): void;

    /**
     * @param CaptureSession $session
     */
    public function storeCaptureSession(string $sessionId, array $session, int $ttlSeconds): void;

    /**
     * @return CaptureSession|null
     */
    public function getCaptureSession(string $sessionId): ?array;

    public function consumeIdempotencyKey(string $key, int $ttlSeconds = 86400): bool;

    public function releaseIdempotencyKey(string $key): void;
}
