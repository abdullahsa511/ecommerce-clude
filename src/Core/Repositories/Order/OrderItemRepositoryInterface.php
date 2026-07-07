<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\OrderItem;
use App\Core\Models\Order\OrderItemData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface OrderItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new quote
     *
     * @param QuoteData $quoteData
     * @return QuoteItem
     */
    public function createOrderItem(array $orderItems): array;

    /**
     * Update an existing quote
     *
     * @param QuoteData $quoteData
     * @return QuoteItem
     */
    public function updateOrderItem(OrderItemData $orderItemData): OrderItem;

    /**
     * Get a quote by ID
     *
     * @param int $quoteId
     * @return QuoteItem
     */
    public function showOrderItem(int $orderId): OrderItem;

    public function productList(string $search): array;

} 