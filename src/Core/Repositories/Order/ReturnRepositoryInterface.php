<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ReturnRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all returns with related data
     * @param int $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(
        int $languageId,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get return by ID
     * @param int $returnId
     * @return array|null
     */
    public function get(int $returnId): ?array;

} 