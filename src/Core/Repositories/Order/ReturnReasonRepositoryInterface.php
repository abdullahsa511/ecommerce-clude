<?php

namespace App\Core\Repositories\Order;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ReturnReasonRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all return reasons with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single return reason by ID
     * 
     * @param int $returnReasonId
     * @return array|null
     */
    public function get(int $returnReasonId): ?array;

} 