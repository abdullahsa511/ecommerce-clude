<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Geoip\RegionGroup;

interface RegionGroupRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all region groups with pagination
     * @param int $languageId
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAll(
        ?int $languageId = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get regions for a specific region group
     * @param int $regionGroupId
     * @param int|null $countryId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getRegions(
        int $regionGroupId,
        ?int $countryId = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Check if a region belongs to a region group
     * @param int $regionGroupId
     * @param int $countryId
     * @param int $regionId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function isRegion(
        int $regionGroupId,
        int $countryId,
        int $regionId,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Add regions to a region group
     * @param array $data
     * @param int $regionGroupId
     * @return bool
     */
    public function addRegions(array $data, int $regionGroupId): bool;

    /**
     * Get region group by ID
     * @param int $regionGroupId
     * @return \App\Core\Models\Geoip\RegionGroup|null
     */
    public function get(int $regionGroupId): ?RegionGroup;
    public function isExistsName(string $name, ?int $id = 0): bool;
    public function updateRegionGroup(array $data, int $id): ?object;
} 