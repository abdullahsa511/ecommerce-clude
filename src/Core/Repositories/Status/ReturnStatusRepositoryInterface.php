<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Order\ReturnStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface ReturnStatusRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?ReturnStatus;
    public function importStatuses(string $csv_file, $primaryKey): array;
} 