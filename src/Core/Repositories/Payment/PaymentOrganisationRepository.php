<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

use function App\Core\System\utils\env;

/**
 * Multi-organisation NAB configuration (QLD / NSW / VIC slugs).
 * Credentials are read from the environment at request time — never stored here.
 */
class PaymentOrganisationRepository implements PaymentOrganisationRepositoryInterface
{
    /** @var array<string, array{slug: string, stateCode: string, label: string, currency: string, envPrefix: string}> */
    private const ORGANISATIONS = [
        'pay' => [
            'slug' => 'pay',
            'stateCode' => 'QLD',
            'label' => 'Queensland',
            'currency' => 'AUD',
            'envPrefix' => 'NAB_QLD',
        ],
        'payment' => [
            'slug' => 'payment',
            'stateCode' => 'NSW',
            'label' => 'New South Wales',
            'currency' => 'AUD',
            'envPrefix' => 'NAB_NSW',
        ],
        'makepayment' => [
            'slug' => 'makepayment',
            'stateCode' => 'VIC',
            'label' => 'Victoria',
            'currency' => 'AUD',
            'envPrefix' => 'NAB_VIC',
        ],
    ];

    public function getBySlug(string $slug): ?array
    {
        return self::ORGANISATIONS[$slug] ?? null;
    }

    public function listAll(): array
    {
        return array_values(self::ORGANISATIONS);
    }

    public function readCredentials(array $org): array
    {
        $prefix = $org['envPrefix'];

        return [
            'organisationId' => (string) (env($prefix . '_ORG_ID') ?: ''),
            'keyId' => (string) (env($prefix . '_KEY_ID') ?: ''),
            'sharedSecret' => (string) (env($prefix . '_SHARED_SECRET') ?: ''),
        ];
    }

    public function getBrandConfig(): array
    {
        return [
            'merchantName' => (string) (env('PAYMENT_MERCHANT_NAME') ?: 'Krost'),
            'supportEmail' => (string) (env('PAYMENT_SUPPORT_EMAIL') ?: 'accounts@krost.com.au'),
            'requireEmail' => filter_var(env('PAYMENT_REQUIRE_EMAIL') ?: 'true', FILTER_VALIDATE_BOOL),
        ];
    }
}
