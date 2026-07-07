<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;
use App\Core\Models\Checkout\Voucher;
use App\Core\Models\Localisation\Currency;
use App\Core\Models\Geoip\Country;
use App\Core\Models\Geoip\Region;
use App\Core\Models\Order\OrderStatus;
use App\Core\Models\Order\PaymentStatus;
use App\Core\Models\Order\ShippingStatus;
use App\Core\Models\Order\OrderTracking;
use stdClass;

class Order extends Model
{
    protected string $table = 'order';
    // protected string $tableAlias = 'o';

    protected int $order_id;
    protected string $uuid;
    protected string $invoice_no;
    protected string $customer_order_id;
    protected string $invoice_prefix;
    protected int $site_id;
    protected string $site_name;
    protected ?int $user_id;
    protected ?int $user_group_id;
    protected string $first_name;
    protected string $last_name;
    protected string $email;
    protected string $phone_number;
    protected string $billing_first_name;
    protected string $billing_last_name;
    protected ?string $billing_company;
    protected string $billing_address_1;
    protected ?string $billing_address_2;
    protected ?string $billing_city;
    protected ?string $billing_post_code;
    protected int $billing_country_id;
    protected ?string $billing_region;
    protected int $billing_region_id;
    protected ?string $payment_method;
    protected ?string $payment_data;
    protected int $payment_status_id;
    protected ?string $shipping_first_name;
    protected ?string $shipping_last_name;
    protected ?string $shipping_company;
    protected ?string $shipping_address_1;
    protected ?string $shipping_address_2;
    protected ?string $shipping_city;
    protected ?string $shipping_post_code;
    protected ?string $shipping_country;
    protected int $shipping_country_id;
    protected ?string $shipping_region;
    protected int $shipping_region_id;
    protected ?string $shipping_method;
    protected ?string $shipping_data;
    protected int $shipping_status_id;
    protected ?float $total;
    protected int $order_status_id;
    protected int $language_id;
    protected int $currency_id;
    protected ?string $currency;
    protected ?float $currency_value;
    protected ?string $notes;
    protected ?string $remote_ip;
    protected ?string $forwarded_for_ip;
    protected ?string $user_agent;
    protected string $created_at;
    protected string $updated_at;

