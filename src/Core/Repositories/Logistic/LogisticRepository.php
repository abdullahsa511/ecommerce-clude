<?php

declare(strict_types=1);

namespace App\Core\Repositories\Logistic;

use App\Core\Models\Order\Order;
use App\Core\Models\Order\OrderData;
use App\Core\Models\Order\OrderItem;
use App\Core\Models\Logistic\LogisticStatus;
use App\Core\Models\Logistic\LogisticTypes;
use App\Core\Models\Logistic\LogisticDates;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class LogisticRepository extends BaseRepository implements LogisticRepositoryInterface
{
    private OrderItem $orderItem;
    private LogisticStatus $logisticStatus;
    private LogisticTypes $logisticTypes;

    public function __construct(PDO $db)
    {
        parent::__construct($db, 'logistic_dates', LogisticDates::class);
        $this->logisticStatus = new LogisticStatus();
        $this->logisticStatus->setDb($db);
        $this->logisticTypes = new LogisticTypes();
        $this->logisticTypes->setDb($db);
    }

    public function createOrder(OrderData $orderData): Order
    {
        $orderDataArray = $orderData->toArray();
        $order = $this->model->create($orderDataArray);
        return $order;
    }
    public function updateOrder(OrderData $orderData): Order
    {
        $orderDataArray = $orderData->toArray();
        $order = $this->model->find($orderDataArray['order_id']);
        $order = $order->update($orderDataArray);

        return $order;
    }

    public function showOrder(int $orderId): array
    {

        $order = $this->model->where('order_id', '=', $orderId)->first();

        $order_items = $this->orderItem
            ->join('item', 'item.item_id', '=', 'order_items.item_id')
            ->join('product', 'product.product_id', '=', 'order_items.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('order_items.order_id', '=', $orderId)
            ->select([
                'order_items.*',
                'product.product_id',
                'product.image',
                'product.product_code',
                'product_content.name',
                'item.item_id',
                'item.km_item_id',
                'item.item_code',
                'item.description',
                'item.quote_image'
         
            ])
            ->findAll();
               
        $orderData = $order->data;
    
        // Calculate totals
        $subTotal = 0;
        $orderItems = [];
        foreach($order_items as $item){
            $quote_image = isset($item['quote_image']) ? $item['quote_image'] : '';

            $orderItems[] = [
                'item_id' => $item['item_id'],
                'km_item_id' => $item['km_item_id'],
                'item_code' => $item['item_code'],
                'image' => $quote_image,
                'alt' => $item['name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => '$' .$item['unit_price'],
                'item_total' => '$' .$item['total_price'],
                'comment_icon' => '/img/datatable/comment-icon.png'
            ];
            $subTotal += $item['total_price'];
        }
        $gst = $subTotal * 0.10; // 10% GST example
        $grandTotal = $subTotal + $gst;
    
        $show_order_summary = [
            'title' => 'Order Title',
            'id' => '#' . $orderData->order_id,
            'description' => $orderData->order_description,
            'account' => $order->reference_number,
            'amount' => '$' . $grandTotal,
            'currency' => $orderData->currency,
            'order_total' => $orderData->total,
            'currency_value' => $orderData->currency_value,
            'notes' => $orderData->notes,
            'created_date' => date('M d, Y', strtotime($orderData->created_at)),
        ];

        return [
            'page_title' => 'Show Order',
            'show_order_summary' => $show_order_summary,
            'table' => [
                'section_title' => 'Items',
                'section_total' => '$' . $subTotal,
                'items' => $orderItems
            ],
            'footer' => [
                'sub_total' => '$' . $subTotal,
                'gst' => '$' . $gst . ' (10%)',
                'total_inc_gst' => '$' . $grandTotal
            ],
        ];
    }
    
    public function insertOrders(array $data): bool 
    {
        $orders = $data['orders'];
        $orderItems = $data['orderItems'];
        $this->db->beginTransaction();
        $this->model->insert($orders);

        $this->orderItem->insert($orderItems);
        
        $this->db->commit();
        return true;
    }

    public function getCustomerOrdersForComponent(array $params): array
    {
        $customerId = $params['customer_id'];
        $sort_field = '?sort=created_at&order=desc';
        $query = $this->model
            ->join('order_items', 'order_items.order_id', '=', 'order.order_id')
            ->join('item', 'item.item_id', '=', 'order_items.item_id')
            ->join('product', 'product.product_id', '=', 'order_items.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('order.customer_id', '=', $customerId)
            ->select([
                'order.order_id',
                'order_items.quantity',
                'order_items.options', 
                'item.item_id',
                'item.km_item_id',
                'item.item_code',
                'item.description as item_description',
                'item.quote_image',
                'product.product_code',
                'product_content.name',
                'product.description as product_description',
                'product.image',
            ]);
            if(isset($params['sort']) && isset($params['order'])){
                $sort_field = '?sort='.strtolower($params['sort']).'&order='.strtolower($params['order']);
                $query->orderBy('order.'.$params['sort'], $params['order']);
            }
            $query->groupBy('order.order_id');
            $orders = $query->findAll();


            $orders_data = [];
            foreach($orders as $order){
                $image = isset($order['image']) ? json_decode($order['image'], true) : null;
                $product_image = isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
                $order_id = isset($order['order_id']) ? $order['order_id'] : null;
                $product_name = isset($order['name']) ? $order['name'] : null;
                $item_code = isset($order['item_code']) ? $order['item_code'] : null;
                $item_description = isset($order['item_description']) ? $order['item_description'] : null;
                $quantity = isset($order['quantity']) ? $order['quantity'] : null;
                $item_options = isset($order['options']) ? json_decode($order['options'], true) : null;
                $quote_image = isset($order['quote_image']) ? $order['quote_image'] : null;
                $option_name = null;
                if($item_options){
                    $item_options = $item_options['variant']['item']['options'];
                    if($item_options){
                        $option_name = $item_options[0]['option_name'];
                    }
                }
    
                $orders_data[] = [
                    'id' => $order_id,
                    'image' => $quote_image ?? '/img/account-dashboard/recent-order1.png',
                    'alt' => $product_name ?? 'order-item',
                    'product_name' => $item_description,
                    'item_code' => $item_code ?? 'N/A',
                    'color' => $option_name ?? 'N/A',
                    'quantity' => $quantity ?? '0',
                    'track_order_url' => '#',
                    'track_order_target' => '#offcanvasRightTop',
                    'view_details_url' => '/account/orders/'.$order_id,
                    'view_details_target' => '#offcanvasRightTop',
                    'offcanvas_id' => 'offcanvasRightTop'
                ];
            }

            $sort_options  = $this->getSortOptions();
            $sort_text = $sort_options[array_search($sort_field, array_column($sort_options, 'url'))]['text'] ?? 'Sort';

            if (empty($orders)) {
                return [
                    'page_title' => 'Recent Orders',
                    'sort_options' => $sort_options ,
                    'sort_button_text' => $sort_text,
                    'orders' => [],
                    'message' => 'No orders found'
                ];
            }

            $results = [
                'page_title' => 'Recent Orders',
                'sort_options' => $sort_options ,
                'sort_button_text' => $sort_text,
                'orders' => $orders_data
            ];

        

        return $results;
    }

    /**
     * Get item code for the order item
     */
    private function getItemCode($order, $item): string
    {
        // This could be enhanced to pull from product table
        return $item->product_id ? 'PROD-' . $item->product_id : 'ITEM-' . $item->order_item_id;
    }

    /**
     * Get item color (placeholder - could be enhanced to pull from product options)
     */
    private function getItemColor($item): string
    {
        // This could be enhanced to pull from product options table
        return 'Standard';
    }

    public function getRecentOrdersWidget($limit = 20): array
    {
        // Quote Reference, Description, Customer, Status, Created Date, Updated Date, Amount
        // SQL (prepared once, reused) // parameters: limit.
        $orders = $this->model
        ->join('user', 'user.user_id', '=', 'order.user_id')
        ->join('customer', 'customer.customer_id', '=', 'order.customer_id')
        ->join('order_status', 'order_status.order_status_id', '=', 'order.order_status_id')
        ->select([
            'customer.name as customer_name',
            'order.order_id as id',
            'order.reference_number as reference',
            'order.order_description as description',
            'DATE_FORMAT(order.created_at, "%M %d, %Y") as date',
            'order.total as amount',
            'CONCAT(user.first_name," ", user.last_name) as manager_name',
            'order.updated_at as updated_at',
            'order_status.name as status'
         ])
        // ->where('order_status_id', '=', 1)
        ->orderBy('order.created_at', 'DESC')
        ->limit($limit)
        ->findAll();
        // return the recent orders widget data
        return $orders;
    }

    public function getOrderById(int $id): array
    {
        $orders = $this->model
        ->join('user', 'user.user_id', '=', 'order.user_id')
        ->join('customer', 'customer.user_id', '=', 'user.user_id')
        ->select(['customer.name as customer_name','order.order_id as id','order.reference_number as reference','order.order_description as description', 'DATE_FORMAT(order.created_at, "%M %d, %Y") as date','order.total as amount','CONCAT(user.first_name," ", user.last_name) as manager_name','order.updated_at as updated_at',
            '(CASE
                WHEN order.order_status_id = 1 THEN "pending"
                WHEN order.order_status_id = 2 THEN "processing"
                WHEN order.order_status_id = 3 THEN "processed"
                WHEN order.order_status_id = 4 THEN "complete"
                WHEN order.order_status_id = 5 THEN "canceled"
                WHEN order.order_status_id = 6 THEN "archived"
                WHEN order.order_status_id = 7 THEN "requires_action"
                ELSE "no status"
            END) as status'
         ])
        ->where('order.order_id', '=', $id)
        ->orderBy('order.created_at', 'DESC')
        ->findAll();
        $order = $orders[0];
        $items = $this->orderItem
        ->select(['order_items.*', 'product.product_code','product.description','product.price as product_price','product.image'])
        ->join('product', 'product.product_id', '=', 'order_items.product_id')
        ->where('order_id', '=', $id)
        ->findAll();

        foreach ($items as &$item) {
            if (!empty($item['image'])) {
                $item['image'] = json_decode($item['image'], true);
    
                if (is_array($item['image']) && isset($item['image'][0]['objectURL'])) {
                    $item['image_url'] = $item['image'][0]['objectURL'];
                }
            } else {
                $item['image'] = [];
            }
            unset($item['image']);
        }

        $order['items'] = $items;
        return $order;
    }

    public function getOrderTracking(string $orderNumber): array
    {
        $this->model->clearQuery();
        $query = $this->model
        ->select(['`order`.*'])
        ->with([
            // Order Items
            'orderItems' => function ($query) use ($orderNumber) {
                $query->join('item', 'item.item_id', '=', 'order_items.item_id')
                    ->join('product', 'product.product_id', '=', 'order_items.product_id')
                    ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
                    ->where('order_items.order_id', '=', $orderNumber)
                    // ->orWhere('order_items.order_id', '=', $orderNumber)
                    ->select([
                        'order_items.order_id',
                        'item.item_code',
                        'item.description as item_description',
                        'product.product_code',
                        'product.description as product_description',
                        'product.image'
                    ]);
            }
        ])
        ->where('order.invoice_no', '=', $orderNumber)
        ->orWhere('order.order_id', '=', $orderNumber)
        ->orderBy('order.order_id', 'DESC')
        ->orderBy('order.created_at', 'DESC')
        ->first();

        if (!$query) {
            return [];
        }

        $result = (array) $query->data;
        // retrive  order status data
        $orderStatus = $this->orderStatus->select(['order_status_id','name'])->limit(5)->findAll();

        // Add tracking info
        $tracking = [];
        foreach ($orderStatus as $status) {
            $tracking[] = [
                'order_status_id' => $status['order_status_id'],
                'name' => $status['name'],
                'completed' => $result['order_status_id'] >= $status['order_status_id']
            ];
        }
        $result['tracking'] = $tracking;

        // Add order items info
        if (isset($result['orderItems'])) {
            $orderItems = json_decode($result['orderItems'], true);
            $result['items'] = is_array($orderItems) ? $orderItems : [];
            unset($result['orderItems']);
        } else {
            $result['items'] = [];
        }

        return $result;
    }

    public function getCustomerLogisticDatesForComponent(array $params): array
    {
        $customerId = $params['customer_id'];
        $sort_field = '?sort=created_at&order=desc';

        $this->model->clearQuery();
        $query = $this->model
            ->join('`order`', '`order`.order_id', '=', 'logistic_dates.order_id')
            ->join('logistic_types', 'logistic_types.logistic_types_id', '=', 'logistic_dates.logistic_types_id')
            ->join('logistic_statuses', 'logistic_statuses.logistic_statuses_id', '=', 'logistic_dates.logistic_statuses_id')
            ->where('logistic_dates.customer_id', '=', $customerId)
            ->select([
                '`order`.order_id',
                '`order`.uuid',
                'concat("#", `order`.invoice_no) as order_number',
                '`order`.customer_order_id',
                '`order`.order_description',
                '`order`.created_at',
                '`order`.updated_at',
                'logistic_types.name as title', 
                'logistic_statuses.name as subtitle', 
                'logistic_dates.date',
                'logistic_dates.expected_start as time', 
                'logistic_dates.expected_end', 
                'logistic_dates.actual_start', 
                'logistic_dates.actual_end'
            ]);
            if(isset($params['sort']) && isset($params['order'])){
                $sort_field = '?sort='.strtolower($params['sort']).'&order='.strtolower($params['order']);
                $query->orderBy('logistic_dates.'.$params['sort'], $params['order']);
                $query->orderBy('`logistic_dates`.logistic_dates_id', $params['order']);
            }
           
            $logistic_dates = $query->findAll();

            $sort_options  = $this->getSortOptions();
            $sort_text = $sort_options[array_search($sort_field, array_column($sort_options, 'url'))]['text'] ?? 'Sort';

            $results = [
                'page_title' => 'Logistic Dates',
                'sort_options' => $sort_options,
                'sort_button_text' => $sort_text,
                'items' => $logistic_dates
            ];

        return $results;
    }

    private function getSortOptions(): array
    {
        return [
            [
                'text' => 'Sort by Date (Newest)',
                'url' => '?sort=created_at&order=desc'
            ],
            [
                'text' => 'Sort by Date (Oldest)',
                'url' => '?sort=created_at&order=asc'
            ]
        ];
    }
} 