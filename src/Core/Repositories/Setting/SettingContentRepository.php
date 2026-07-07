<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\Site\SettingContent;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Repositories\Setting\SettingContentRepositoryInterface;
use PDO;

class SettingContentRepository extends BaseRepository implements SettingContentRepositoryInterface
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'setting_content', SettingContent::class);
    }

    public function findBySiteAndKey(int $site_id, int $language_id, string $namespace, string $key): ?SettingContent
    {
        $model = $this->model->where('site_id', '=', $site_id)
            ->where('language_id', '=', $language_id)
            ->where('namespace', '=', $namespace)
            ->where('key', '=', $key);

        $settingContent = $model->executeQuery($model->getQuery(false));
        if (!empty($settingContent)) {
            $settingContent = $model->set($settingContent[0]);
            return $settingContent;
        }
        return null;
    }

    public function findBySite(int $site_id): array|null
    {
        $model = $this->model->where('site_id', '=', $site_id);

        $settingContent = $model->executeQuery($model->getQuery(false));
        if (!empty($settingContent)) {
            $settingContent = $model->set($settingContent[0]);
            return (array) $settingContent;
        }
        return [];
    }


    public function findByLanguage(int $site_id, int $language_id): array
    {
        $model = $this->model->where('site_id', '=', $site_id)
            ->where('language_id', '=', $language_id);

        $settingContent = $model->executeQuery($model->getQuery(false));
        if (!empty($settingContent)) {
            $settingContent = $model->set($settingContent[0]);
            return (array) $settingContent;
        }
        return [];
    }

    public function findByNamespace(int $site_id, int $language_id, string $namespace): array
    {
        $model = $this->model->where('site_id', '=', $site_id)
            ->where('language_id', '=', $language_id)
            ->where('namespace', '=', $namespace);

        $settingContent = $model->executeQuery($model->getQuery(false));
        if (!empty($settingContent)) {
            $settingContent = $model->set($settingContent[0]);
            return (array) $settingContent;
        }
        return [];
    }

    

    public function updateBySiteAndKey(int $site_id, int $language_id, string $namespace, string $key, array $data): bool
    {
        if (!isset($data['value'])) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE setting_content 
            SET value = ?
            WHERE site_id = ? AND language_id = ? AND namespace = ? AND `key` = ?
        ");

        return $stmt->execute([
            $data['value'],
            $site_id,
            $language_id,
            $namespace,
            $key
        ]);
    }

    public function deleteBySiteAndKey(int $site_id, int $language_id, string $namespace, string $key): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM setting_content 
            WHERE site_id = ? AND language_id = ? AND namespace = ? AND `key` = ?
        ");
        return $stmt->execute([$site_id, $language_id, $namespace, $key]);
    }
} 