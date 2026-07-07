<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

/**
 * @phpstan-type OrganisationConfig array{
 *   slug: string,
 *   stateCode: string,
 *   label: string,
 *   currency: string,
 *   envPrefix: string
 * }
 */
interface PaymentOrganisationRepositoryInterface
{
    /**
     * @return OrganisationConfig|null
     */
    public function getBySlug(string $slug): ?array;

    /**
     * @return list<OrganisationConfig>
     */
    public function listAll(): array;

    /**
     * @param OrganisationConfig $org
     * @return array{organisationId: string, keyId: string, sharedSecret: string}
     */
    public function readCredentials(array $org): array;

    /**
     * @return array{merchantName: string, supportEmail: string, requireEmail: bool}
     */
    public function getBrandConfig(): array;
}
