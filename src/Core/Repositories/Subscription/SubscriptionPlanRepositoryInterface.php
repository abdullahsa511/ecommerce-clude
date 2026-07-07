<?php

declare(strict_types=1);

namespace App\Core\Repositories\Subscription;

use App\Core\Models\Subscription\SubscriptionPlan;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SubscriptionPlanRepositoryInterface extends BaseRepositoryInterface
{
    
    public function getActivePlans(): array;
    
    public function getPlanWithTrial(): ?SubscriptionPlan;

    public function getAll(int $languageId = null, int $start = 0, int $limit = 10): array;

    public function get(int $subscriptionId): ?array;

    public function deletePlan(int $id): bool;
} 