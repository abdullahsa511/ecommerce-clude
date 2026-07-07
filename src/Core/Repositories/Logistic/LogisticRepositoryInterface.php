<?php

declare(strict_types=1);

namespace App\Core\Repositories\Logistic;

use App\Core\Models\Order\Order;
use App\Core\Models\Order\OrderData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface LogisticRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrder(OrderData $orderData): Order;

    public function updateOrder(OrderData $orderData): Order;

    public function showOrder(int $orderId): array;

    public function insertOrders(array $data): bool;

    /**
     * Get customer orders for component
     *
     * @param array $params
     * @param array $params['customer_id']
     * @param array $params['model']
     * @param array $params['fields']
     * @param array $params['item_count']
     * @param array $params['joins']
     * @return array
     */
    public function getCustomerOrdersForComponent(array $params): array;
    /**
     * Get the recent orders widget data
     *
     * @param int $limit
     * @return array
     */
    public function getRecentOrdersWidget($limit = 20): array;
    public function getOrderById(int $id): array;
    /**
     * Get order tracking
     *
     * @param string $orderNumber
     * @return array
     */
   
    public function getCustomerLogisticDatesForComponent(array $params): array;
} 