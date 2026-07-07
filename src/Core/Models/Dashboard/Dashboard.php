<?php

declare(strict_types=1);

namespace App\Core\Models\Dashboard;

use App\Core\Models\Base\Model;
use \stdClass;
use App\Core\Models\Order\Order;

class Dashboard extends Model
{
    protected string $table = 'dashboard';
    protected string $primaryKey = 'dashboard_id';

    public int|string $dashboard_id;
    public ?int $total_revenue;

    public function __construct()
    {
        parent::__construct();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

class DashboardData
{
    public int $total_revenue;

    public function __construct($data)
    {
        $this->total_revenue = $data['total_revenue'] ?? $data->total_revenue ?? 0;
    }
}

/**
 * start revenue card widget
 * */ 
class CardData
{
    public string $title;
    public string $card_type;
    public string $analytics;
    public string $value;
    public string|int $percent;

    public function __construct($data)
    {
        $this->title = $data['title'] ?? $data->title ?? '';
        $this->card_type = $data['card_type'] ?? $data->card_type ?? '';
        $this->analytics = $data['analytics'] ?? $data->analytics ?? '';
        $this->value = $data['value'] ?? $data->value ?? '';
        $this->percent = $data['percent'] ?? $data->percent ?? '';
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'card_type' => $this->card_type,
            'analytics' => $this->analytics,
            'value' => $this->value,
            'percent' => $this->percent,
        ];
    }
}

class RevenueCardWidget
{
    public CardData $cardData;
    public ?string $borderColor = null;
    public array $bgColor = [];
    public array $data = [];

    /**
     * Accepts either an associative array or stdClass representing a single widget entry.
     *
     * Example payload shape:
     * [
     *   'cardData' => ['title'=>'...','analytics'=>'...','value'=>'...','percent'=>'...'],
     *   'borderColor' => 'rgb(...)',
     *   'bgColor' => ['rgba(...)','rgba(...)'],
     *   'data' => [123,456,...]
     * ]
     *
     * @param array|stdClass $payload
     */
    public function __construct($payload)
    {
        $card = $payload['cardData'] ?? $payload->cardData ?? [];
        $this->cardData = new CardData($card);
        $this->borderColor = $payload['borderColor'] ?? $payload->borderColor ?? null;
        $this->bgColor = $payload['bgColor'] ?? $payload->bgColor ?? [];
        $this->data = $payload['data'] ?? $payload->data ?? [];
    }

    public function toArray(): array
    {
        return [
            'cardData' => $this->cardData->toArray(),
            'borderColor' => $this->borderColor,
            'bgColor' => $this->bgColor,
            'data' => $this->data,
        ];
    }
}

/**
 * end revenue card widget
 */

/**
 * start overview widget
 */

/**
 * end overview widget
 */

/**
 * start pinboards widget
*/

/**
 * end pinboards widget
 */

/**
* start recent quotes widget
*/

/**
 * end recent quotes widget
 */

/**
 * start recent orders widget
 */

/**
 * end recent orders widget
 */