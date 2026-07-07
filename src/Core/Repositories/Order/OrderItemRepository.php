<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Product\Product;
use PDO;
use App\Core\Models\Order\OrderItem;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Order\OrderItemData;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    private Product $product;
    public function __construct(PDO $db, Product $product)
    {
        parent::__construct($db, 'order_items', OrderItem::class);
        $this->product = $product;
        $this->product->setDb($db);
    }

    public function createOrderItem(array $orderItems): array
    {
        $mappedItems = array_map(function($item) {
            return [
                'order_id' => $item['order_id'],
                'product_id' => $item['product_id'],
                'description' => $item['item_description'],
                'quantity' => $item['item_quantity'],
                'unit_price' => $item['item_unit_price'],
                'total_price' => $item['item_total'],
                'uuid' => $this->generateUuid(),
                'language_id' => $item['language_id'] ?? 1 // Default language ID if not provided
            ];
        }, $orderItems);

        $this->model->upsert($mappedItems, ['order_item_id', 'language_id']);
        return $mappedItems;
    }
    public function updateOrderItem(OrderItemData $orderItemData): OrderItem
    {
        $orderDataArray = $orderItemData->toArray();
        $order = $this->model->find($orderDataArray['order_id']);
        $order = $order->update($orderDataArray);

        return $order;
    }

    public function showOrderItem(int $orderId): OrderItem
    {
        $order = $this->model->where('order_id', '=', $orderId)
        ->first();

        return $order;
    }

    public function productList(string $search): array
    {
        $result = $this->product
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_content.name', 'LIKE', '%' . $search . '%')
            ->select(['product.product_id', 'product.description', 'product.price', 'product_content.name'])
            ->orderBy('product.product_id', 'DESC')
            ->limit(50)
            ->findAll(false);
        return $result;
    }

    private function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf('%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }
} 