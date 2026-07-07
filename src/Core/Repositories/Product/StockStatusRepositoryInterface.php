<?php

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface StockStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all stock statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single stock status by ID
     * 
     * @param int $stockStatusId
     * @return array|null
     */
    public function get(int $stockStatusId): ?array;

    
} 