<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\Site\Setting;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySiteAndKey(int $site_id, string $namespace, string $key): ?Setting;
    public function findBySite(int $site_id): array;
    public function findByNamespace(string $namespace): array;
} 