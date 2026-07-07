<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Dashboard\RevenueCardWidget;
use App\Core\Repositories\Dashboard\DashboardRepositoryInterface;

class DashboardController extends ApiController
{
    private DashboardRepositoryInterface $dashboardRepository;
    public function __construct(
        DashboardRepositoryInterface $dashboardRepository,
    ) {
        parent::__construct();
        $this->dashboardRepository = $dashboardRepository;
    }

    // 1. dashboard/revenue-card-widget
    /**
     * Get the revenue card widget data
     *
     * @return array
     */
    public function revenueCardWidget(Request $request): Response
    {
        // get the revenue card widget data
        $data = $this->dashboardRepository->getRevenueCardWidgetData();
        // map the data to the revenue card widget
        $widgets = array_map(fn($item) => (new RevenueCardWidget($item))->toArray(), $data);
        // return the revenue card widget data
        return $this->renderResponse($widgets);
    }

    public function revenueCardDetails(Request $request): Response
    {
        // get the card type
        $cardType = $request->query('card_type') ?? 'pinboard';
        // get the revenue card details data
        $data = $this->dashboardRepository->getRevenueCardDetailsData($cardType);
        // return the revenue card details data
        return $this->renderResponse($data);
    }

    // 2. dashboard/overview-widget
    /**
     * Get the overview widget data
     *
     * @return array
     */
    public function overviewWidget(Request $request): Response
    {
        $type = $request->query('type');
        $data = $this->dashboardRepository->getOverviewWidgetData($type);
        return $this->renderResponse($data);
    }
    // 3. dashboard/pinboards-widget
    /**
     * Get the pinboards widget data
     *
     * @param Request $request
     * @param int $limit
     * @return array
     */
    public function pinboardsWidget(Request $request): Response
    {
        // get the limit
        $limit = $request->query('limit');
        // get the pinboards widget data
        $data = $this->dashboardRepository->getPinboardsWidgetData((int)$limit);
        // return the pinboards widget data
        return $this->renderResponse($data);
    }
    // 4. dashboard/recent-quotes-widget
    /**
     * Get the recent quotes widget data
     *
     * @param Request $request
     * @param int $limit
     * @return array
     */
    public function recentQuotesWidget(Request $request): Response
    {
        // get the limit
        $limit = $request->query('limit');
        // get the recent quotes widget data
        $data = $this->dashboardRepository->getRecentQuotesWidgetData((int)$limit);
        // return the recent quotes widget data
        return $this->renderResponse($data);
    }
    // 5. dashboard/recent-orders-widget
    /**
     * Get the recent orders widget data
     * @param Request $request
     * @param int $limit
     * @return array
     */
    public function recentOrdersWidget(Request $request): Response
    {
        $limit = $request->query('limit');
        $data = $this->dashboardRepository->getRecentOrdersWidgetData((int)$limit);
        return $this->renderResponse($data);
    }

    public function quoteOrderDetails(Request $request): Response
    {
        $id = $request->query('id');
        $type = $request->query('type');
        $data = $this->dashboardRepository->getQuoteOrderDetails((int) $id, $type);
        return $this->renderResponse($data);
    }
}
