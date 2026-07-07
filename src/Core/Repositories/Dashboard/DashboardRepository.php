<?php

declare(strict_types=1);

namespace App\Core\Repositories\Dashboard;

use App\Core\Exceptions\ValidationException;
use PDO;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Dashboard\Dashboard;
use App\Core\Models\Order\OrderItem;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use DateTime;
use App\Core\Repositories\Quote\QuoteRepositoryInterface;
use App\Core\Repositories\Order\OrderRepositoryInterface;

class DashboardRepository extends BaseRepository implements DashboardRepositoryInterface
{
    private PinboardRepositoryInterface $pinboardRepository;
    private QuoteRepositoryInterface $quoteRepository;
    private OrderRepositoryInterface $orderRepository;
    private OrderItem $orderItem;

    public function __construct(
        PDO $db,
        PinboardRepositoryInterface $pinboardRepository,
        QuoteRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        OrderItem $orderItem
    ) {
        parent::__construct($db, 'dashboard', Dashboard::class);
        $this->pinboardRepository = $pinboardRepository;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItem = $orderItem;
        $this->orderItem->setDb($db);
    }

    // 1. dashboard/revenue-card-widget
    /**
     * Get the revenue card widget data
     *
     * @return array
     */
    public function getRevenueCardWidgetData(): array
    {
        // pinboard opens // parameter: title, table, sum column, status column, status id, card type [pending status id = 1]
        $pinboardOpens = $this->revenueCardWidget('Open Pinboards', 'pinboard', 'grand_total_sp_inc_gst', 'pinboard_status_id', 1, 'pinboard');
        // quote opens // parameter: title, table, sum column, status column, status id, card type [pending status id = 1]
        $quoteOpens = $this->revenueCardWidget('Open Quotes', 'quote', 'grand_total_sp_inc_gst', 'quote_status_id', 1, 'quote');
        // order opens // parameter: title, table, sum column, status column, status id, card type [pending status id = 1]
        $orderOpens = $this->revenueCardWidget('Open Orders','order', 'total', 'order_status_id', 1, 'order');
        // monthly revenue // parameter: title, table, sum column, status column, status id, card type [complete status id = 4]
        $monthlyRevenue = $this->revenueCardWidget('Monthly Revenue', 'order', 'total', 'order_status_id', 4, 'monthly_revenue');
        // data array
        $data = [ $pinboardOpens, $quoteOpens, $orderOpens, $monthlyRevenue ];
        // return the data array
        return $data;
    }

    // 2. dashboard/overview-widget
    /**
     * Get the overview widget data
     *
     * @return array
     */
    public function getOverviewWidgetData(string $type = 'weekly'): array
    {

        // define weeks array
        $weeks = [];
        // now date
        $now = new DateTime();

        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $now)->modify("-$i week")->modify('monday this week');
            // get the end date of the week
            $end   = (clone $start)->modify('sunday this week');

