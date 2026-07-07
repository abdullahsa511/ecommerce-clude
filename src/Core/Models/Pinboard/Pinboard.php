<?php

declare(strict_types=1);

namespace App\Core\Models\Pinboard;

use App\Core\Models\Base\Model;
use App\Core\Models\User;
use function App\Core\System\utils\currentDateTime;
use DateTimeImmutable;
use DateTimeZone;
use stdClass;

class Pinboard extends Model
{
    protected string $table = 'pinboard';
    // protected string $tableAlias = 'p';

    protected ?int $pinboard_id;
    protected ?int $lead_id;
    protected ?string $uuid;
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

    protected ?int $job_id;
    protected ?int $pinboard_status_id;
    protected ?float $total;
    protected ?string $ship_address_two;
    protected ?string $ship_suburb;
    protected ?string $ship_state;
    protected ?string $ship_postcode;
    protected ?string $ship_country;
    protected ?int $is_active;
    // custom fields
    protected ?string $name;
    protected ?string $phone;
    protected ?string $address;
    protected ?string $gmail_Id;
    protected ?string $note;
    protected ?int $is_cancel_phone_call;
    protected ?string $customer_name;
    protected ?string $customer_email;
    protected ?string $item_count;
    
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
        return $this->hasMany(PinboardItem::class, 'pinboard_id', 'pinboard_id');
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

class PinboardResponse
{
    public ?int $pinboard_id;
    public ?string $uuid;
    public ?string $reference_number;
    public ?int $company_id;
    public ?int $dispatch_location_id;
    public ?string $job_title;
    public ?string $pinboard_name;
    public ?string $pinboard_description;
    public ?int $account_manager_id;
    public ?int $project_manager_id;
    public ?int $user_id;
    public ?int $customer_id;
    public ?string $contact_number = null;
    public ?string $customer_po_number;
    public ?string $expiry_date;
    public ?string $organisation_code;
    public ?int $organisation_id;
    public ?string $organisation_name;
    public ?string $zoho_id;
    public ?string $terms;
    public ?float $deposit_percentage;
    public ?float $gst;
    public ?string $bill_to;
    public ?string $ship_to;
    public ?string $site_contacts;
    public ?float $customer_balance;
    public ?string $sales_price_list;
    public ?float $total_bp_ex_gst;
    public ?float $total_bp_inc_gst;
    public ?float $total_sp_ex_gst;
    public ?float $total_sp_inc_gst;
    public ?float $order_discount;
    public ?float $discount_rate;
    public ?float $discount_amount;
    public ?float $grand_total_sp_ex_gst;
    public ?float $grand_total_sp_inc_gst;
    public ?string $bill_instructions;
    public ?string $bill_address;
    public ?string $bill_suburb;
    public ?string $bill_state;
    public ?string $bill_postcode;
    public ?string $bill_country;
    public ?string $ship_building_name;
    public ?string $ship_instructions;
    public ?string $ship_address;
    public ?string $created_at;
    public ?string $updated_at;
    public ?int $job_id;
    public ?int $pinboard_status_id;
    public ?float $total;
    public ?string $ship_address_two;
    public ?string $ship_suburb;
    public ?string $ship_state;
    public ?string $ship_postcode;
    public ?string $ship_country;
    public ?int $is_active;
    public ?string $name;
    public ?string $phone;
    public ?string $address;
    public CustomerDetailsResponse $customerDetails;
    public ?array $pinboardItems;
    public ?array $itemImages;
    public ?array $productIds;
    /** Set when enriching pinboard for lead flows (e.g. PinboardRepository). */
    public ?array $customerInfo = null;
    public ?array $productItems = null;
    public ?array $leadItems = null;
    public ?PinboardStatusResponse $pinboard_status = null;

