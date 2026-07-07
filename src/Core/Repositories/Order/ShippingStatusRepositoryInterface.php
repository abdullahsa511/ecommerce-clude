<?php

namespace App\Core\Repositories\Order;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ShippingStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all shipping statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single shipping status by ID
     * 
     * @param int $shippingStatusId
     * @return array|null
     */
    public function get(int $shippingStatusId): ?array;
    public function importStatuses(string $csv_file, $primaryKey): array;

    
} 