            // add the week to the weeks array
            $weeks[] = [
                'start' => $start->format('Y-m-d'),
                'end'   => $end->format('Y-m-d')
            ];
        }

        // Calculate overall date range for single query
        $overallStart = $weeks[0]['start'];
        $overallEnd = $weeks[count($weeks) - 1]['end'];

        // Single query to retrieve all data: order items with their dates, categories, and top-level category mapping
        // This query uses a recursive CTE to map each category to its top-level parent
        // and retrieves all order items within the date range
        $sql = "WITH RECURSIVE category_hierarchy AS (
                -- Base case: Get all top-level categories (parent_id IS NULL)
                SELECT 
                    ti.taxonomy_item_id AS top_category_id,
                    ti.taxonomy_item_id AS category_id,
                    ti.name AS top_category_name,
                    0 AS level
                FROM taxonomy_item ti
                JOIN taxonomy t ON t.taxonomy_id = ti.taxonomy_id
                WHERE t.taxonomy_id = 1
                AND ti.parent_id IS NULL
                
                UNION ALL
                
                -- Recursive case: Get all children of categories in the hierarchy
                SELECT 
                    ch.top_category_id,
                    ti.taxonomy_item_id AS category_id,
                    ch.top_category_name,
                    ch.level + 1 AS level
                FROM category_hierarchy ch
                JOIN taxonomy_item ti ON ti.parent_id = ch.category_id
                JOIN taxonomy t ON t.taxonomy_id = ti.taxonomy_id
                WHERE t.taxonomy_id = 1
            )
            SELECT 
                DATE(oi.created_at) AS order_date,
                ch.top_category_id AS category_id,
                ch.top_category_name AS product_category,
                oi.total_price
            FROM order_items oi
            JOIN product p ON p.product_id = oi.product_id
            JOIN product_to_taxonomy_item ptx ON ptx.product_id = p.product_id
            JOIN category_hierarchy ch ON ch.category_id = ptx.taxonomy_item_id
            WHERE DATE(oi.created_at) BETWEEN :start AND :end
            ORDER BY order_date ASC, ch.top_category_id ASC";

        // Execute single query to get all data
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start' => $overallStart,
            ':end'   => $overallEnd
        ]);
        $allResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group data by week and by top category in PHP
        $groupedData = [];
        
        // Initialize structure for all weeks
        foreach ($weeks as $week) {
            $weekKey = $week['start'];
            $groupedData[$weekKey] = [
                'week' => date('d M Y', strtotime($week['start'])),
                'week_start' => $week['start'],
                'week_end' => $week['end'],
                'categories' => []
            ];
        }

        // Group results by week and category
        foreach ($allResults as $row) {
            $orderDate = $row['order_date'];
            $categoryId = $row['category_id'];
            $categoryName = $row['product_category'];
            $totalPrice = (float) $row['total_price'];

            // Find which week this order belongs to
            foreach ($weeks as $week) {
                if ($orderDate >= $week['start'] && $orderDate <= $week['end']) {
                    $weekKey = $week['start'];
                    
                    // Initialize category if not exists
                    if (!isset($groupedData[$weekKey]['categories'][$categoryId])) {
                        $groupedData[$weekKey]['categories'][$categoryId] = [
                            'category_id' => $categoryId,
                            'parent_name' => $categoryName,
                            'parent_id' => null,
                            // 'parent_name' => null,
                            'total_amount' => 0
                        ];
                    }
                    
                    // Sum the total amount for this category in this week
                    $groupedData[$weekKey]['categories'][$categoryId]['total_amount'] += $totalPrice;
                    break;
                }
            }
        }

        // Convert to array format, sort by total_amount, and limit to top 3 per week
        $data = [];
        foreach ($weeks as $week) {
            $weekKey = $week['start'];
            $categories = array_values($groupedData[$weekKey]['categories']);
            
            // Sort by total_amount descending
            usort($categories, function($a, $b) {
                return $b['total_amount'] <=> $a['total_amount'];
            });
            
            // Limit to top 3 categories
            $categories = array_slice($categories, 0, 3);
            
            $data[] = [
                'week' => $groupedData[$weekKey]['week'],
                'week_start' => $groupedData[$weekKey]['week_start'],
                'week_end' => $groupedData[$weekKey]['week_end'],
                'categories' => $categories
            ];
        }

        return $data;
    }
    public function getOverviewWidgetData_new(string $type = 'weekly'): array
    {   
        $weeks = [];
        $now = new DateTime();

        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $now)->modify("-$i week")->modify('monday this week');
            $end   = (clone $start)->modify('sunday this week');

            $weeks[] = [
                'start' => $start->format('Y-m-d'),
                'end'   => $end->format('Y-m-d')
            ];
        }

        // SQL (prepared once, reused)
        $sql = "SELECT 
                oi.*, 
                ptx.taxonomy_item_id, 
                ti.name AS product_category, 
                t.name AS taxonomy_name, 
                ti.parent_id,
                (SELECT name 
                 FROM taxonomy_item 
                 WHERE taxonomy_item_id = ti.parent_id
                 LIMIT 1) AS parent_name,
                 SUM(oi.quantity) AS quantity,
                 SUM(oi.total_price) AS total_price
            FROM order_items oi
            JOIN product p ON p.product_id = oi.product_id
            JOIN product_to_taxonomy_item ptx ON p.product_id = ptx.product_id
            JOIN taxonomy_item ti ON ti.taxonomy_item_id = ptx.taxonomy_item_id
            JOIN taxonomy t ON t.taxonomy_id = ti.taxonomy_id
            WHERE t.taxonomy_id = 1
            AND DATE(oi.created_at) BETWEEN :start AND :end
        ";

        $categorySql = "SELECT 
                ti.taxonomy_item_id AS category_id,
                ti.parent_id AS parent_id,
                ti.name AS product_category,
                txp.name AS parent_name,
                SUM(oi.total_price) AS total_amount
            FROM order_items oi
            JOIN product p ON p.product_id = oi.product_id
            JOIN product_to_taxonomy_item ptx ON p.product_id = ptx.product_id
            JOIN taxonomy_item ti ON ti.taxonomy_item_id = ptx.taxonomy_item_id
            LEFT JOIN taxonomy_item txp ON txp.taxonomy_item_id = ti.parent_id
            JOIN taxonomy t ON t.taxonomy_id = ti.taxonomy_id
            WHERE t.taxonomy_id = 1
            AND DATE(oi.created_at) BETWEEN :start AND :end
            GROUP BY 
                -- ti.taxonomy_item_id,
                ti.parent_id,
                txp.name
            ORDER BY total_amount DESC
            LIMIT 3";
        // $stmt = $this->db->prepare($sql);
        $categoryStmt = $this->db->prepare($categorySql);

        $chartData = [];

        foreach ($weeks as $week) {

            // execute queries
            // $stmt->execute([
            //     ':start' => $week['start'],
            //     ':end'   => $week['end']
            // ]);

            $categoryStmt->execute([
                ':start' => $week['start'],
                ':end'   => $week['end']
            ]);

            $categoryResult = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

            // y-axis values (top 3 categories)
            $yValues = [];

            foreach ($categoryResult as $cat) {
                $yValues[] = (float) $cat['total_amount'];
            }

            // ensure always 3 values (chart safety)
            while (count($yValues) < 3) {
                $yValues[] = 0;
            }

            $chartData[] = [
                'x' => date('Y-m-d', strtotime($week['start'])),
                'y' => $yValues
            ];
        }

        return $chartData;
    }
    // 3. dashboard/pinboards-widget
    /**
     * Get the pinboards widget data
     *
     * @param int $limit
     * @return array
     */
    public function getPinboardsWidgetData(int $limit = 14): array
    {
        // get the pinboards widget data
        $pinboards = $this->pinboardRepository->getPinboardWidget($limit);
        // return the pinboards widget data
        return $pinboards;
    }
    // 4. dashboard/recent-quotes-widget
    /**
     * Get the recent quotes widget data
     *
     * @param int $limit
     * @return array
     */
    public function getRecentQuotesWidgetData($limit = 20): array
    {
        // get the recent quotes widget data
        $quotes = $this->quoteRepository->getRecentQuotesWidget($limit);
        // return the recent quotes widget data

        return $quotes;
    }
    // 5. dashboard/recent-orders-widget
    /**
     * Get the recent orders widget data
     *
     * @param int $limit
     * @return array
     */
    public function getRecentOrdersWidgetData($limit = 20): array
    {
        // get the recent orders widget data
        $orders = $this->orderRepository->getRecentOrdersWidget($limit);
        // return the recent orders widget data
        return $orders;
    }

    /**
     * Example return data
     * $cardData = [
            'card_type' => 'pinboard',
            'title' => 'Open Pinboards',
            'analytics' => 'Last 9 Weeks Pinboards Analytics',
            'value' => 'AUD ' . number_format(500000, 2, ',', '.'),
            'percent' => '64'
        ];
        $data = [ 
            '1st week 2025-10-01 - 2025-10-07' . ' /AUD '. number_format(12030, 2), 
            '2nd week 2025-10-08 - 2025-10-14' . ' /AUD '. number_format(26045, 2), 
            '3rd week 2025-10-15 - 2025-10-21' . ' /AUD '. number_format(14560, 2), 
            '4th week 2025-10-22 - 2025-10-28' . ' /AUD '. number_format(24075, 2), 
            '5th week 2025-10-29 - 2025-11-04' . ' /AUD '. number_format(18789, 2), 
            '6th week 2025-11-05 - 2025-11-11' . ' /AUD '. number_format(12000, 2), 
            '7th week 2025-11-12 - 2025-11-18' . ' /AUD '. number_format(17012, 2), 
            '8th week 2025-11-19 - 2025-11-25' . ' /AUD '. number_format(19023, 2), 
            '9th week 2025-11-26 - 2025-12-02' . ' /AUD '. number_format(10245, 2),
        ];
        return [
            'cardData' => $cardData,
            'borderColor' => 'rgb(234, 88, 12)',
            'bgColor' => ['rgba(234,88,12,0.3)', 'rgba(234,88,12,0)'],
            'data' => $data
        ];
     * 
    */
    private function revenueCardWidget(string $title, string $table, string $sumColumn, string $statusColumn, int $statusId, string $cardType = 'pinboard'): array
    {
        $analytics = 'Last 9 Weeks ' . $title . ' Analytics';
        $weeks = [];
        $now = new DateTime();
        $currentWeekStart = (clone $now)->modify('monday this week')->format('Y-m-d');
    
        // Create 9 fixed week ranges (Mon → Sun)
        for ($i = 8; $i >= 0; $i--) {
            $start = (clone $now)->modify("-$i week")->modify('monday this week');
            $end   = (clone $start)->modify('sunday this week');
    
            $weeks[$start->format('Y-m-d')] = [
                'start' => $start->format('Y-m-d'),
                'end'   => $end->format('Y-m-d'),
                'total' => 0.0
            ];
        }
    
        $sql = "SELECT 
                YEARWEEK(created_at, 1) AS year_week,
                MIN(DATE(created_at)) AS week_start,
                SUM({$sumColumn}) AS total_amount
            FROM `{$table}`
            WHERE {$statusColumn} = :statusId
              AND created_at >= DATE_SUB(CURDATE(), INTERVAL 9 WEEK)
            GROUP BY year_week
            ORDER BY year_week ASC;";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['statusId' => $statusId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $grandTotalAmount = 0.0;
    
        // Map SQL results to the 9-week range (week-wise result)
        foreach ($results as $row) {
            $weekStart = date('Y-m-d', strtotime($row['week_start']));
            if (isset($weeks[$weekStart])) {
                $weeks[$weekStart]['total'] = (float)$row['total_amount'];
                $grandTotalAmount += (float)$row['total_amount'];
            }
        }
    
        $data = [];
        // testing data for week-wise result
        $weekWise = [];
        $last8WeeksTotal = 0.0;
        $weekCount = 0;
    
        foreach ($weeks as $start => $week) {
            $data[] = date('d M', strtotime($week['start'])) .
                ' - ' . date('d M', strtotime($week['end'])) .
                ' / AUD ' . number_format($week['total'], 2);
            // testing data for week-wise result
            $weekWise[] = [
                'start' => $week['start'],
                'end'   => $week['end'],
                'total' => $week['total']
            ];
            // calculate percent for each week
            if ($start !== $currentWeekStart) {
                $last8WeeksTotal += $week['total'];
                $weekCount++;
            }
        }

        // 100 / ((current week sales / current day of the week) / (last 8 week avarage / 7)) ## Percent calculation formula
        $currentDayOfWeek = (int)$now->format('N'); // 1=Mon ... 7=Sun
        $currentWeekAmount = $weeks[$currentWeekStart]['total'] ?? 0.0;
        $averageLast8WeeksPerDay = $weekCount > 0 ? ($last8WeeksTotal / $weekCount / 7) : 0.0;
        $percent = ($averageLast8WeeksPerDay > 0 && $currentWeekAmount > 0)
        ? round(100 / (($currentWeekAmount / $currentDayOfWeek) / $averageLast8WeeksPerDay), 2) . '%'
        : '0%';
    
        $cardData = [
            // card type
            'card_type' => $cardType,
            'title'     => $title,
            'analytics' => $analytics,
            'value'     => 'AUD ' . number_format($grandTotalAmount, 2),
            'percent'   => $percent,
        ];
    
        return [
            'cardData' => $cardData,
            'borderColor' => $this->revenueCardStyle($cardType)['borderColor'],
            'bgColor' => $this->revenueCardStyle($cardType)['bgColor'],
            'data' => $data,  // api output for week-wise result (ui output)
            'weekWise' => $weekWise    // api output for week-wise result (testing purpose)
        ];
    }
    
    private function revenueCardStyle(string $cardType): array
    {
        // data array
        $data = [];
        switch ($cardType) {
            case 'quote': // quote card style
                $data = [
                    'borderColor' => 'rgb(234, 88, 12)',
                    'bgColor' => ['rgba(234,88,12,0.3)', 'rgba(234,88,12,0)'],
                ];
                break;
            case 'order': // order card style
                $data = [
                    'borderColor' => 'rgb(37, 99, 235)',
                    'bgColor' => ['rgba(37,99,235,0.3)', 'rgba(37,99,235,0)'],
                ];
                break;
            case 'monthly_revenue': // monthly revenue card style
                $data = [
                    'borderColor' => 'rgb(124, 58, 237)',
                    'bgColor' => ['rgba(124,58,237,0.3)', 'rgba(124,58,237,0)'],
                ];
                break;
            default: // default card style
                $data = [
                    'borderColor' => '',
                    'bgColor' => []
                ];
        }
        // return the data array
        return $data;
    }

    /**
     * Get the revenue card details data
     *
     * @param string $cardType
     * @return array
     */
    public function getRevenueCardDetailsData(string $cardType = 'pinboard'): array
    {
        // last 9 weeks data from pinboard, quote, order // parameters: card type
        // now date
        $now = new DateTime();
        // get the start and end date of the week
        $startDate = (clone $now)->modify('-8 weeks')->modify('monday this week')->format('Y-m-d');
        $endDate = (clone $now)->modify('sunday this week')->format('Y-m-d');

        // SQL (prepared once, reused) // parameters: card type, start date, end date.
        if($cardType == 'pinboard'){ // pinboard card type [pending status id = 1]
            $sql = "
                SELECT 
                    p.pinboard_id as id, 
                    p.reference_number as reference, 
                    p.grand_total_sp_inc_gst as total, 
                    p.pinboard_description as description, 
                    u.avatar, 
                    c.name as customer_name, 
                    CASE
                        WHEN p.pinboard_status_id = 1 THEN 'pending'
                        WHEN p.pinboard_status_id = 2 THEN 'processing'
                        WHEN p.pinboard_status_id = 3 THEN 'processed'
                        WHEN p.pinboard_status_id = 4 THEN 'complete'
                        WHEN p.pinboard_status_id = 5 THEN 'canceled'
                        WHEN p.pinboard_status_id = 6 THEN 'archived'
                        WHEN p.pinboard_status_id = 7 THEN 'requires_action'
                        ELSE 'no status'
                    END AS status, 
                    p.created_at, 
                    p.updated_at
                FROM `pinboard` p
                LEFT JOIN `user` u ON u.user_id = p.user_id
                LEFT JOIN `customer` c ON c.user_id = u.user_id
                WHERE p.pinboard_status_id = 1
                AND DATE(p.created_at) BETWEEN '$startDate' AND '$endDate'
                ORDER BY p.created_at DESC
            ";
        }elseif($cardType == 'quote'){ // quote card type [pending status id = 1]
            $sql = "
                SELECT q.quote_id as id, q.reference_number as reference, q.grand_total_sp_inc_gst as total, q.quote_description as description, u.avatar, c.name as customer_name,
                CASE
                    WHEN q.quote_status_id = 1 THEN 'pending'
                    WHEN q.quote_status_id = 2 THEN 'processing'
                    WHEN q.quote_status_id = 3 THEN 'processed'
                    WHEN q.quote_status_id = 4 THEN 'complete'
                    WHEN q.quote_status_id = 5 THEN 'canceled'
                    WHEN q.quote_status_id = 6 THEN 'archived'
                    WHEN q.quote_status_id = 7 THEN 'requires_action'
                    ELSE 'no status'
                END AS status,
                q.created_at, q.updated_at
                FROM `quote` q
                LEFT JOIN `customer` c ON c.customer_id = q.customer_id
                LEFT JOIN `user` u ON u.user_id = q.user_id
                WHERE q.quote_status_id = 1
                AND DATE(q.created_at) BETWEEN '$startDate' AND '$endDate'
                ORDER BY q.created_at DESC
            ";
        }elseif($cardType == 'order'){ // order card type [pending status id = 1]
            $sql = "
                SELECT o.order_id as id, o.reference_number as reference, o.total as total, o.order_description as description, u.avatar, c.name as customer_name,
                CASE
                    WHEN o.order_status_id = 1 THEN 'pending'
                    WHEN o.order_status_id = 2 THEN 'processing'
                    WHEN o.order_status_id = 3 THEN 'processed'
                    WHEN o.order_status_id = 4 THEN 'complete'
                    WHEN o.order_status_id = 5 THEN 'canceled'
                    WHEN o.order_status_id = 6 THEN 'archived'
                    WHEN o.order_status_id = 7 THEN 'requires_action'
                    ELSE 'no status'
                END AS status, o.created_at, o.updated_at
                FROM `order` o
                LEFT JOIN `customer` c ON c.customer_id = o.customer_id
                LEFT JOIN `user` u ON u.user_id = o.user_id
                WHERE o.order_status_id = 1
                AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
                ORDER BY o.created_at DESC
            ";
        }elseif($cardType == 'monthly_revenue'){ // monthly revenue card type [complete status id = 4]
            $sql = "
                SELECT o.order_id as id, o.reference_number as reference, o.total as total, o.order_description as description, u.avatar, c.name as customer_name,
                CASE
                    WHEN o.order_status_id = 1 THEN 'pending'
                    WHEN o.order_status_id = 2 THEN 'processing'
                    WHEN o.order_status_id = 3 THEN 'processed'
                    WHEN o.order_status_id = 4 THEN 'complete'
                    WHEN o.order_status_id = 5 THEN 'canceled'
                    WHEN o.order_status_id = 6 THEN 'archived'
                    WHEN o.order_status_id = 7 THEN 'requires_action'
                    ELSE 'no status'
                END AS status, o.created_at, o.updated_at
                FROM `order` o
                LEFT JOIN `customer` c ON c.customer_id = o.customer_id
                LEFT JOIN `user` u ON u.user_id = o.user_id
                WHERE o.order_status_id = 4
                AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
                ORDER BY o.created_at DESC
            ";
        }

        // prepare the statement
        $stmt = $this->db->prepare($sql);
        // execute the statement
        $stmt->execute();
        // fetch the result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return the result
        return $result;       
    }
    /**
     * Get the quote or order details data
     *
     * @param int $id
     * @param string $type
     * @return array
     */
    public function getQuoteOrderDetails(int $id, string $type = 'quote'): array
    {
        $results = [];
        if($type == 'quote'){
            $results = $this->quoteRepository->getQuoteById($id);
        }elseif($type == 'order'){
            $results = $this->orderRepository->getOrderById($id);
        }

        return  $results;
    }
}