    public function __construct(stdClass $data) 
    {
        $payload = (array) $data;

        $intFields = [
            'pinboard_id', 'company_id', 'dispatch_location_id', 'account_manager_id', 'project_manager_id',
            'user_id', 'customer_id', 'organisation_id', 'job_id', 'pinboard_status_id', 'is_active',
        ];
        foreach ($intFields as $field) {
            $this->{$field} = self::toNullableInt($payload[$field] ?? null);
        }

        $floatFields = [
            'deposit_percentage', 'gst', 'customer_balance', 'total_bp_ex_gst', 'total_bp_inc_gst',
            'total_sp_ex_gst', 'total_sp_inc_gst', 'order_discount', 'discount_rate', 'discount_amount',
            'grand_total_sp_ex_gst', 'grand_total_sp_inc_gst', 'total',
        ];
        foreach ($floatFields as $field) {
            $this->{$field} = self::toNullableFloat($payload[$field] ?? null);
        }

        $stringFields = [
            'uuid', 'reference_number', 'job_title', 'pinboard_name', 'pinboard_description', 'contact_number',
            'customer_po_number', 'expiry_date', 'organisation_code', 'organisation_name', 'zoho_id',
            'terms', 'bill_to', 'ship_to', 'site_contacts', 'sales_price_list', 'bill_instructions',
            'bill_address', 'bill_suburb', 'bill_state', 'bill_postcode', 'bill_country',
            'ship_building_name', 'ship_instructions', 'ship_address', 'created_at', 'updated_at',
            'ship_address_two', 'ship_suburb', 'ship_state', 'ship_postcode', 'ship_country','name', 'phone', 'address',
        ];
        foreach ($stringFields as $field) {
            $this->{$field} = self::toNullableString($payload[$field] ?? null);
        }

        $this->customerDetails = new CustomerDetailsResponse($data);
        $this->pinboardItems = $this->parsePinboardItems($payload['pinboard_items'] ?? null);
        $this->itemImages = array_filter($this->pinboardItems, function($item){
            return $item['type'] == 'images';
        });

        if (isset($payload['pinboard_status'])) {
            $status = $payload['pinboard_status'];
            if ($status instanceof PinboardStatusResponse) {
                $this->pinboard_status = $status;
            } elseif (is_array($status) || $status instanceof stdClass) {
                $this->pinboard_status = new PinboardStatusResponse(
                    $status instanceof stdClass ? $status : $status
                );
            }
        }
    }

    private function parsePinboardItems($pinboardItems): ?array
    {
        // if (empty($pinboardItems)) {
        //     return null;
        // }

        // // If it's already an array, return it
        // if (is_array($pinboardItems)) {
        //     return $pinboardItems;
        // }

        // // If it's a JSON string, decode it
        // if (is_string($pinboardItems)) {
        //     $decoded = json_decode($pinboardItems, true);
        //     return is_array($decoded) ? $decoded : null;
        // }

        $items = [];
        if(isset($pinboardItems)){
            // Handle case where pinboardItems might be a JSON string
            $pinboardItems = is_string($pinboardItems) ? json_decode($pinboardItems, true) : $pinboardItems;

            $productIds = [];
            
            if(is_array($pinboardItems) || is_object($pinboardItems)){
                foreach($pinboardItems as $index => $item){
                    if(!isset($item['pinboard_item_id']) || !$item['pinboard_item_id']) {
                        unset($pinboardItems[$index]);
                        continue;
                    }
                    $pinboardItem = [];
                    $pinboardItem['pinboard_id'] = $item['pinboard_id'] ?? null;
                    $pinboardItem['pinboard_item_id'] = $item['pinboard_item_id'] ?? null;
                    $pinboardItem['description']  = $item['description'] ?? null;
                    $pinboardItem['quantity'] = $item['quantity'] ?? 0;
                    $pinboardItem['unit_price'] = $item['unit_price'] ?? 0;
                    $pinboardItem['photo'] = $item['photo'] ?? '';
                    $pinboardItem['product_url'] = $item['product_url'] ?? null;
                    $pinboardItem['sort_order'] = $item['sort_order'] ?? 0;
                    $pinboardItem['type'] = $item['model_type'] ?? '';
                    $pinboardItem['model_type'] = $item['model_type'] ?? '';
                    $pinboardItem['comments'] = $item['comments'] ?? '';
                    $pinboardItem['model_id'] = $item['model_id'] ?? null;

                    if(isset($item['model']) && $item['model']){
                        $pinboardItem = array_merge($pinboardItem, $item['model']);
                        switch($item['model_type']){
                            case 'product':
                                $pinboardItem['title'] = isset($pinboardItem['product_title']) ? $pinboardItem['product_title'] : '';
                                $productIds[] = $pinboardItem['model_id'];
                                break;
                            case 'project':
                                $pinboardItem['title'] = $pinboardItem['project_title'] ?? '';
                                break;
                            case 'media':
                                $pinboardItem['title'] = $pinboardItem['media_title'] ?? '';
                                break;
                            case 'comment':
                                $pinboardItem['title'] = $pinboardItem['comment_title'] ?? '';
                                break;
                            case 'post':
                                $pinboardItem['title'] = $pinboardItem['post_title'] ?? '';
                                break;
                            default:
                                $pinboardItem['title'] = $pinboardItem['description'] ?? '';
                        }
                        
                    }
                    $items[] = $pinboardItem;

                }
            }
            $this->productIds = $productIds;
       }
       
        usort($items, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        return $items;
    }
    private static function toNullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function toNullableFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private static function toNullableString(mixed $value): ?string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return null;
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }
}

class PinboardStatusResponse
{
    public ?int $order_status_id;
    public ?int $language_id;
    public ?string $name;
    public ?int $sort_order;

