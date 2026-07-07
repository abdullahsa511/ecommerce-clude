<?php

declare(strict_types=1);

namespace App\Core\Models\Dashboard;

/**
 * Simple value object for the revenue card widget.
 *
 * Accepts either a numeric-indexed array (as produced in the repository)
 * or an associative array with keys:
 *  - pinboardOpens
 *  - quoteOpens
 *  - orderOpens
 *  - monthlyRevenue
 */
class RevenueCardWidget
{
    private int $pinboardOpens;
    private int $quoteOpens;
    private int $orderOpens;
    private float $monthlyRevenue;

    /**
     * RevenueCardWidget constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->pinboardOpens = isset($data[0]) ? (int)$data[0] : (int)($data['pinboardOpens'] ?? 0);
        $this->quoteOpens = isset($data[1]) ? (int)$data[1] : (int)($data['quoteOpens'] ?? 0);
        $this->orderOpens = isset($data[2]) ? (int)$data[2] : (int)($data['orderOpens'] ?? 0);
        $this->monthlyRevenue = isset($data[3]) ? (float)$data[3] : (float)($data['monthlyRevenue'] ?? 0.0);
    }

    /**
     * Return the widget data as an associative array ready for the API/response.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'pinboard_opens' => $this->pinboardOpens,
            'quote_opens' => $this->quoteOpens,
            'order_opens' => $this->orderOpens,
            'monthly_revenue' => number_format($this->monthlyRevenue, 2, '.', ''),
        ];
    }
}


