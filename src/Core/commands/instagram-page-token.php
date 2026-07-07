<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../autoload.php';

use App\Core\Services\Instagram\InstagramTokenService;

/**
 * Generate production Instagram credentials using a short-lived user token.
 *
 * Usage:
 *   php src/Core/commands/instagram-page-token.php --token=SHORT_LIVED_TOKEN
 *   php src/Core/commands/instagram-page-token.php --token=SHORT_LIVED_TOKEN --page-id=123456789
 *   php src/Core/commands/instagram-page-token.php --list-pages --token=SHORT_LIVED_TOKEN
 */
function parseCliArgs(array $argv): array
{
    $args = [
        'token' => '',
        'page_id' => null,
        'list_pages' => false,
    ];

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--list-pages') {
            $args['list_pages'] = true;
            continue;
        }

        if (str_starts_with($arg, '--token=')) {
            $args['token'] = substr($arg, 8);
            continue;
        }

        if (str_starts_with($arg, '--page-id=')) {
            $args['page_id'] = substr($arg, 10);
        }
    }

    if ($args['token'] === '') {
        $args['token'] = trim((string) (getenv('INSTAGRAM_SHORT_LIVED_TOKEN') ?: ''));
    }

    return $args;
}

try {
    $args = parseCliArgs($argv);
    $tokenService = new InstagramTokenService();

    if ($args['token'] === '') {
        echo "Missing short-lived token.\n";
        echo "Provide --token=... or set INSTAGRAM_SHORT_LIVED_TOKEN in .env\n";
        exit(1);
    }

    if ($args['list_pages']) {
        $longLived = $tokenService->exchangeForLongLivedToken($args['token']);
        $pages = $tokenService->getPageAccounts($longLived['access_token']);

        echo "Long-lived user token expires in: {$longLived['expires_in']} seconds\n\n";
        echo "Available pages:\n";

        foreach ($pages as $page) {
            echo "- {$page['page_name']} (page_id: {$page['page_id']})\n";
            echo "  instagram_business_account_id: " . ($page['instagram_business_account_id'] ?? 'not linked') . "\n";
        }

        exit(0);
    }

    $credentials = $tokenService->resolvePageCredentials($args['token'], $args['page_id']);

    echo "Instagram page credentials resolved successfully.\n\n";
    echo "Page: {$credentials['page_name']} ({$credentials['page_id']})\n";
    echo "Instagram Business Account ID: {$credentials['instagram_business_account_id']}\n";
    echo "Long-lived user token expires in: {$credentials['long_lived_user_token_expires_in']} seconds\n\n";

    if (!empty($credentials['token_debug'])) {
        $debug = $credentials['token_debug'];
        $expiresAt = isset($debug['expires_at']) ? (int) $debug['expires_at'] : 0;
        echo "Page token valid: " . (!empty($debug['is_valid']) ? 'yes' : 'no') . "\n";
        echo "Page token type: " . ($debug['type'] ?? 'unknown') . "\n";
        echo "Page token expires at: " . ($expiresAt > 0 ? gmdate('Y-m-d H:i:s', $expiresAt) . ' UTC' : 'never / not provided') . "\n\n";
    }

    echo "Add these values to .env:\n\n";
    foreach ($credentials['env'] as $key => $value) {
        echo "{$key}={$value}\n";
    }

    echo "\nDone.\n";
} catch (Throwable $e) {
    echo "Failed to resolve Instagram page token: " . $e->getMessage() . "\n";
    exit(1);
}
