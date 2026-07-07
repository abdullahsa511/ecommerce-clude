<?php

declare(strict_types=1);

namespace App\Core\Repositories\Model;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Model\Model;

interface ModelRepositoryInterface extends BaseRepositoryInterface
{
    public static function getModels(): array;
    public function getTableColumns(string $className): array;
    public static function getRelatedModels(string $modelName): ?array;
    public function getJoinedTableColumns(string $mainModelClass, string $relatedModelClass, string $joinType = 'left'): array;
} 