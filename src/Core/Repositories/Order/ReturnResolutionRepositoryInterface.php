<?php

namespace App\Core\Repositories\Order;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ReturnResolutionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all return resolutions with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single return resolution by ID
     * 
     * @param int $returnResolutionId
     * @return array|null
     */
    public function get(int $returnResolutionId): ?array;

    
} 