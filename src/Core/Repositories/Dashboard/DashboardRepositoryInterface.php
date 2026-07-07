<?php

declare(strict_types=1);

namespace App\Core\Repositories\Dashboard;

use App\Core\Models\Base\Model;
use App\Core\Models\Dashboard\Dashboard;
use App\Core\Models\Dashboard\DashboardData;
use App\Core\ModelsFilters\RequestUri;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface DashboardRepositoryInterface extends BaseRepositoryInterface
{
    // 1. dashboard/revenue-card-widget
    /**
     * Get the revenue card widget data
     *
     * @return array
     */
    public function getRevenueCardWidgetData(): array;

    // 2. dashboard/overview-widget
    /**
     * Get the overview widget data
     *
     * @return array
     */
    public function getOverviewWidgetData(string $type = 'weekly'): array;
    // 3. dashboard/pinboards-widget
    /**
     * Get the pinboards widget data
     *
     * @return array
     */
    public function getPinboardsWidgetData(): array;
    // 4. dashboard/recent-quotes-widget
    /**
     * Get the recent quotes widget data
     *
     * @return array
     */
    public function getRecentQuotesWidgetData(): array;
    // 5. dashboard/recent-orders-widget
    /**
     * Get the recent orders widget data
     *
     * @return array
     */
    public function getRecentOrdersWidgetData(): array;
    public function getRevenueCardDetailsData(string $cardType = 'pinboard'): array;
    /**
     * Get the quote or order details data
     *
     * @param int $id
     * @param string $type
     * @return array
     */
    public function getQuoteOrderDetails(int $id, string $type = 'quote'): array;
} 