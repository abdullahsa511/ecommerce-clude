<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\Order;
use App\Core\Models\Order\OrderData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getIdByUuid(string $uuid): ?Order;
    
    public function createOrder(OrderData $orderData): Order;

    public function updateOrder(OrderData $orderData): Order;

    public function showOrder(string $uuid): array;

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
    public function getOrderTracking(string $orderNumber): array;
    /**
     * Get order by uuid
     *
     * @param string $uuid
     * @return array
     */
    public function getOrderByUuid(string $uuid): array;

    public function findByReferenceNumber(string $referenceNumber): ?Order;
} 