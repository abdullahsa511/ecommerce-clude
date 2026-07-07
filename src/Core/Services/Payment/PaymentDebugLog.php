<?php

declare(strict_types=1);

namespace App\Core\Services\Payment;

use function App\Core\System\utils\env;

/** Structured payment-flow logging gated by PAYMENT_DEBUG_ERRORS=true. */
final class PaymentDebugLog
{
    public static function enabled(): bool
    {
        return filter_var(env('PAYMENT_DEBUG_ERRORS') ?: 'false', FILTER_VALIDATE_BOOL);
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function log(string $step, array $context = []): void
    {
        if (!self::enabled()) {
            return;
        }

        $line = '[payment] ' . $step;
        if ($context !== []) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        }

        error_log($line);
    }
}
