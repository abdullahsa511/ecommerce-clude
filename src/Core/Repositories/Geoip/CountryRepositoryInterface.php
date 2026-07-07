<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Geoip\Country;

interface CountryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all countries with pagination and filtering
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a single country by ID
     */
    public function get(int $countryId): ?Country;
    public function isExistsName(string $name, ?int $id = 0): bool;
    public function updateCountry(array $data, int $id): ?object;
} 