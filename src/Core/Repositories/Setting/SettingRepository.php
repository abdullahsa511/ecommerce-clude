<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\Site\Setting;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'setting', Setting::class);
    }

    public function findBySiteAndKey(int $site_id, string $namespace, string $key): ?Setting
    {

        $model = $this->model->where('site_id', '=', $site_id)
        ->where('namespace', '=', $namespace)
        ->where('key', '=', $key)
        ->limit(1);


        $setting = $model->executeQuery($model->getQuery(false));
        if (!empty($setting)) {
            $setting = $model->set($setting[0]);
            return $setting;
        }
        return null;
        
    }

    public function findBySite(int $site_id): array
    {
        $model = $this->model->where('site_id', '=', $site_id);
        $settings = $model->executeQuery($model->getQuery(false));
        
        if (!empty($settings)) {
            return array_map([$this->model, 'set'], $settings);
        }
        
        return [];
    }

    public function findByNamespace(string $namespace): array
    {
        $model = $this->model->where('namespace', '=', $namespace);
        $settings = $model->executeQuery($model->getQuery(false));
        
        if (!empty($settings)) {
            return array_map([$this->model, 'set'], $settings);
        }
        
        return [];
    }
} 