#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Debug NAB payment with a transient token from Unified Checkout.
 *
 * Usage:
 *   php scripts/test-nab-pay.php --slug=pay --amount=25.00 --reference=INV-TEST --token='eyJ...'
 */

require dirname(__DIR__) . '/autoload.php';
require dirname(__DIR__) . '/src/Core/System/utils/functions.php';

use App\Core\Repositories\Payment\PaymentOrganisationRepository;
use App\Core\Services\NabPaymentService;
use App\Core\Services\Payment\NabGatewayException;

$options = getopt('', ['slug:', 'amount:', 'reference:', 'token:', 'email::']);
$slug = (string) ($options['slug'] ?? 'pay');
$amount = (string) ($options['amount'] ?? '25.00');
$reference = (string) ($options['reference'] ?? 'INV-TEST');
$token = (string) ($options['token'] ?? '');
$email = (string) ($options['email'] ?? '');

if ($token === '') {
    fwrite(STDERR, "Missing --token (transient JWT from Unified Checkout).\n");
    exit(1);
}

$service = new NabPaymentService(new PaymentOrganisationRepository());
$mode = $service->resolveMode($slug);

echo 'Mode: ' . ($mode['mock'] ? 'mock' : 'live') . PHP_EOL;
echo 'Host: ' . $mode['host'] . PHP_EOL;
echo 'Org:  ' . $mode['credentials']['organisationId'] . PHP_EOL;

if ($service->isTransientTokenExpired($token)) {
    fwrite(STDERR, "Transient token is expired.\n");
    exit(1);
}

try {
    $result = $service->processPayment($slug, [
        'amount' => $amount,
        'reference' => $reference,
        'currency' => 'AUD',
        'email' => $email !== '' ? $email : null,
        'token' => $token,
    ]);
    echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    exit($result['approved'] ? 0 : 2);
} catch (NabGatewayException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    fwrite(STDERR, 'User message: ' . $e->userMessage() . PHP_EOL);
    exit(3);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(4);
}
