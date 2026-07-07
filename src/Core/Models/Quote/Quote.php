<?php

declare(strict_types=1);

namespace App\Core\Models\Quote;

use App\Core\Models\Base\Model;
use App\Core\Models\User;
use stdClass;

class Quote extends Model
{
    protected string $table = 'quote';
    // protected string $tableAlias = 'q';

    protected ?int $quote_id;
    protected ?string $uuid;
    protected ?string $reference_number;
    protected ?int $company_id;
    protected ?int $dispatch_location_id;
    protected ?string $job_title;
    protected ?string $quote_description;
    protected ?int $account_manager_id;
    protected ?int $project_manager_id;
    protected ?int $user_id;
    protected ?string $customer_po_number;
    protected ?string $expiry_date;
    protected ?string $organisation_code;
    protected ?int $quote_status_id;
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

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Get quote items relationship
     */
    public function item()
    {
        return $this->hasMany(QuoteItem::class, 'quote_id', 'quote_id');
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

class QuoteResponse
{
    public ?int $quote_id;
    public QuoteDetailsResponse $quoteDetails;
    public CustomerDetailsResponse $customerDetails;
    public QuoteTotalsResponse $quoteTotals;
    public BillingAddressResponse $billingAddress;
    public ShippingAddressResponse $shippingAddress;

    public function __construct(stdClass $data) 
    {
        $this->quote_id = $data->quote_id ?? null;
        $this->quoteDetails = new QuoteDetailsResponse($data);
        $this->customerDetails = new CustomerDetailsResponse($data);
        $this->quoteTotals = new QuoteTotalsResponse($data);
        $this->billingAddress = new BillingAddressResponse($data);
        $this->shippingAddress = new ShippingAddressResponse($data);
    }
}

class QuoteDetailsResponse
{
    public string $uuid;
    public string $referenceNumber;
    public string $createdByCompany;
    public string $dispatchLocation;
    public string $jobTitle;
    public string $quoteDescription;
    public string $accountManager;
    public string $projectManager;
    public string $createdBy;
    public string $customerPO;
    public string $expiryDate;
    public string $customerName;
    public string $location;

    public function __construct(stdClass $data)
    {
        $this->uuid = $data->uuid ?? '';
        $this->referenceNumber = $data->reference_number ?? '';
        $this->createdByCompany = $data->company_name ?? '';
        $this->dispatchLocation = $data->dispatch_location_name ?? '';
        $this->jobTitle = $data->job_title ?? '';
        $this->quoteDescription = $data->quote_description ?? '';
        $this->accountManager = $data->account_manager_name ?? '';
        $this->projectManager = $data->project_manager_name ?? '';
        $this->createdBy = $data->user_name ?? '';
        $this->customerPO = $data->customer_po_number ?? '';
        $this->expiryDate = $data->expiry_date ?? '';
        $this->customerName = $data->customer_name ?? '';
        $this->location = $data->location ?? '';
    }
}

class CustomerDetailsResponse
{
    public string $organisationCode;
    public string $organisationName;
    public string $zohoId;
    public string $terms;
    public string $deposit;
    public string $gst;
    public string $billTo;
    public string $shipTo;
    public string $siteContacts;
    public string $customerBalance;

    public function __construct(stdClass $data)
    {
        $this->organisationCode = $data->organisation_code ?? '';
        $this->organisationName = $data->organisation_name ?? '';
        $this->zohoId = $data->zoho_id ?? '';
        $this->terms = $data->terms ?? '';
        $this->deposit = (string)($data->deposit_percentage ?? '0');
        $this->gst = $data->gst ?? '';
        $this->billTo = $data->bill_to ?? '';
        $this->shipTo = $data->ship_to ?? '';
        $this->siteContacts = $data->site_contacts ?? '';
        $this->customerBalance = (string)($data->customer_balance ?? '0.00');
    }
}

class QuoteTotalsResponse
{
    public string $salesPriceList;
    public string $totalBpExGst;
    public string $totalBpIncGst;
    public string $totalSpExGst;
    public string $totalSpIncGst;
    public string $orderDiscount;
    public string $discountAmount;
    public string $grandTotalSpExGst;
    public string $grandTotalSpIncGst;

    public function __construct(stdClass $data)
    {
        $this->salesPriceList = $data->sales_price_list ?? '';
        $this->totalBpExGst = number_format((float)($data->total_bp_ex_gst ?? 0), 2, '.', '');
        $this->totalBpIncGst = number_format((float)($data->total_bp_inc_gst ?? 0), 2, '.', '');
        $this->totalSpExGst = number_format((float)($data->total_sp_ex_gst ?? 0), 2, '.', '');
        $this->totalSpIncGst = number_format((float)($data->total_sp_inc_gst ?? 0), 2, '.', '');
        $this->orderDiscount = (string)($data->discount_rate ?? '0');
        $this->discountAmount = number_format((float)($data->discount_amount ?? 0), 2, '.', '');
        $this->grandTotalSpExGst = number_format((float)($data->grand_total_sp_ex_gst ?? 0), 2, '.', '');
        $this->grandTotalSpIncGst = number_format((float)($data->grand_total_sp_inc_gst ?? 0), 2, '.', '');
    }
}

class BillingAddressResponse
{
    public string $instructions;
    public string $address;
    public string $suburb;
    public string $state;
    public string $postcode;
    public string $country;

    public function __construct(stdClass $data)
    {
        $this->instructions = $data->bill_instructions ?? '';
        $this->address = $data->bill_address ?? '';
        $this->suburb = $data->bill_suburb ?? '';
        $this->state = $data->bill_state ?? '';
        $this->postcode = $data->bill_postcode ?? '';
        $this->country = $data->bill_country ?? '';
    }
}

class ShippingAddressResponse
{
    public string $buildingName;
    public string $instructions;
    public string $address;

    public function __construct(stdClass $data)
    {
        $this->buildingName = $data->ship_building_name ?? '';
        $this->instructions = $data->ship_instructions ?? '';
        $this->address = $data->ship_address ?? '';
    }
}

class QuoteData
{
    public ?int $quote_id;
    public ?string $uuid;
    public ?int $job_id;
    public QuoteDetails $quoteDetails;
    public CustomerDetails $customerDetails;
    public QuoteTotals $quoteTotals;
    public BillingAddress $billingAddress;
    public ShippingAddress $shippingAddress;

    public function __construct(array $data = [])
    {
        $this->quote_id = $data['quote_id'] ?? null;
        $this->uuid = $data['uuid'] ?? null;
        $this->job_id = $data['job_id'] ?? 1;
        $this->quoteDetails = new QuoteDetails($data['quoteDetails'] ?? []);
        $this->customerDetails = new CustomerDetails($data['customerDetails'] ?? []);
        $this->quoteTotals = new QuoteTotals($data['quoteTotals'] ?? []);
        $this->billingAddress = new BillingAddress($data['billingAddress'] ?? []);
        $this->shippingAddress = new ShippingAddress($data['shippingAddress'] ?? []);
    }

    public function toArray(): array
    {
        return [
            'quote_id' => $this->quote_id,
            'uuid' => $this->uuid,
            'job_id' => $this->job_id,
            'reference_number' => $this->quoteDetails->reference_number,
            'company_id' => $this->quoteDetails->company_id,
            'dispatch_location_id' => $this->quoteDetails->dispatch_location_id,
            'job_title' => $this->quoteDetails->job_title,
            'quote_description' => $this->quoteDetails->quote_description,
            'account_manager_id' => $this->quoteDetails->account_manager_id,
            'project_manager_id' => $this->quoteDetails->project_manager_id,
            'user_id' => $this->quoteDetails->user_id,
            'customer_po_number' => $this->quoteDetails->customer_po_number,
            'expiry_date' => $this->quoteDetails->expiry_date,
            'organisation_code' => $this->customerDetails->organisation_code,
            'organisation_id' => $this->customerDetails->organisation_id,
            'organisation_name' => $this->customerDetails->organisation_name,
            'zoho_id' => $this->customerDetails->zoho_id,
            'terms' => $this->customerDetails->terms,
            'deposit_percentage' => $this->customerDetails->deposit_percentage,
            'gst' => $this->customerDetails->gst,
            'bill_to' => $this->customerDetails->bill_to,
            'ship_to' => $this->customerDetails->ship_to,
            'site_contacts' => $this->customerDetails->site_contacts,
            'customer_balance' => $this->customerDetails->customer_balance,
            'sales_price_list' => $this->quoteTotals->sales_price_list,
            'total_bp_ex_gst' => $this->quoteTotals->total_bp_ex_gst,
            'total_bp_inc_gst' => $this->quoteTotals->total_bp_inc_gst,
            'total_sp_ex_gst' => $this->quoteTotals->total_sp_ex_gst,
            'total_sp_inc_gst' => $this->quoteTotals->total_sp_inc_gst,
            'order_discount' => $this->quoteTotals->order_discount,
            'discount_rate' => $this->quoteTotals->discount_rate,
            'discount_amount' => $this->quoteTotals->discount_amount,
            'grand_total_sp_ex_gst' => $this->quoteTotals->grand_total_sp_ex_gst,
            'grand_total_sp_inc_gst' => $this->quoteTotals->grand_total_sp_inc_gst,
            'bill_instructions' => $this->billingAddress->bill_instructions,
            'bill_address' => $this->billingAddress->bill_address,
            'bill_suburb' => $this->billingAddress->bill_suburb,
            'bill_state' => $this->billingAddress->bill_state,
            'bill_postcode' => $this->billingAddress->bill_postcode,
            'bill_country' => $this->billingAddress->bill_country,
            'ship_building_name' => $this->shippingAddress->ship_building_name,
            'ship_instructions' => $this->shippingAddress->ship_instructions,
            'ship_address' => $this->shippingAddress->ship_address
        ];
    }
}

class QuoteDetails
{
    public string $reference_number = '';
    public int $company_id = 0;
    public int $dispatch_location_id = 0;
    public string $job_title = '';
    public string $quote_description = '';
    public int $account_manager_id = 0;
    public int $project_manager_id = 0;
    public int $user_id = 0;
    public string $customer_po_number = '';
    public string $expiry_date = '';

    public function __construct(array $data = [])
    {
        $this->reference_number = $data['referenceNumber'] ?? '';
        $this->company_id = $data['companyId'] ?? 0;
        $this->dispatch_location_id = $data['dispatchLocationId'] ?? 0;
        $this->job_title = $data['jobTitle'] ?? '';
        $this->quote_description = $data['quoteDescription'] ?? '';
        $this->account_manager_id = $data['accountManagerId'] ?? 0;
        $this->project_manager_id = $data['projectManagerId'] ?? 0;
        $this->user_id = $data['userId'] ?? 0;
        $this->customer_po_number = $data['customerPO'] ?? '';
        $this->expiry_date = $data['expiryDate'] ?? '';
    }
}

class CustomerDetails
{
    public string $organisation_code = '';
    public int $organisation_id = 0;
    public string $organisation_name = '';
    public string $zoho_id = '';
    public string $terms = '';
    public float $deposit_percentage = 0;
    public string $gst = '';
    public string $bill_to = '';
    public string $ship_to = '';
    public string $site_contacts = '';
    public float $customer_balance = 0;

    public function __construct(array $data = [])
    {
        $this->organisation_code = $data['organisationCode'] ?? '';
        $this->organisation_id = $data['organisationId'] ?? 0;
        $this->organisation_name = $data['organisationName'] ?? '';
        $this->zoho_id = $data['zohoId'] ?? '';
        $this->terms = $data['terms'] ?? '';
        $this->deposit_percentage = (float)($data['deposit'] ?? 0);
        $this->gst = $data['gst'] ?? '';
        $this->bill_to = $data['billTo'] ?? '';
        $this->ship_to = $data['shipTo'] ?? '';
        $this->site_contacts = $data['siteContacts'] ?? '';
        $this->customer_balance = (float)($data['customerBalance'] ?? 0);
    }
}

class QuoteTotals
{
    public string $sales_price_list = '';
    public float $total_bp_ex_gst = 0;
    public float $total_bp_inc_gst = 0;
    public float $total_sp_ex_gst = 0;
    public float $total_sp_inc_gst = 0;
    public float $order_discount = 0;
    public float $discount_rate = 0;
    public float $discount_amount = 0;
    public float $grand_total_sp_ex_gst = 0;
    public float $grand_total_sp_inc_gst = 0;

    public function __construct(array $data = [])
    {
        $this->sales_price_list = $data['salesPriceList'] ?? '';
        $this->total_bp_ex_gst = (float)($data['totalBpExGst'] ?? 0);
        $this->total_bp_inc_gst = (float)($data['totalBpIncGst'] ?? 0);
        $this->total_sp_ex_gst = (float)($data['totalSpExGst'] ?? 0);
        $this->total_sp_inc_gst = (float)($data['totalSpIncGst'] ?? 0);
        $this->order_discount = (float)($data['orderDiscount'] ?? 0);
        $this->discount_rate = (float)($data['discountRate'] ?? 0);
        $this->discount_amount = (float)($data['discountAmount'] ?? 0);
        $this->grand_total_sp_ex_gst = (float)($data['grandTotalSpExGst'] ?? 0);
        $this->grand_total_sp_inc_gst = (float)($data['grandTotalSpIncGst'] ?? 0);
    }
}

class BillingAddress
{
    public string $bill_instructions = '';
    public string $bill_address = '';
    public string $bill_suburb = '';
    public string $bill_state = '';
    public string $bill_postcode = '';
    public string $bill_country = '';

    public function __construct(array $data = [])
    {
        $this->bill_instructions = $data['instructions'] ?? '';
        $this->bill_address = $data['address'] ?? '';
        $this->bill_suburb = $data['suburb'] ?? '';
        $this->bill_state = $data['state'] ?? '';
        $this->bill_postcode = $data['postcode'] ?? '';
        $this->bill_country = $data['country'] ?? '';
    }
}

class ShippingAddress
{
    public string $ship_building_name = '';
    public string $ship_instructions = '';
    public string $ship_address = '';

    public function __construct(array $data = [])
    {
        $this->ship_building_name = $data['buildingName'] ?? '';
        $this->ship_instructions = $data['instructions'] ?? '';
        $this->ship_address = $data['address'] ?? '';
    }
} 