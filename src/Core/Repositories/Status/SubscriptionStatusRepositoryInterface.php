<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Subscription\SubscriptionStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SubscriptionStatusRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?SubscriptionStatus;
    public function importStatuses(string $csv_file, $primaryKey): array;
    // update data
    public function updateSubscriptionStatus(array $data, int $id): array;
} 