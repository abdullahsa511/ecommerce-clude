#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generate a signed payment link token for local / sandbox testing.
 *
 * Usage:
 *   php scripts/generate-payment-token.php --slug=payment --amount=10.00 --reference=INV-TEST-001 --email=you@example.com
 *
 * Slugs: pay (QLD), payment (NSW), makepayment (VIC)
 */

require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/src/Core/System/utils/functions.php';

use App\Core\Services\Payment\PaymentValidation;

use function App\Core\System\utils\env;

function arg(string $name, string $default = ''): string
{
    $prefix = '--' . $name . '=';
    foreach ($GLOBALS['argv'] ?? [] as $arg) {
        if (str_starts_with($arg, $prefix)) {
            return substr($arg, strlen($prefix));
        }
    }

    return $default;
}

function base64urlEncode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

$slug = arg('slug', 'payment');
$amount = arg('amount', '10.00');
$reference = arg('reference', 'INV-TEST-' . date('Ymd-His'));
$email = arg('email', 'test@example.com');
$ttl = max(300, (int) arg('ttl', '86400'));
$baseUrl = rtrim(arg('base', env('APP_URL') ?: 'http://localhost:8089'), '/');

$path = match ($slug) {
    'pay' => '/pay',
    'payment' => '/payment',
    'makepayment' => '/makepayment',
    default => null,
};

if ($path === null) {
    fwrite(STDERR, "Invalid --slug. Use: pay, payment, or makepayment\n");
    exit(1);
}

$parsedAmount = PaymentValidation::parseAmount($amount);
$parsedReference = PaymentValidation::validateReference($reference);
if (!$parsedAmount['ok']) {
    fwrite(STDERR, 'Invalid amount: ' . ($parsedAmount['error'] ?? 'unknown') . "\n");
    exit(1);
}
if (!$parsedReference['ok']) {
    fwrite(STDERR, 'Invalid reference: ' . ($parsedReference['error'] ?? 'unknown') . "\n");
    exit(1);
}

$signingSecret = (string) (env('PAYMENT_INTENT_SECRET') ?: env('APP_SECRET') ?: 'change-me-payment-intent-secret');
$body = [
    'slug' => $slug,
    'reference' => $parsedReference['value'],
    'amount' => $parsedAmount['value'],
    'currency' => 'AUD',
    'email' => PaymentValidation::isValidEmail($email) ? trim($email) : '',
    'invoice_type' => 'manual',
    'invoice_id' => 0,
    'exp' => time() + $ttl,
    'jti' => bin2hex(random_bytes(16)),
];

$encoded = base64urlEncode(json_encode($body, JSON_THROW_ON_ERROR));
$signature = hash_hmac('sha256', $encoded, $signingSecret);
$token = $encoded . '.' . $signature;
$url = $baseUrl . $path . '?token=' . rawurlencode($token);

echo "Signed payment link (valid {$ttl} seconds)\n";
echo "Slug:      {$slug}\n";
echo "Amount:    {$parsedAmount['value']} AUD\n";
echo "Reference: {$parsedReference['value']}\n";
echo "Email:     {$body['email']}\n";
echo "\n";
echo $url . "\n";