    public function __construct(stdClass|array $data)
    {
        $payload = $data instanceof stdClass ? (array) $data : $data;

        $this->order_status_id = is_numeric($payload['order_status_id'] ?? null)
            ? (int) $payload['order_status_id']
            : null;
        $this->language_id = is_numeric($payload['language_id'] ?? null)
            ? (int) $payload['language_id']
            : null;
        $this->name = isset($payload['name']) ? trim((string) $payload['name']) : null;
        $this->sort_order = is_numeric($payload['sort_order'] ?? null)
            ? (int) $payload['sort_order']
            : null;
    }
}

class CustomerDetailsResponse
{
    public ?int $customer_id;
    public ?string $organisation_code;
    public ?int $organisation_id;
    public ?string $organisation_name;
    public ?string $zoho_id;
    public ?string $terms;
    public ?float $deposit_percentage;
    public ?float $gst;
    public ?string $bill_to;
    public ?string $ship_to;
    public ?string $site_contacts;
    public ?float $customer_balance;
    public ?string $customer_name;
    public ?string $customer_phone;
    public ?string $customer_email;
    public function __construct(stdClass $data)
    {
        $payload = (array) $data;
        $this->customer_id = is_numeric($payload['customer_id'] ?? null) ? (int) $payload['customer_id'] : null;
        $this->organisation_code = isset($payload['organisation_code']) ? (string) $payload['organisation_code'] : null;
        $this->organisation_id = is_numeric($payload['organisation_id'] ?? null) ? (int) $payload['organisation_id'] : null;
        $this->organisation_name = isset($payload['organisation_name']) ? (string) $payload['organisation_name'] : null;
        $this->zoho_id = isset($payload['zoho_id']) ? (string) $payload['zoho_id'] : null;
        $this->terms = isset($payload['terms']) ? (string) $payload['terms'] : null;
        $this->deposit_percentage = is_numeric($payload['deposit_percentage'] ?? null) ? (float) $payload['deposit_percentage'] : null;
        $this->gst = is_numeric($payload['gst'] ?? null) ? (float) $payload['gst'] : null;
        $this->bill_to = isset($payload['bill_to']) ? (string) $payload['bill_to'] : null;
        $this->ship_to = isset($payload['ship_to']) ? (string) $payload['ship_to'] : null;
        $this->site_contacts = isset($payload['site_contacts']) ? (string) $payload['site_contacts'] : null;
        $this->customer_balance = is_numeric($payload['customer_balance'] ?? null) ? (float) $payload['customer_balance'] : null;
        $this->customer_name = isset($payload['customer_name']) ? (string) $payload['customer_name'] : null;
        $this->customer_phone = isset($payload['customer_phone']) ? (string) $payload['customer_phone'] : null;
        $this->customer_email = isset($payload['customer_email']) ? (string) $payload['customer_email'] : null;
    }
}

class PinboardData
{
    public ?int $pinboard_id;
    public ?int $job_id;
    public ?string $uuid;
    public ?string $reference_number;
    public ?int $company_id;
    public ?int $dispatch_location_id;
    public ?string $job_title;
    public ?string $pinboard_name;
    public ?string $pinboard_description;
    public ?int $account_manager_id;
    public ?int $project_manager_id;
    public ?int $user_id;
    public ?int $customer_id;
    public ?string $contact_number = null;
    public ?string $customer_po_number;
    public ?string $expiry_date;
    public ?string $organisation_code;
    public ?int $organisation_id;
    public ?string $organisation_name;
    public ?string $zoho_id;
    public ?string $terms;
    public ?float $deposit_percentage;
    public ?float $gst;
    public ?string $bill_to;
    public ?string $ship_to;
    public ?string $site_contacts;
    public ?float $customer_balance;
    public ?string $sales_price_list;
    public ?float $total_bp_ex_gst;
    public ?float $total_bp_inc_gst;
    public ?float $total_sp_ex_gst;
    public ?float $total_sp_inc_gst;
    public ?float $order_discount;
    public ?float $discount_rate;
    public ?float $discount_amount;
    public ?float $grand_total_sp_ex_gst;
    public ?float $grand_total_sp_inc_gst;
    public ?string $bill_instructions;
    public ?string $bill_address;
    public ?string $bill_suburb;
    public ?string $bill_state;
    public ?string $bill_postcode;
    public ?string $bill_country;
    public ?string $ship_building_name;
    public ?string $ship_instructions;
    public ?string $ship_address;
    public ?string $created_at;
    public ?string $updated_at;
    public ?int $pinboard_status_id;
    public ?float $total;
    public ?string $ship_address_two;
    public ?string $ship_suburb;
    public ?string $ship_state;
    public ?string $ship_postcode;
    public ?string $ship_country;
    public ?int $is_active;
    public ?string $name;
    public ?string $phone;
    public ?string $address;
    public CustomerDetails $customerDetails;
    public ?array $pinboardItems;

