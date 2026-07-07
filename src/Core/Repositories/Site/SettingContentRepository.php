<?php

namespace App\Core\Repositories\Site;

use App\Core\Models\Site\SettingContent;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class SettingContentRepository extends BaseRepository implements SettingContentRepositoryInterface
{
    protected Model $model;

    public function __construct(SettingContent $model) 
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Get a single setting content value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @param int|null $languageId
     * @return array|null
     */
    public function get(?int $siteId, ?string $namespace, string $key, ?int $languageId = null): ?array
    {
        $query = $this->model->select(['key', 'value'])
            ->where('key', '=', $key);

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        $result = $query->findAll();
        return !empty($result) ? $result[0] : null;
    }

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
    public function set(?int $siteId, ?string $namespace, string $key, string $value, ?int $languageId = null): int
    {
        $data = [
            'site_id' => $siteId,
            'namespace' => $namespace,
            'key' => $key,
            'value' => $value,
            'language_id' => $languageId
        ];

        // Using upsert to handle "ON DUPLICATE KEY UPDATE"
        $result = $this->model->upsert([$data], ['site_id', 'namespace', 'key', 'language_id']);
        return $result ? 1 : 0;
    }

    /**
     * Get multiple setting contents at once
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @param int|null $languageId
     * @return array
     */
    public function getMulti(?int $siteId, ?string $namespace, ?array $keys = null, ?int $languageId = null): array
    {
        $query = $this->model->select(['site_id', 'namespace', 'key', 'value', 'language_id']);

        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($keys !== null && !empty($keys)) {
            $query->whereIn('key', $keys);
        }

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        return $query->findAll();
    }

    /**
     * Set multiple setting contents at once
     * 
     * @param int|null $siteId
     * @param array $meta Array of setting content data
     * @return int
     */
    public function setMulti(?int $siteId, array $meta): int
    {
        // Ensure site_id is set for each record
        foreach ($meta as &$item) {
            $item['site_id'] = $siteId;
        }

        // Using upsert to handle "ON DUPLICATE KEY UPDATE"
        $result = $this->model->upsert($meta, ['site_id', 'namespace', 'key', 'language_id']);
        return $result ? count($meta) : 0;
    }

    /**
     * Delete setting contents by filters
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @param int|null $languageId
     * @return bool
     */
    public function deleteByFilters(?int $siteId, ?string $namespace, ?array $keys = null, ?int $languageId = null): bool
    {
        $query = $this->model;

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($keys !== null && !empty($keys)) {
            $query->whereIn('key', $keys);
        }

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        // Execute the delete query
        return $query->delete($query->getId());
    }

} 