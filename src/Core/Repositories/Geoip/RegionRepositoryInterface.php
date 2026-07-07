<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Geoip\Region;

interface RegionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all regions with optional filtering
     * @param int|null $countryId
     * @param int|null $status
     * @param string|null $search
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAll(
        ?int $countryId = null,
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get region by ID
     * @param int $regionId
     * @return \App\Core\Models\Geoip\Region|null
     */
    public function get(int $regionId): ?Region;
    public function updateRegion(array $data, int $id): ?object;
    public function isExistsCode(string $code, ?int $id = 0): bool;
} 