    public function __construct(array $data = [])
    {
        $details = is_array($data['pinboardDetails'] ?? null) ? $data['pinboardDetails'] : [];
        $customer = is_array($data['customerDetails'] ?? null) ? $data['customerDetails'] : [];
        $totals = is_array($data['pinboardTotals'] ?? null) ? $data['pinboardTotals'] : [];
        $billing = is_array($data['billingAddress'] ?? null) ? $data['billingAddress'] : [];
        $shipping = is_array($data['shippingAddress'] ?? null) ? $data['shippingAddress'] : [];

        $this->pinboard_id = self::toNullableInt($data['pinboard_id'] ?? null);
        $this->job_id = self::toNullableInt($data['job_id'] ?? 1);
        $this->uuid = self::toNullableString($data['uuid'] ?? null);
        $this->reference_number = self::toNullableString($data['reference_number'] ?? ($details['referenceNumber'] ?? null));
        $this->company_id = self::toNullableInt($data['company_id'] ?? ($details['companyId'] ?? null));
        $this->dispatch_location_id = self::toNullableInt($data['dispatch_location_id'] ?? ($details['dispatchLocationId'] ?? null));
        $this->job_title = self::toNullableString($data['job_title'] ?? ($details['jobTitle'] ?? null));
        $this->pinboard_name = self::toNullableString($data['pinboard_name'] ?? null);
        $this->pinboard_description = self::toNullableString($data['pinboard_description'] ?? ($details['pinboardDescription'] ?? null));
        $this->account_manager_id = self::toNullableInt($data['account_manager_id'] ?? ($details['accountManagerId'] ?? null));
        $this->project_manager_id = self::toNullableInt($data['project_manager_id'] ?? ($details['projectManagerId'] ?? null));
        $this->user_id = self::toNullableInt($data['user_id'] ?? ($details['userId'] ?? null));
        $this->customer_id = self::toNullableInt($data['customer_id'] ?? ($details['customerId'] ?? ($customer['customer_id'] ?? null)));
        $this->contact_number = self::toNullableString($data['contact_number'] ?? null);
        $this->customer_po_number = self::toNullableString($data['customer_po_number'] ?? ($details['customerPO'] ?? null));
        $this->expiry_date = self::normalizeDate($data['expiry_date'] ?? ($details['expiryDate'] ?? null));
        $this->organisation_code = self::toNullableString($data['organisation_code'] ?? ($customer['organisationCode'] ?? null));
        $this->organisation_id = self::toNullableInt($data['organisation_id'] ?? ($customer['organisationId'] ?? null));
        $this->organisation_name = self::toNullableString($data['organisation_name'] ?? ($customer['organisationName'] ?? null));
        $this->zoho_id = self::toNullableString($data['zoho_id'] ?? ($customer['zohoId'] ?? null));
        $this->terms = self::toNullableString($data['terms'] ?? ($customer['terms'] ?? null));
        $this->deposit_percentage = self::toNullableFloat($data['deposit_percentage'] ?? ($customer['deposit'] ?? null));
        $this->gst = self::toNullableFloat($data['gst'] ?? ($customer['gst'] ?? null));
        $this->bill_to = self::toNullableString($data['bill_to'] ?? ($customer['billTo'] ?? null));
        $this->ship_to = self::toNullableString($data['ship_to'] ?? ($customer['shipTo'] ?? null));
        $this->site_contacts = self::toNullableString($data['site_contacts'] ?? ($customer['siteContacts'] ?? null));
        $this->customer_balance = self::toNullableFloat($data['customer_balance'] ?? ($customer['customerBalance'] ?? null));
        $this->sales_price_list = self::toNullableString($data['sales_price_list'] ?? ($totals['salesPriceList'] ?? null));
        $this->total_bp_ex_gst = self::toNullableFloat($data['total_bp_ex_gst'] ?? ($totals['totalBpExGst'] ?? null));
        $this->total_bp_inc_gst = self::toNullableFloat($data['total_bp_inc_gst'] ?? ($totals['totalBpIncGst'] ?? null));
        $this->total_sp_ex_gst = self::toNullableFloat($data['total_sp_ex_gst'] ?? ($totals['totalSpExGst'] ?? null));
        $this->total_sp_inc_gst = self::toNullableFloat($data['total_sp_inc_gst'] ?? ($totals['totalSpIncGst'] ?? null));
        $this->order_discount = self::toNullableFloat($data['order_discount'] ?? ($totals['orderDiscount'] ?? null));
        $this->discount_rate = self::toNullableFloat($data['discount_rate'] ?? ($totals['discountRate'] ?? null));
        $this->discount_amount = self::toNullableFloat($data['discount_amount'] ?? ($totals['discountAmount'] ?? null));
        $this->grand_total_sp_ex_gst = self::toNullableFloat($data['grand_total_sp_ex_gst'] ?? ($totals['grandTotalSpExGst'] ?? null));
        $this->grand_total_sp_inc_gst = self::toNullableFloat($data['grand_total_sp_inc_gst'] ?? ($totals['grandTotalSpIncGst'] ?? null));
        $this->bill_instructions = self::toNullableString($data['bill_instructions'] ?? ($billing['instructions'] ?? null));
        $this->bill_address = self::toNullableString($data['bill_address'] ?? ($billing['address'] ?? null));
        $this->bill_suburb = self::toNullableString($data['bill_suburb'] ?? ($billing['suburb'] ?? null));
        $this->bill_state = self::toNullableString($data['bill_state'] ?? ($billing['state'] ?? null));
        $this->bill_postcode = self::toNullableString($data['bill_postcode'] ?? ($billing['postcode'] ?? null));
        $this->bill_country = self::toNullableString($data['bill_country'] ?? ($billing['country'] ?? null));
        $this->ship_building_name = self::toNullableString($data['ship_building_name'] ?? ($shipping['buildingName'] ?? null));
        $this->ship_instructions = self::toNullableString($data['ship_instructions'] ?? ($shipping['instructions'] ?? null));
        $this->ship_address = self::toNullableString($data['ship_address'] ?? ($shipping['address'] ?? null));
        $this->created_at = currentDateTime($this->dispatch_location_id ?? 1);
        $this->updated_at = currentDateTime($this->dispatch_location_id ?? 1);
        $this->pinboard_status_id = self::toNullableInt($data['pinboard_status_id'] ?? 1);
        $this->total = self::toNullableFloat($data['total'] ?? 0);
        $this->ship_address_two = self::toNullableString($data['ship_address_two'] ?? null);
        $this->ship_suburb = self::toNullableString($data['ship_suburb'] ?? ($shipping['suburb'] ?? null));
        $this->ship_state = self::toNullableString($data['ship_state'] ?? ($shipping['state'] ?? null));
        $this->ship_postcode = self::toNullableString($data['ship_postcode'] ?? ($shipping['postcode'] ?? null));
        $this->ship_country = self::toNullableString($data['ship_country'] ?? ($shipping['country'] ?? null));
        $this->is_active = self::toNullableInt($data['is_active'] ?? 0);
        $this->name = self::toNullableString($data['name'] ?? ($data['companyName'] ?? null));
        $this->phone = self::toNullableString($data['phone'] ?? ($data['contact_number'] ?? null));
        $this->address = self::toNullableString($data['address'] ?? null);

        $this->customerDetails = new CustomerDetails($customer);
        $this->pinboardItems = $this->parsePinboardItems($data['pinboardItems'] ?? ($data['pinboard_item'] ?? null));
    }

