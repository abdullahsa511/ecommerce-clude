<?php

declare(strict_types=1);

namespace App\Core\Models\Customer;

use App\Core\Models\Base\Model;
use App\Core\Models\Company\Company;
use App\Core\Models\User;
use App\Core\Models\User\UserAddress;

class Customer extends Model
{
    protected string $table = 'customer';
    protected string $primaryKey = 'customer_id';

    public int $customer_id;
    public ?int $company_id;
    public int $user_id;
    public int $organisation_id;
    public string $uuid;
    public string $org_code;
    public string $name;
    public ?string $customer_name;
    public float $rating;
    public ?string $abn;
    public int $segment_id;
    public int $term_id;
    public float $credit_limit;
    public int $caution_bad_payer;
    public int $is_active;
    public bool|null $is_verified = false;
    public ?string $date_last_invoice;
    public ?string $website;
    public ?string $event_group;
    public int $default_price_list;
    public float $deposit_percentage;
    public float $gst;
    public int $is_gmail_lead;
    public ?string $gmail_Id;
    public ?string $bpay_ref;
    public ?string $last_updated_on;
    public ?int $created_by;
    public ?string $deleted_at;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $billing_first_name;
    public ?string $billing_last_name;
    public ?string $billing_company;
    public ?string $billing_address_1;
    public ?string $billing_address_2;
    public ?string $billing_city;
    public ?string $billing_post_code;
    public ?string $billing_country_id;
    public ?string $billing_region;
    public ?string $billing_region_id;
    public ?string $payment_method;
    public ?string $payment_data;
    public ?string $payment_status_id;
    public ?string $shipping_first_name;
    public ?string $shipping_last_name;
    public ?string $shipping_company;
    public ?string $shipping_address_1;
    public ?string $shipping_address_2;
    public ?string $shipping_city;
    public ?string $shipping_post_code;
    public ?string $shipping_country;
    public ?string $shipping_country_id;
    public ?string $shipping_region;
    public ?string $shipping_region_id;
    public ?string $shipping_method;
    public ?string $shipping_data;
    public ?string $shipping_status_id;
    public ?string $company_name;


    
    public function __construct() 
    {
        parent::__construct();
    }

    // user relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // user address relationship
    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_id');
    }

    /**
     * Define relationship with Company model
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}