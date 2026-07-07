<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Order\ReturnReason;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ReturnReasonStatusRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?ReturnReason;
    public function importStatuses(string $csv_file, $primaryKey): array;
} 