    public function toArray(): array
    {
        return [
            'pinboard_id' => $this->pinboard_id,
            'uuid' => $this->uuid,
            'job_id' => $this->job_id,
            'reference_number' => $this->reference_number,
            'company_id' => $this->company_id,
            'dispatch_location_id' => $this->dispatch_location_id,
            'job_title' => $this->job_title,
            'pinboard_name' => $this->pinboard_name,
            'pinboard_description' => $this->pinboard_description,
            'account_manager_id' => $this->account_manager_id,
            'project_manager_id' => $this->project_manager_id,
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id ?? $this->customerDetails->customer_id,
            'contact_number' => $this->contact_number,
            'customer_po_number' => $this->customer_po_number,
            'expiry_date' => $this->expiry_date,
            'organisation_code' => $this->organisation_code ?? $this->customerDetails->organisation_code,
            'organisation_id' => $this->organisation_id ?? $this->customerDetails->organisation_id,
            'organisation_name' => $this->organisation_name ?? $this->customerDetails->organisation_name,
            'zoho_id' => $this->zoho_id ?? $this->customerDetails->zoho_id,
            'terms' => $this->terms ?? $this->customerDetails->terms,
            'gst' => $this->gst ?? $this->customerDetails->gst,
            'bill_to' => $this->bill_to ?? $this->customerDetails->bill_to,
            'ship_to' => $this->ship_to ?? $this->customerDetails->ship_to,
            'site_contacts' => $this->site_contacts ?? $this->customerDetails->site_contacts,
            'customer_balance' => $this->customer_balance ?? $this->customerDetails->customer_balance,
            'sales_price_list' => $this->sales_price_list,
            'total_bp_ex_gst' => $this->total_bp_ex_gst,
            'total_bp_inc_gst' => $this->total_bp_inc_gst,
            'total_sp_ex_gst' => $this->total_sp_ex_gst,
            'total_sp_inc_gst' => $this->total_sp_inc_gst,
            'order_discount' => $this->order_discount,
            'discount_rate' => $this->discount_rate,
            'discount_amount' => $this->discount_amount,
            'grand_total_sp_ex_gst' => $this->grand_total_sp_ex_gst,
            'grand_total_sp_inc_gst' => $this->grand_total_sp_inc_gst,
            'bill_instructions' => $this->bill_instructions,
            'bill_address' => $this->bill_address,
            'bill_suburb' => $this->bill_suburb,
            'bill_state' => $this->bill_state,
            'bill_postcode' => $this->bill_postcode,
            'bill_country' => $this->bill_country,
            'ship_building_name' => $this->ship_building_name,
            'ship_instructions' => $this->ship_instructions,
            'ship_address' => $this->ship_address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pinboard_status_id' => $this->pinboard_status_id,
            'total' => $this->total,
            'ship_address_two' => $this->ship_address_two,
            'ship_suburb' => $this->ship_suburb,
            'ship_state' => $this->ship_state,
            'ship_postcode' => $this->ship_postcode,
            'ship_country' => $this->ship_country,
            'is_active' => $this->is_active
        ];
    }
    public static function getUpdateFieldsFromRequest(array $data): array
    {
        $details = is_array($data['pinboardDetails'] ?? null) ? $data['pinboardDetails'] : [];
        $customer = is_array($data['customerDetails'] ?? null) ? $data['customerDetails'] : [];
        $totals = is_array($data['pinboardTotals'] ?? null) ? $data['pinboardTotals'] : [];
        $billing = is_array($data['billingAddress'] ?? null) ? $data['billingAddress'] : [];
        $shipping = is_array($data['shippingAddress'] ?? null) ? $data['shippingAddress'] : [];

        $fieldSources = [
            'job_id' => [[$data, 'job_id']],
            'uuid' => [[$data, 'uuid']],
            'reference_number' => [[$data, 'reference_number'], [$details, 'referenceNumber']],
            'company_id' => [[$data, 'company_id'], [$details, 'companyId']],
            'dispatch_location_id' => [[$data, 'dispatch_location_id'], [$details, 'dispatchLocationId']],
            'job_title' => [[$data, 'job_title'], [$details, 'jobTitle']],
            'pinboard_name' => [[$data, 'pinboard_name']],
            'pinboard_description' => [[$data, 'pinboard_description'], [$details, 'pinboardDescription']],
            'account_manager_id' => [[$data, 'account_manager_id'], [$details, 'accountManagerId']],
            'project_manager_id' => [[$data, 'project_manager_id'], [$details, 'projectManagerId']],
            'user_id' => [[$data, 'user_id'], [$details, 'userId']],
            'customer_id' => [[$data, 'customer_id'], [$details, 'customerId'], [$customer, 'customer_id'], [$customer, 'customerId']],
            'contact_number' => [[$data, 'contact_number']],
            'customer_po_number' => [[$data, 'customer_po_number'], [$details, 'customerPO']],
            'expiry_date' => [[$data, 'expiry_date'], [$details, 'expiryDate']],
            'organisation_code' => [[$data, 'organisation_code'], [$customer, 'organisationCode']],
            'organisation_id' => [[$data, 'organisation_id'], [$customer, 'organisationId']],
            'organisation_name' => [[$data, 'organisation_name'], [$customer, 'organisationName']],
            'zoho_id' => [[$data, 'zoho_id'], [$customer, 'zohoId']],
            'terms' => [[$data, 'terms'], [$customer, 'terms']],
            'gst' => [[$data, 'gst'], [$customer, 'gst']],
            'bill_to' => [[$data, 'bill_to'], [$customer, 'billTo']],
            'ship_to' => [[$data, 'ship_to'], [$customer, 'shipTo']],
            'site_contacts' => [[$data, 'site_contacts'], [$customer, 'siteContacts']],
            'customer_balance' => [[$data, 'customer_balance'], [$customer, 'customerBalance']],
            'sales_price_list' => [[$data, 'sales_price_list'], [$totals, 'salesPriceList']],
            'total_bp_ex_gst' => [[$data, 'total_bp_ex_gst'], [$totals, 'totalBpExGst']],
            'total_bp_inc_gst' => [[$data, 'total_bp_inc_gst'], [$totals, 'totalBpIncGst']],
            'total_sp_ex_gst' => [[$data, 'total_sp_ex_gst'], [$totals, 'totalSpExGst']],
            'total_sp_inc_gst' => [[$data, 'total_sp_inc_gst'], [$totals, 'totalSpIncGst']],
            'order_discount' => [[$data, 'order_discount'], [$totals, 'orderDiscount']],
            'discount_rate' => [[$data, 'discount_rate'], [$totals, 'discountRate']],
            'discount_amount' => [[$data, 'discount_amount'], [$totals, 'discountAmount']],
            'grand_total_sp_ex_gst' => [[$data, 'grand_total_sp_ex_gst'], [$totals, 'grandTotalSpExGst']],
            'grand_total_sp_inc_gst' => [[$data, 'grand_total_sp_inc_gst'], [$totals, 'grandTotalSpIncGst']],
            'bill_instructions' => [[$data, 'bill_instructions'], [$billing, 'instructions']],
            'bill_address' => [[$data, 'bill_address'], [$billing, 'address']],
            'bill_suburb' => [[$data, 'bill_suburb'], [$billing, 'suburb']],
            'bill_state' => [[$data, 'bill_state'], [$billing, 'state']],
            'bill_postcode' => [[$data, 'bill_postcode'], [$billing, 'postcode']],
            'bill_country' => [[$data, 'bill_country'], [$billing, 'country']],
            'ship_building_name' => [[$data, 'ship_building_name'], [$shipping, 'buildingName']],
            'ship_instructions' => [[$data, 'ship_instructions'], [$shipping, 'instructions']],
            'ship_address' => [[$data, 'ship_address'], [$shipping, 'address']],
            'pinboard_status_id' => [[$data, 'pinboard_status_id']],
            'total' => [[$data, 'total']],
            'ship_address_two' => [[$data, 'ship_address_two']],
            'ship_suburb' => [[$data, 'ship_suburb'], [$shipping, 'suburb']],
            'ship_state' => [[$data, 'ship_state'], [$shipping, 'state']],
            'ship_postcode' => [[$data, 'ship_postcode'], [$shipping, 'postcode']],
            'ship_country' => [[$data, 'ship_country'], [$shipping, 'country']],
            'is_active' => [[$data, 'is_active']],
        ];

        $fields = [];
        foreach ($fieldSources as $field => $sources) {
            foreach ($sources as [$source, $key]) {
                if (array_key_exists($key, $source)) {
                    $fields[] = $field;
                    break;
                }
            }
        }

        return array_values(array_unique($fields));
    }

