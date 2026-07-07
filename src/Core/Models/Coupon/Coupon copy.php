<?php

declare(strict_types=1);

namespace App\Core\Models\Coupon;

use App\Core\Models\Base\Model;
use stdClass;

class Coupon extends Model
{
    protected string $table = 'coupon';
    // protected string $tableAlias = 'c';

    protected ?int $coupon_id;
    protected ?string $name;
    protected ?string $code;
    protected ?float $discount;
    protected ?string $type;
    protected ?int $free_shipping;
    protected ?int $status;
    protected ?int $registered_user_only;
    protected ?float $cart_total_min;
    protected ?string $date_start;
    protected ?string $date_end;
    protected ?int $coupon_limit;
    protected ?int $user_limit;
    protected ?string $created_at;
    protected ?string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Get coupon products relationship
     */
    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class, 'coupon_id', 'coupon_id');
    }
}

class CouponResponse
{
    public ?int $coupon_id;
    public CouponDetailsResponse $couponDetails;
    public ?array $couponProducts;

    public function __construct(stdClass $data) 
    {
        $this->coupon_id = $data->coupon_id ?? null;
        $this->couponDetails = new CouponDetailsResponse($data);
        $this->couponProducts = $this->parseCouponProducts($data->couponProducts ?? null);
    }

    /**
     * Parse coupon products from JSON string or array
     * 
     * @param mixed $couponProducts
     * @return array|null
     */
    private function parseCouponProducts($couponProducts): ?array
    {
        if (empty($couponProducts)) {
            return null;
        }

        // If it's already an array, return it
        if (is_array($couponProducts)) {
            return $couponProducts;
        }

        // If it's a JSON string, decode it
        if (is_string($couponProducts)) {
            $decoded = json_decode($couponProducts, true);
            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }
}

class CouponDetailsResponse
{
    public ?string $name;
    public ?string $code;
    public ?float $discount;
    public ?string $type;
    public ?int $free_shipping;
    public ?int $status;
    public ?int $registered_user_only;
    public ?float $cart_total_min;
    public ?string $date_start;
    public ?string $date_end;
    public ?int $coupon_limit;
    public ?int $user_limit;

    public function __construct(stdClass $data)
    {
        $this->name = $data->name ?? '';
        $this->code = $data->code ?? '';
        $this->discount = (float)($data->discount ?? 0);
        $this->type = $data->type ?? '';
        $this->free_shipping = (int)($data->free_shipping ?? 0);
        $this->status = (int)($data->status ?? 0);
        $this->registered_user_only = (int)($data->registered_user_only ?? 0);
        $this->cart_total_min = (float)($data->cart_total_min ?? 0);
        $this->date_start = $this->formatDateForDatabase($data->date_start ?? '');
        $this->date_end = $this->formatDateForDatabase($data->date_end ?? '');
        $this->coupon_limit = (int)($data->coupon_limit ?? 0);
        $this->user_limit = (int)($data->user_limit ?? 0);
    }

    /**
     * Convert ISO 8601 datetime string to MySQL date format
     * 
     * @param string $dateString
     * @return string
     */
    private function formatDateForDatabase(string $dateString): string
    {
        if (empty($dateString)) {
            return '';
        }

        try {
            $date = new \DateTime($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, return empty string
            return '';
        }
    }
}

class CouponData
{
    public ?int $coupon_id;
    public CouponDetails $couponDetails;

    public function __construct(array $data = [])
    {
        $this->coupon_id = $data['coupon_id'] ?? null;
        $this->couponDetails = new CouponDetails($data['couponDetails'] ?? []);
    }

    public function toArray(): array
    {
        return [
            'coupon_id' => $this->coupon_id,
            'name' => $this->couponDetails->name,
            'code' => $this->couponDetails->code,
            'discount' => $this->couponDetails->discount,
            'type' => $this->couponDetails->type,
            'free_shipping' => $this->couponDetails->free_shipping,
            'status' => $this->couponDetails->status,
            'registered_user_only' => $this->couponDetails->registered_user_only,
            'cart_total_min' => $this->couponDetails->cart_total_min,
            'date_start' => $this->couponDetails->date_start,
            'date_end' => $this->couponDetails->date_end,
            'coupon_limit' => $this->couponDetails->coupon_limit,
            'user_limit' => $this->couponDetails->user_limit
        ];
    }
}

class CouponDetails
{
    public string $name = '';
    public string $code = '';
    public float $discount = 0;
    public string $type = '';
    public int $free_shipping = 0;
    public int $status = 0;
    public int $registered_user_only = 0;
    public float $cart_total_min = 0;
    public string $date_start = '';
    public string $date_end = '';
    public int $coupon_limit = 0;
    public int $user_limit = 0;
    public ?array $couponProducts = null;

    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->code = $data['code'] ?? '';
        $this->discount = (float)($data['discount'] ?? 0);
        $this->type = $data['type'] ?? '';
        $this->free_shipping = (int)($data['free_shipping'] ?? 0);
        $this->status = (int)($data['status'] ?? 0);
        $this->registered_user_only = (int)($data['registered_user_only'] ?? 0);
        $this->cart_total_min = (float)($data['cart_total_min'] ?? 0);
        $this->date_start = $this->formatDateForDatabase($data['date_start'] ?? '');
        $this->date_end = $this->formatDateForDatabase($data['date_end'] ?? '');
        $this->coupon_limit = (int)($data['coupon_limit'] ?? 0);
        $this->user_limit = (int)($data['user_limit'] ?? 0);
        $this->couponProducts = $this->parseCouponProducts($data['couponProducts'] ?? null);
    }

    /**
     * Convert ISO 8601 datetime string to MySQL date format
     * 
     * @param string $dateString
     * @return string
     */
    private function formatDateForDatabase(string $dateString): string
    {
        if (empty($dateString)) {
            return '';
        }

        try {
            $date = new \DateTime($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, return empty string
            return '';
        }
    }

    /**
     * Parse coupon products from JSON string or array
     * 
     * @param mixed $couponProducts
     * @return array|null
     */
    private function parseCouponProducts($couponProducts): ?array
    {
        if (empty($couponProducts)) {
            return null;
        }

        // If it's already an array, return it
        if (is_array($couponProducts)) {
            return $couponProducts;
        }

        // If it's a JSON string, decode it
        if (is_string($couponProducts)) {
            $decoded = json_decode($couponProducts, true);
            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }
} 