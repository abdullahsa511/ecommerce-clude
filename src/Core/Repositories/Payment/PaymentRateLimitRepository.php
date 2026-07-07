<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

use App\Core\System\Cache\Redis;

/**
 * Simple sliding-window rate limiter backed by Redis counters.
 */
class PaymentRateLimitRepository implements PaymentRateLimitRepositoryInterface
{
    private const NAMESPACE = 'payment_rate';

    public function __construct(private Redis $redis)
    {
    }

    public function attempt(string $bucket, string $ip, int $maxAttempts, int $windowSeconds): bool
    {
        $ip = trim($ip) !== '' ? trim($ip) : 'unknown';
        $key = $bucket . ':' . hash('sha256', $ip);
        $current = $this->redis->get(self::NAMESPACE, $key);

        if (!is_int($current) && !is_string($current)) {
            $this->redis->set(self::NAMESPACE, $key, 1, $windowSeconds);

            return true;
        }

        $count = (int) $current;
        if ($count >= $maxAttempts) {
            return false;
        }

        $this->redis->set(self::NAMESPACE, $key, $count + 1, $windowSeconds);

        return true;
    }
}