    public function toArrayForUpdate(array $fields = []): array
    {
        return array_intersect_key($this->toArray(), array_flip($fields));
    }

    private function parsePinboardItems(mixed $pinboardItems): ?array
    {
        if (empty($pinboardItems)) {
            return null;
        }
        if (is_array($pinboardItems)) {
            return $pinboardItems;
        }
        if (is_string($pinboardItems)) {
            $decoded = json_decode($pinboardItems, true);
            return is_array($decoded) ? $decoded : null;
        }
        return null;
    }

    private static function toNullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function toNullableFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private static function toNullableString(mixed $value): ?string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return null;
        }
        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    private static function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $timestamp = strtotime((string) $value);
        return $timestamp === false ? null : date('Y-m-d', $timestamp);
    }

    // public static function normalizeCurrentDate(?int $dispatchLocationId = null): ?string
    // {
    //     $iana = self::DISPATCH_LOCATION_TIMEZONES[$dispatchLocationId ?? 0]
    //         ?? self::DEFAULT_TIMEZONE;

    //     return (new DateTimeImmutable('now', new DateTimeZone($iana)))->format('Y-m-d H:i:s');
    // }
}

class CustomerDetails
{
    public ?int $customer_id;
    public ?string $organisation_code;
    public ?int $organisation_id;
    public ?string $organisation_name;
    public ?string $zoho_id;
    public ?string $terms;
    public ?float $deposit_percentage;
    public ?float $gst;
    public ?string $bill_to;
    public ?string $ship_to;
    public ?string $site_contacts;
    public ?float $customer_balance;
    public ?string $gmail_Id;