    protected ?string $job_id;
    protected ?string $reference_number;
    protected ?string $order_description;
    protected ?string $item_description;
    protected ?string $product_description;

    
    public function __construct() 
    {
        parent::__construct();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function orderTracking()
    {
        return $this->hasMany(OrderTracking::class, 'order_id', 'order_id');
    }
    


    /**
     * Get order products relationship
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'order_id');
    }

    /**
     * Get order product options relationship
     */
    public function productOptions()
    {
        return $this->hasMany(OrderProductOption::class, 'order_id', 'order_id');
    }

    /**
     * Get order totals relationship
     */
    public function totals()
    {
        return $this->hasMany(OrderTotal::class, 'order_id', 'order_id');
    }

    /**
     * Get order shipments relationship
     */
    public function shipments()
    {
        return $this->hasMany(OrderShipment::class, 'order_id', 'order_id');
    }

    /**
     * Get order vouchers relationship
     */
    public function vouchers()
    {
        return $this->hasMany(OrderVoucher::class, 'order_id', 'order_id');
    }

    /**
     * Get order log relationship
     */
    public function logs()
    {
        return $this->hasMany(OrderLog::class, 'order_id', 'order_id');
    }

    /**
     * Get order meta relationship
     */
    public function meta()
    {
        return $this->hasMany(OrderMeta::class, 'order_id', 'order_id');
    }

    /**
     * Get order status relationship
     */
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'order_status_id');
    }

    /**
     * Get payment status relationship
     */
    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id', 'payment_status_id');
    }

    /**
     * Get shipping status relationship
     */
    public function shippingStatus()
    {
        return $this->belongsTo(ShippingStatus::class, 'shipping_status_id', 'shipping_status_id');
    }

    /**
     * Get billing country relationship
     */
    public function billingCountry()
    {
        return $this->belongsTo(Country::class, 'billing_country_id', 'country_id');
    }

    /**
     * Get shipping country relationship
     */
    public function shippingCountry()
    {
        return $this->belongsTo(Country::class, 'shipping_country_id', 'country_id');
    }

    /**
     * Get billing region relationship
     */
    public function billingRegion()
    {
        return $this->belongsTo(Region::class, 'billing_region_id', 'region_id');
    }

    /**
     * Get shipping region relationship
     */
    public function shippingRegion()
    {
        return $this->belongsTo(Region::class, 'shipping_region_id', 'region_id');
    }

    /**
     * Get currency relationship
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'currency_id');
    }

    /**
     * Get voucher relationship
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }
} 


class OrderResponse
{
    public ?int $order_id;
    public string $uuid;
    public OrderDetailsResponse $orderDetails;
    public AccountManagerDetailsResponse $accountManagerDetails;
    public CustomerDetailsResponse $customerDetails;
    public BillingDetailsResponse $billingDetails;
    public ShippingDetailsResponse $shippingDetails;

    public function __construct(stdClass $data) 
    {
        $this->order_id = $data->order_id ?? null;
        $this->uuid = $data->uuid ?? '';
        $this->orderDetails = new OrderDetailsResponse($data);
        $this->customerDetails = new CustomerDetailsResponse($data);        
        $this->accountManagerDetails = new AccountManagerDetailsResponse($data);
        $this->billingDetails = new BillingDetailsResponse($data);
        $this->shippingDetails = new ShippingDetailsResponse($data);
    }
}

class OrderDetailsResponse
{
    public string $invoice_no;
    public string $reference_no;
    public string $customer_order_id;
    public string $invoice_prefix;
    public int $site_id;
    public string $site_name;
    public string $total;
    public int $order_status_id;
    public int $language_id;
    public int $currency_id;
    public string $currency;
    public string $currency_value;
    public string $notes;
    public string $remote_ip;
    public string $forwarded_for_ip;
    public string $user_agent;
    public string $created_at;
    public string $updated_at;

    public function __construct(stdClass $data)
    {
        $this->invoice_no = $data->invoice_no ?? '';
        $this->reference_no = $data->reference_number ?? '';
        $this->customer_order_id = $data->customer_order_id ?? '';
        $this->invoice_prefix = $data->invoice_prefix ?? '';
        $this->site_id = $data->site_id ?? 0;
        $this->site_name = $data->site_name ?? '';
        $this->total = number_format((float)($data->total ?? 0), 2, '.', '');
        $this->order_status_id = $data->order_status_id ?? 0;
        $this->language_id = $data->language_id ?? 0;
        $this->currency_id = $data->currency_id ?? 0;
        $this->currency = $data->currency ?? '';
        $this->currency_value = number_format((float)($data->currency_value ?? 0), 2, '.', '');
        $this->notes = $data->notes ?? '';
        $this->remote_ip = $data->remote_ip ?? '';
        $this->forwarded_for_ip = $data->forwarded_for_ip ?? '';
        $this->user_agent = $data->user_agent ?? '';
        $this->created_at = $data->created_at ?? '';
        $this->updated_at = $data->updated_at ?? '';
    }
}

class CustomerDetailsResponse
{
    public int $user_id;
    public int $user_group_id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone_number;
    public string $total_orders;

    public function __construct(stdClass $data)
    {
        $this->user_id = $data->user_id ?? 0;
        $this->user_group_id = $data->user_group_id ?? 0;
        $this->first_name = $data->first_name ?? '';
        $this->last_name = $data->last_name ?? '';
        $this->email = $data->email ?? '';
        $this->phone_number = $data->phone_number ?? '';
        $this->total_orders = $data->total_orders ?? '';
    }
}
class AccountManagerDetailsResponse
{
    public int $user_id;
    public int $user_group_id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone_number;
    public string $total_orders;

    public function __construct(stdClass $data)
    {
        $this->user_id = $data->user_id ?? 0;
        $this->user_group_id = $data->user_group_id ?? 0;
        $this->first_name = $data->first_name ?? '';
        $this->last_name = $data->last_name ?? '';
        $this->email = $data->email ?? '';
        $this->phone_number = $data->phone_number ?? '';
        $this->total_orders = $data->total_orders ?? '';
    }
}

class BillingDetailsResponse
{
    public string $billing_first_name;
    public string $billing_last_name;
    public string $billing_company;
    public string $billing_address_1;
    public string $billing_address_2;
    public string $billing_city;
    public string $billing_post_code;
    public int $billing_country_id;
    public string $billing_region;
    public int $billing_region_id;
    public string $payment_method;
    public string $payment_data;
    public int $payment_status_id;

    public function __construct(stdClass $data)
    {
        $this->billing_first_name = $data->billing_first_name ?? '';
        $this->billing_last_name = $data->billing_last_name ?? '';
        $this->billing_company = $data->billing_company ?? '';
        $this->billing_address_1 = $data->billing_address_1 ?? '';
        $this->billing_address_2 = $data->billing_address_2 ?? '';
        $this->billing_city = $data->billing_city ?? '';
        $this->billing_post_code = $data->billing_post_code ?? '';
        $this->billing_country_id = $data->billing_country_id ?? 0;
        $this->billing_region = $data->billing_region ?? '';
        $this->billing_region_id = $data->billing_region_id ?? 0;
        $this->payment_method = $data->payment_method ?? '';
        $this->payment_data = $data->payment_data ?? '';
        $this->payment_status_id = $data->payment_status_id ?? 0;
    }
}

class ShippingDetailsResponse
{
    public string $shipping_first_name;
    public string $shipping_last_name;
    public string $shipping_company;
    public string $shipping_address_1;
    public string $shipping_address_2;
    public string $shipping_city;
    public string $shipping_post_code;
    public string $shipping_country;
    public int $shipping_country_id;
    public string $shipping_region;
    public int $shipping_region_id;
    public string $shipping_method;
    public string $shipping_data;
    public int $shipping_status_id;

    public function __construct(stdClass $data)
    {
        $this->shipping_first_name = $data->shipping_first_name ?? '';
        $this->shipping_last_name = $data->shipping_last_name ?? '';
        $this->shipping_company = $data->shipping_company ?? '';
        $this->shipping_address_1 = $data->shipping_address_1 ?? '';
        $this->shipping_address_2 = $data->shipping_address_2 ?? '';
        $this->shipping_city = $data->shipping_city ?? '';
        $this->shipping_post_code = $data->shipping_post_code ?? '';
        $this->shipping_country = $data->shipping_country ?? '';
        $this->shipping_country_id = $data->shipping_country_id ?? 0;
        $this->shipping_region = $data->shipping_region ?? '';
        $this->shipping_region_id = $data->shipping_region_id ?? 0;
        $this->shipping_method = $data->shipping_method ?? '';
        $this->shipping_data = $data->shipping_data ?? '';
        $this->shipping_status_id = $data->shipping_status_id ?? 0;
    }
}

class OrderData
{
    public ?int $order_id;
    public string $uuid;
    public OrderDetails $orderDetails;
    public CustomerDetails $customerDetails;
    public BillingDetails $billingDetails;
    public ShippingDetails $shippingDetails;

    public function __construct(array $data = [])
    {
        $this->order_id = $data['order_id'] ?? null;
        $this->uuid = $data['uuid'] ?? '';
        $this->orderDetails = new OrderDetails($data['orderDetails'] ?? []);
        $this->customerDetails = new CustomerDetails($data['customerDetails'] ?? []);
        $this->billingDetails = new BillingDetails($data['billingDetails'] ?? []);
        $this->shippingDetails = new ShippingDetails($data['shippingDetails'] ?? []);
    }

    public function toArray(): array
    {
        return [
            // mandatory fields
            'job_id' => $this->orderDetails->job_id ?? '1',
            'uuid' => $this->uuid,
            'order_description' => $this->orderDetails->order_description ?? 'SA alex chair order from website',
            'site_name' => $this->orderDetails->site_name ?? 'krost-furniture',
            'first_name' => $this->customerDetails->first_name ?? 'Ali',
            'last_name' => $this->customerDetails->last_name ?? 'Abdullah',
            'email' => $this->customerDetails->email ?? 'abdullah@satechonology.com',
            'billing_first_name' => $this->billingDetails->billing_first_name ?? 'Alex',
            'billing_last_name' => $this->billingDetails->billing_last_name ?? 'Johnson',
            'billing_address_1' => $this->billingDetails->billing_address_1 ?? '123 Main St',
            'billing_country_id' => $this->billingDetails->billing_country_id ?? 1,
            'billing_region_id' => $this->billingDetails->billing_region_id ?? 1,
            'language_id' => $this->orderDetails->language_id ?? 1,
            'currency_id' => $this->orderDetails->currency_id ?? 1,
            // optional fields
            'order_id' => $this->order_id,
            'invoice_no' => $this->orderDetails->invoice_no,
            'customer_order_id' => $this->orderDetails->customer_order_id,
            'invoice_prefix' => $this->orderDetails->invoice_prefix,
            'site_id' => $this->orderDetails->site_id,
            // 'site_name' => $this->orderDetails->site_name,
            'total' => $this->orderDetails->total,
            'order_status_id' => $this->orderDetails->order_status_id,
            // 'language_id' => $this->orderDetails->language_id,
            // 'currency_id' => $this->orderDetails->currency_id,
            'currency' => $this->orderDetails->currency,
            'currency_value' => $this->orderDetails->currency_value,
            'notes' => $this->orderDetails->notes,
            'remote_ip' => $this->orderDetails->remote_ip,
            'forwarded_for_ip' => $this->orderDetails->forwarded_for_ip,
            'user_agent' => $this->orderDetails->user_agent,
            'user_id' => $this->customerDetails->user_id,
            'user_group_id' => $this->customerDetails->user_group_id,
            // 'first_name' => $this->customerDetails->first_name,
            // 'last_name' => $this->customerDetails->last_name,
            // 'email' => $this->customerDetails->email,
            'phone_number' => $this->customerDetails->phone_number,
            // 'total_orders' => $this->customerDetails->total_orders, // Commented out due to missing database column
            // 'billing_first_name' => $this->billingDetails->billing_first_name,
            // 'billing_last_name' => $this->billingDetails->billing_last_name,
            'billing_company' => $this->billingDetails->billing_company,
            // 'billing_address_1' => $this->billingDetails->billing_address_1,
            'billing_address_2' => $this->billingDetails->billing_address_2,
            'billing_city' => $this->billingDetails->billing_city,
            'billing_post_code' => $this->billingDetails->billing_post_code,
            // 'billing_country_id' => $this->billingDetails->billing_country_id,
            'billing_region' => $this->billingDetails->billing_region,
            // 'billing_region_id' => $this->billingDetails->billing_region_id,
            'payment_method' => $this->billingDetails->payment_method,
            'payment_data' => $this->billingDetails->payment_data,
            'payment_status_id' => $this->billingDetails->payment_status_id,
            'shipping_first_name' => $this->shippingDetails->shipping_first_name,
            'shipping_last_name' => $this->shippingDetails->shipping_last_name,
            'shipping_company' => $this->shippingDetails->shipping_company,
            'shipping_address_1' => $this->shippingDetails->shipping_address_1,
            'shipping_address_2' => $this->shippingDetails->shipping_address_2,
            'shipping_city' => $this->shippingDetails->shipping_city,
            'shipping_post_code' => $this->shippingDetails->shipping_post_code,
            'shipping_country' => $this->shippingDetails->shipping_country,
            'shipping_country_id' => $this->shippingDetails->shipping_country_id,
            'shipping_region' => $this->shippingDetails->shipping_region,
            'shipping_region_id' => $this->shippingDetails->shipping_region_id,
            'shipping_method' => $this->shippingDetails->shipping_method,
            'shipping_data' => $this->shippingDetails->shipping_data,
            'shipping_status_id' => $this->shippingDetails->shipping_status_id
        ];
    }
}

class OrderDetails
{
    public string $invoice_no = '';
    public string $job_id = '';
    public string $customer_order_id = '';
    public string $invoice_prefix = '';
    public int $site_id = 0;
    public string $site_name = '';
    public float $total = 0;
    public int $order_status_id = 0;
    public int $language_id = 0;
    public int $currency_id = 0;
    public string $currency = '';
    public float $currency_value = 0;
    public string $notes = '';
    public string $remote_ip = '';
    public string $forwarded_for_ip = '';
    public string $user_agent = '';

    public function __construct(array $data = [])
    {
        $this->invoice_no = $data['invoice_no'] ?? '';
        $this->job_id = $data['job_id'] ?? '1';
        $this->customer_order_id = $data['customer_order_id'] ?? '';
        $this->invoice_prefix = $data['invoice_prefix'] ?? '';
        $this->site_id = (int)($data['site_id'] ?? 0);
        $this->site_name = $data['site_name'] ?? '';
        $this->total = (float)($data['total'] ?? 0);
        $this->order_status_id = (int)($data['order_status_id'] ?? 0);
        $this->language_id = (int)($data['language_id'] ?? 0);
        $this->currency_id = (int)($data['currency_id'] ?? 0);
        $this->currency = $data['currency'] ?? '';
        $this->currency_value = (float)($data['currency_value'] ?? 0);
        $this->notes = $data['notes'] ?? '';
        $this->remote_ip = $data['remote_ip'] ?? '';
        $this->forwarded_for_ip = $data['forwarded_for_ip'] ?? '';
        $this->user_agent = $data['user_agent'] ?? '';
    }
}

class CustomerDetails
{
    public int $user_id = 0;
    public int $user_group_id = 0;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone_number = '';
    public int $total_orders = 0;

    public function __construct(array $data = [])
    {
        $this->user_id = (int)($data['user_id'] ?? 0);
        $this->user_group_id = (int)($data['user_group_id'] ?? 0);
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone_number = $data['phone_number'] ?? '';
        $this->total_orders = (int)($data['total_orders'] ?? 0);
    }
}

class BillingDetails
{
    public string $billing_first_name = '';
    public string $billing_last_name = '';
    public string $billing_company = '';
    public string $billing_address_1 = '';
    public string $billing_address_2 = '';
    public string $billing_city = '';
    public string $billing_post_code = '';
    public int $billing_country_id = 0;
    public string $billing_region = '';
    public int $billing_region_id = 0;
    public string $payment_method = '';
    public string $payment_data = '';
    public int $payment_status_id = 0;

    public function __construct(array $data = [])
    {
        $this->billing_first_name = $data['billing_first_name'] ?? '';
        $this->billing_last_name = $data['billing_last_name'] ?? '';
        $this->billing_company = $data['billing_company'] ?? '';
        $this->billing_address_1 = $data['billing_address_1'] ?? '';
        $this->billing_address_2 = $data['billing_address_2'] ?? '';
        $this->billing_city = $data['billing_city'] ?? '';
        $this->billing_post_code = $data['billing_post_code'] ?? '';
        $this->billing_country_id = (int)($data['billing_country_id'] ?? 0);
        $this->billing_region = $data['billing_region'] ?? '';
        $this->billing_region_id = (int)($data['billing_region_id'] ?? 0);
        $this->payment_method = $data['payment_method'] ?? '';
        $this->payment_data = $data['payment_data'] ?? '';
        $this->payment_status_id = (int)($data['payment_status_id'] ?? 0);
    }
}

class ShippingDetails
{
    public string $shipping_first_name = '';
    public string $shipping_last_name = '';
    public string $shipping_company = '';
    public string $shipping_address_1 = '';
    public string $shipping_address_2 = '';
    public string $shipping_city = '';
    public string $shipping_post_code = '';
    public string $shipping_country = '';
    public int $shipping_country_id = 0;
    public string $shipping_region = '';
    public int $shipping_region_id = 0;
    public string $shipping_method = '';
    public string $shipping_data = '';
    public int $shipping_status_id = 0;

    public function __construct(array $data = [])
    {
        $this->shipping_first_name = $data['shipping_first_name'] ?? '';
        $this->shipping_last_name = $data['shipping_last_name'] ?? '';
        $this->shipping_company = $data['shipping_company'] ?? '';
        $this->shipping_address_1 = $data['shipping_address_1'] ?? '';
        $this->shipping_address_2 = $data['shipping_address_2'] ?? '';
        $this->shipping_city = $data['shipping_city'] ?? '';
        $this->shipping_post_code = $data['shipping_post_code'] ?? '';
        $this->shipping_country = $data['shipping_country'] ?? '';
        $this->shipping_country_id = (int)($data['shipping_country_id'] ?? 0);
        $this->shipping_region = $data['shipping_region'] ?? '';
        $this->shipping_region_id = (int)($data['shipping_region_id'] ?? 0);
        $this->shipping_method = $data['shipping_method'] ?? '';
        $this->shipping_data = $data['shipping_data'] ?? '';
        $this->shipping_status_id = (int)($data['shipping_status_id'] ?? 0);
    }
} 