<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

interface PaymentRateLimitRepositoryInterface
{
    /**
     * Returns true when the action is allowed, false when rate limit exceeded.
     */
    public function attempt(string $bucket, string $ip, int $maxAttempts, int $windowSeconds): bool;
}
