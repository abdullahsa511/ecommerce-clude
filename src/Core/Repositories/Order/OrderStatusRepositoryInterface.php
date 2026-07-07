<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\OrderStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface OrderStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all order statuses
     *
     * @param int|null $language_id
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAll(?int $language_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single order status
     *
     * @param int $order_status_id
     * @return OrderStatus|null
     */
    public function get(int $order_status_id): ?OrderStatus;

    
} 