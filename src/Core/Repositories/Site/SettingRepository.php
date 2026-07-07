<?php

namespace App\Core\Repositories\Site;

use App\Core\Models\Site\Setting;
use App\Core\Models\Site\SettingContent;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    protected Model $model;
    protected SettingContent $settingContent;
    public function __construct(PDO $pdo, SettingContent $settingContent)
    {
        parent::__construct($pdo, 'setting', Setting::class);
        $this->model = new Setting();
        $this->model->setDb($pdo);
        $this->settingContent = $settingContent;
        $this->settingContent->setDb($pdo);
        
    }

    /**
     * Get a single setting value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @return string|null
     */
    public function get(?int $siteId, ?string $namespace, string $key): ?string
    {
        $query = $this->model->select(['value'])
            ->where('key', '=', $key);

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        $result = $query->findAll();
        return $result[0]['value'] ?? null;
    }

    /**
     * Set a single setting value
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param string $key
     * @param string $value
     * @return int
     */
    public function set(?int $siteId, ?string $namespace, string $key, string $value): int
    {
        $data = [
            'site_id' => $siteId,
            'namespace' => $namespace,
            'key' => $key,
            'value' => $value
        ];

        // Using upsert to handle "ON DUPLICATE KEY UPDATE"
        $result = $this->model->upsert([$data], ['site_id', 'namespace', 'key']);
        return $result ? 1 : 0;
    }

    /**
     * Get multiple settings at once
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @return array
     */
    public function getMulti(?int $siteId, ?string $namespace, ?array $keys = null): array
    {
        $query = $this->model->select(['namespace', 'key', 'value']);

        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($keys !== null && !empty($keys)) {
            $query->whereIn('key', $keys);
        }

        return $query->findAll();
    }

    /**
     * Set multiple settings at once
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array $settings Key-value pairs of settings
     * @return int
     */
    public function setMulti(?int $siteId, ?string $namespace, array $settings): int
    {
        $data = [];
        foreach ($settings as $key => $value) {
            $data[] = [
                'site_id' => $siteId,
                'namespace' => $namespace,
                'key' => $key,
                'value' => $value
            ];
        }

        // Using upsert to handle "ON DUPLICATE KEY UPDATE"
        $result = $this->model->upsert($data, ['site_id', 'namespace', 'key']);
        return $result ? count($data) : 0;
    }

    /**
     * Delete settings by site, namespace and keys
     * 
     * @param int|null $siteId
     * @param string|null $namespace
     * @param array|null $keys
     * @return bool
     */
    public function deleteByFilters(?int $siteId, ?string $namespace, ?array $keys = null): bool
    {
        $query = $this->model;

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($keys !== null && !empty($keys)) {
            $query->whereIn('key', $keys);
        }

        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        // Execute the delete query
        return $query->delete($query->getId());
    }
    // get email settings
    public function getEmailSettings(): array
    {
        $query = $this->model->where('`key`', '=', 'email_setting')->first();
        if(!$query){
            return [];
        }

        $result = $query->data;
        $result = json_decode($result->value, true);
        return $result;
    }

    // create email settings
    public function createEmailSettings(array $data): array
    {
        try {
        $key = $data['key'];
        $languageId = $data['language_id'];
        $siteId = $data['site_id'];
        $namespace = 'email_setting';
        unset($data['key'], $data['site_id']);
        $values = json_encode($data);
        // data prepare for upsert
        $data = [
            'site_id' => $siteId,
            'key' => $key,
            'namespace' => $namespace,
            'value' => $values
        ];
        $this->db->beginTransaction();
        $result = $this->model->upsert([$data], ['site_id', 'namespace', 'key']);
        // upsert content table
        $contentData = [
            'site_id' => $siteId,
            'language_id' => $languageId,
            'key' => $key,
            'namespace' => $namespace,
            'value' => $values
        ];
        $result = $this->settingContent->upsert([$contentData], ['site_id', 'language_id', 'namespace', 'key']);
        $this->db->commit();

        return $this->getEmailSettings();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to create email settings: ' . $e->getMessage());
        }
    }
} 