    public function __construct(array $data = [])
    {
        $this->customer_id = is_numeric($data['customer_id'] ?? ($data['customerId'] ?? null)) ? (int) ($data['customer_id'] ?? $data['customerId']) : null;
        $this->organisation_code = isset($data['organisation_code']) ? (string) $data['organisation_code'] : (isset($data['organisationCode']) ? (string) $data['organisationCode'] : null);
        $this->organisation_id = is_numeric($data['organisation_id'] ?? ($data['organisationId'] ?? null)) ? (int) ($data['organisation_id'] ?? $data['organisationId']) : null;
        $this->organisation_name = isset($data['organisation_name']) ? (string) $data['organisation_name'] : (isset($data['organisationName']) ? (string) $data['organisationName'] : null);
        $this->zoho_id = isset($data['zoho_id']) ? (string) $data['zoho_id'] : (isset($data['zohoId']) ? (string) $data['zohoId'] : null);
        $this->terms = isset($data['terms']) ? (string) $data['terms'] : null;
        $this->deposit_percentage = is_numeric($data['deposit_percentage'] ?? ($data['deposit'] ?? null)) ? (float) ($data['deposit_percentage'] ?? $data['deposit']) : null;
        $this->gst = is_numeric($data['gst'] ?? null) ? (float) $data['gst'] : null;
        $this->bill_to = isset($data['bill_to']) ? (string) $data['bill_to'] : (isset($data['billTo']) ? (string) $data['billTo'] : null);
        $this->ship_to = isset($data['ship_to']) ? (string) $data['ship_to'] : (isset($data['shipTo']) ? (string) $data['shipTo'] : null);
        $this->site_contacts = isset($data['site_contacts']) ? (string) $data['site_contacts'] : (isset($data['siteContacts']) ? (string) $data['siteContacts'] : null);
        $this->customer_balance = is_numeric($data['customer_balance'] ?? ($data['customerBalance'] ?? null)) ? (float) ($data['customer_balance'] ?? $data['customerBalance']) : null;
        $this->gmail_Id = isset($data['gmail_Id']) ? (string) $data['gmail_Id'] : null;
    }
}


class PinboardResponseData
extends PinboardResponse
{
    public ?array $pinboard_item;

    public function __construct(stdClass $data)
    {
        parent::__construct($data);
        $this->pinboard_item = $this->pinboardItems;
    }
}