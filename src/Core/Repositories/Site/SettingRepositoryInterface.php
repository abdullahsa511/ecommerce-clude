<?php

namespace App\Core\Repositories\Site;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single setting value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @return string|null
     */
    public function get(?int $siteId, ?string $namespace, string $key): ?string;

    /**
     * Set a single setting value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @param string $value
     * @return int
     */
    public function set(?int $siteId, ?string $namespace, string $key, string $value): int;

    /**
     * Get multiple settings at once
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @return array
     */
    public function getMulti(?int $siteId, ?string $namespace, ?array $keys = null): array;

    /**
     * Set multiple settings at once
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array $settings Key-value pairs of settings
     * @return int
     */
    public function setMulti(?int $siteId, ?string $namespace, array $settings): int;

    /**
     * Delete settings by site, namespace and keys
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @return bool
     */
    public function deleteByFilters(?int $siteId, ?string $namespace, ?array $keys = null): bool;
    public function getEmailSettings(): array;
    public function createEmailSettings(array $data): array;
}