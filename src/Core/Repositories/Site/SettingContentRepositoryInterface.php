<?php

namespace App\Core\Repositories\Site;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SettingContentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single setting content value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @param int|null $languageId
     * @return array|null
     */
    public function get(?int $siteId, ?string $namespace, string $key, ?int $languageId = null): ?array;

    /**
     * Set a single setting content value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @param string $value
     * @param int|null $languageId
     * @return int
     */
    public function set(?int $siteId, ?string $namespace, string $key, string $value, ?int $languageId = null): int;

    /**
     * Get multiple setting contents at once
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @param int|null $languageId
     * @return array
     */
    public function getMulti(?int $siteId, ?string $namespace, ?array $keys = null, ?int $languageId = null): array;

    /**
     * Set multiple setting contents at once
     * 
     * @param int|null $siteId
     * @param array $meta Array of setting content data
     * @return int
     */
    public function setMulti(?int $siteId, array $meta): int;

    /**
     * Delete setting contents by filters
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @param int|null $languageId
     * @return bool
     */
    public function deleteByFilters(?int $siteId, ?string $namespace, ?array $keys = null, ?int $languageId = null): bool;
} 