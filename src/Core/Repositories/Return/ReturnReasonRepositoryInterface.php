<?php

declare(strict_types=1);

namespace App\Core\Repositories\Return;

use App\Core\Models\Order\ReturnReason;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface ReturnReasonRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $language_id): array;
    public function findByName(string $name, int $language_id): ?ReturnReason;
} 