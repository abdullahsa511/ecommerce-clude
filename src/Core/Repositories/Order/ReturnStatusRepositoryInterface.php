<?php

namespace App\Core\Repositories\Order;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ReturnStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all return statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single return status by ID
     * 
     * @param int $returnStatusId
     * @return array|null
     */
    public function get(int $returnStatusId): ?array;

    
} 