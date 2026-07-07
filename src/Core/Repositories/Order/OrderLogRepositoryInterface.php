<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\OrderLog;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface OrderLogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all order logs with pagination
     * 
     * @param int|null $order_id Order ID (optional)
     * @param int $start Start offset
     * @param int $limit Number of records to return
     * @return array{data: array, total: int}
     */
    public function getAll(?int $order_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single order log by ID
     * 
     * @param int $order_log_id Order Log ID
     * @return OrderLog|null
     */
    public function get(int $order_log_id): ?OrderLog;

    
} 