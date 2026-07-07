<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Product\StockStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface StockStatusRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?StockStatus;
    public function importStatuses(string $csv_file, $primaryKey): array;
} 