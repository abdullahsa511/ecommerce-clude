<?php

declare(strict_types=1);

namespace App\Core\Models\Pinboard;

use App\Core\Models\Base\Model;
use App\Core\Models\User;
use stdClass;

class PinboardTemp extends Model
{
    protected string $table = 'pinboard_temp';
    // protected string $tableAlias = 'p';

    protected ?int $pinboard_temp_id;
    protected ?string $reference_number;
    protected ?int $company_id;
    protected ?int $dispatch_location_id;
    protected ?string $job_title;
    protected ?string $pinboard_name;
    protected ?string $pinboard_description;
    protected ?int $account_manager_id;
    protected ?int $project_manager_id;
    protected ?int $user_id;
    protected ?int $customer_id;
    protected ?string $contact_number = null;
    protected ?string $customer_po_number;
    protected ?string $expiry_date;
    protected ?string $organisation_code;
    protected ?int $organisation_id;
    protected ?string $organisation_name;
    protected ?string $zoho_id;
    protected ?string $terms;
    protected ?float $deposit_percentage;
    protected ?float $gst;
    protected ?string $bill_to;
    protected ?string $ship_to;
    protected ?string $site_contacts;
    protected ?float $customer_balance;
    protected ?string $sales_price_list;
    protected ?float $total_bp_ex_gst;
    protected ?float $total_bp_inc_gst;
    protected ?float $total_sp_ex_gst;
    protected ?float $total_sp_inc_gst;
    protected ?float $order_discount;
    protected ?float $discount_rate;
    protected ?float $discount_amount;
    protected ?float $grand_total_sp_ex_gst;
    protected ?float $grand_total_sp_inc_gst;
    protected ?string $bill_instructions;
    protected ?string $bill_address;
    protected ?string $bill_suburb;
    protected ?string $bill_state;
    protected ?string $bill_postcode;
    protected ?string $bill_country;
    protected ?string $ship_building_name;
    protected ?string $ship_instructions;
    protected ?string $ship_address;
    protected ?string $created_at;
    protected ?string $updated_at;
    protected ?string $customer_email;

    protected ?int $job_id;
    protected ?int $pinboard_status_id;
    protected ?float $total;
    protected ?string $ship_address_two;
    protected ?string $ship_suburb;
    protected ?string $ship_state;
    protected ?string $ship_postcode;
    protected ?string $ship_country;

    // custom fields
    protected ?string $gmail_Id;
    protected ?string $name;
    protected ?string $phone;
    protected ?string $address;


    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Override set to normalize numeric fields (avoid typed property assignment errors).
     *
     * @param array|null $data
     * @return ?self
     */
    public function set(?array $data): ?self
    {
        if (!is_array($data) || empty($data)) {
            return null;
        }

        if (array_key_exists('gst', $data)) {
            $data['gst'] = is_numeric($data['gst']) ? (float)$data['gst'] : null;
        }

        return parent::set($data);
    }

    /**
     * Get pinboard items relationship
     */
    public function pinboard_items()
    {
        return $this->hasMany(PinboardItem::class, 'pinboard_temp_id', 'pinboard_temp_id');
    }

    /**
     * Get account manager relationship
     */
    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id', 'user_id');
    }

    /**
     * Get project manager relationship
     */
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id', 'user_id');
    }

    /**
     * Get user relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
