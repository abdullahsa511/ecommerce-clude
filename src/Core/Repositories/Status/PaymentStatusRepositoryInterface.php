<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Order\PaymentStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PaymentStatusRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?PaymentStatus;
    public function importStatuses(string $csv_file, $primaryKey): array;
} 