<?php

declare(strict_types=1);

namespace App\Core\Repositories\Setting;

use App\Core\Models\Site\SettingContent;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SettingContentRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySiteAndKey(int $site_id, int $language_id, string $namespace, string $key): ?SettingContent;
    public function findBySite(int $site_id): array|null;
    public function findByLanguage(int $site_id, int $language_id): array;
    public function findByNamespace(int $site_id, int $language_id, string $namespace): array;
    public function updateBySiteAndKey(int $site_id, int $language_id, string $namespace, string $key, array $data): bool;
    public function deleteBySiteAndKey(int $site_id, int $language_id, string $namespace, string $key): bool;
} 