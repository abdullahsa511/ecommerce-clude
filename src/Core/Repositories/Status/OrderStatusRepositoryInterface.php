<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Order\OrderStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface OrderStatusRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?OrderStatus;
    public function importStatuses(string $csv_file, $primaryKey): array;
} 