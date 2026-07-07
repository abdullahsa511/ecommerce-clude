<?php

declare(strict_types=1);

namespace App\Core\Services\Payment;

/**
 * Shared validation / sanitisation for payment amount, reference, and email.
 * Server-side only — never trust values from the browser.
 */
final class PaymentValidation
{
    public const MAX_AMOUNT = 1_000_000;

    /**
     * @return array{ok: bool, value?: string, error?: string}
     */
    public static function parseAmount(mixed $input): array
    {
        if ($input === null || $input === '' || trim((string) $input) === '') {
            return ['ok' => false, 'error' => 'Enter an amount'];
        }

        $raw = trim(str_replace(['$', ',', ' '], '', (string) $input));
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $raw)) {
            return ['ok' => false, 'error' => 'Enter a valid amount (e.g. 1250.00)'];
        }

        $num = (float) $raw;
        if (!is_finite($num) || $num <= 0) {
            return ['ok' => false, 'error' => 'Amount must be greater than zero'];
        }
        if ($num > self::MAX_AMOUNT) {
            return ['ok' => false, 'error' => 'Amount exceeds the maximum allowed'];
        }

        return ['ok' => true, 'value' => number_format($num, 2, '.', '')];
    }

    public static function sanitizeReference(mixed $input): string
    {
        if ($input === null) {
            return '';
        }

        // ~ delimiters — pattern allows letters, digits, space, - / . # : in references.
        $value = preg_replace('~[^\w\s\-/.#:]~u', '', (string) $input) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return mb_substr(trim($value), 0, 50);
    }

    /**
     * @return array{ok: bool, value?: string, error?: string}
     */
    public static function validateReference(mixed $input): array
    {
        $value = self::sanitizeReference($input);
        if ($value === '') {
            return ['ok' => false, 'error' => 'Enter a reference'];
        }

        return ['ok' => true, 'value' => $value];
    }

    public static function isValidEmail(mixed $input): bool
    {
        if (!is_string($input)) {
            return false;
        }

        $value = trim($input);

        return $value !== '' && strlen($value) <= 254 && (bool) preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $value);
    }
}
