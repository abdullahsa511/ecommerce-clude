<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class CustomerDataValidation extends Validation
{
    public stdClass $customer;
    public stdClass $user;
    public stdClass $billingAddress;
    public stdClass $shippingAddress;

    public function __construct(
        array $data,
        array $requiredFields = [],
        array $textFields = [],
        array $existingData = ["customerMap" => [], "customerIds" => [], "companyIds" => [], "countryIds" => [], "userIds" => []]
    ) {
        $this->rawData = $data;
        
        // Set required fields for customer
        $requiredFields = $requiredFields ?: [
            'user_id',
            'organisation_id',
            'uuid',
            'org_code',
            'name',
        ];
        // Set text fields for customer
        $textFields = $textFields ?: [
            'name',
            'org_code',
            'abn',
            'website',
            'event_group',
            'gmail_Id',
            'bpay_ref',
            'uuid',
            'date_last_invoice',
            'last_updated_on',
        ];
        
        parent::__construct($requiredFields, $textFields);
        $this->customer = new stdClass();
        $this->user = new stdClass();
        $this->billingAddress = new stdClass();
        $this->shippingAddress = new stdClass();

        // Check if customer exists by ID
        if (isset($data['customer_id'])) {
            $this->customer->customer_id = $this->validateInteger($data['customer_id'], 'customer_id', 0);
            if (isset($existingData['customerMap'][$data['customer_id']])) {
                $this->isExistingData = true;
            }
        }

        // Check if customer exists by org_code
        if (isset($data['org_code'])) {
            $orgCode = trim($data['org_code']);
            if (isset($existingData['customerMap'][$orgCode])) {
                $this->customer->customer_id = $existingData['customerMap'][$orgCode];
                $this->isExistingData = true;
            }
        }

        // Required fields
        $this->customer->user_id = $this->validateInteger($data['user_id'] ?? 0, 'user_id', 0, true);
        $this->customer->organisation_id = $this->validateInteger($data['organisation_id'] ?? 0, 'organisation_id', 0, true);
        
        // Generate UUID if not provided (uuid is binary(16) in DB but we'll handle as string)
        $uuid = isset($data['uuid']) ? $data['uuid'] : null;
        if (empty($uuid)) {
            $uuid = bin2hex(random_bytes(16)); // 32 character hex string for binary(16)
        }
        $this->customer->uuid = $this->validateString($uuid, 'uuid', 16, true);
        // if (strlen($uuid) > 16) {
        //     $this->addError('uuid', 'must be a valid uuid');
        //     return null;
        // }
        $this->customer->org_code = $this->validateString($data['org_code'] ?? '', 'org_code', 255, true);
        $this->customer->name = $this->validateString($data['name'] ?? '', 'name', 255, true);
        $this->customer->company_name = $this->validateString($data['company_name'] ?? 'not', 'company_name', 255, false);

        // Numeric fields with defaults
        $this->customer->rating = isset($data['rating']) ? $this->validateFloat($data['rating'], 'rating', 0.00) : 0.00;
        $this->customer->segment_id = isset($data['segment_id']) ? $this->validateInteger($data['segment_id'], 'segment_id', 1) : 1;
        $this->customer->term_id = isset($data['term_id']) ? $this->validateInteger($data['term_id'], 'term_id', 1) : 1;
        $this->customer->credit_limit = isset($data['credit_limit']) ? $this->validateFloat($data['credit_limit'], 'credit_limit', 0.00) : 0.00;
        $this->customer->caution_bad_payer = isset($data['caution_bad_payer']) ? $this->validateInteger($data['caution_bad_payer'], 'caution_bad_payer', 0) : 0;
        $this->customer->is_active = isset($data['is_active']) ? $this->validateInteger($data['is_active'], 'is_active', 1) : 1;
        $this->customer->default_price_list = isset($data['default_price_list']) ? $this->validateInteger($data['default_price_list'], 'default_price_list', 1) : 1;
        $this->customer->deposit_percentage = isset($data['deposit_percentage']) ? $this->validateFloat($data['deposit_percentage'], 'deposit_percentage', 0.00) : 0.00;
        $this->customer->gst = isset($data['gst']) ? $this->validateFloat($data['gst'], 'gst', 0.00) : 0.00;
        $this->customer->is_gmail_lead = isset($data['is_gmail_lead']) ? $this->validateInteger($data['is_gmail_lead'], 'is_gmail_lead', 0) : 0;

        // Optional string fields
        $this->customer->abn = $this->validateString($data['abn'] ?? null, 'abn', 255);
        $this->customer->date_last_invoice =  $this->validateDate($data['date_last_invoice'] ?? null, 'date_last_invoice', 255);
        $this->customer->website = $this->validateString($data['website'] ?? null, 'website', 255);
        $this->customer->event_group = $this->validateString($data['event_group'] ?? null, 'event_group', 255);
        $this->customer->gmail_Id = $this->validateString($data['gmail_Id'] ?? null, 'gmail_Id', 255, true);
        $this->customer->bpay_ref = $this->validateString($data['bpay_ref'] ?? null, 'bpay_ref', 10);
        $this->customer->last_updated_on = $this->validateString($data['last_updated_on'] ?? null, 'last_updated_on', 255);
        $this->customer->created_by = isset($data['created_by']) ? $this->validateInteger($data['created_by'], 'created_by', null) : null;
        $this->customer->phone = isset($data['phone']) ? $this->validateString($data['phone'], 'phone', 255) : '';
        $this->customer->address = isset($data['address']) ? $this->validateString($data['address'], 'address', 255) : null;

        // Validate company_id exists in companyIds if provided
        if (isset($existingData['companyIds']) && !empty($this->customer->company_id)) {
            if (!in_array($this->customer->company_id, $existingData['companyIds'])) {
                $this->addError('company_id', 'Company not found');
            }
        }

        // user
        $this->user->user_group_id = 1;
        $this->user->username = str_replace(' ', '-', strtolower(trim($this->customer->name ?? '')));
        $this->user->password = '123456';
        if(isset($existingData['userIds'][$this->customer->gmail_Id])) {
           $this->isExistingData = true;
        } else {
            $this->user->email = $this->customer->gmail_Id;
        }
        $this->user->phone_number = $this->customer->phone;
        // end of user
        // Validate billing address
        $this->billingAddress->email = $this->customer->gmail_Id;
        $this->billingAddress->first_name = $this->validateString($data['b_first_name'] ?? null, 'b_first_name', 255);
        $this->billingAddress->last_name = $this->validateString($data['b_last_name'] ?? null, 'b_last_name', 255);
        $this->billingAddress->company = $this->validateString($data['b_company'] ?? null, 'b_company', 255);
        $this->billingAddress->address_1 = $this->validateString($data['b_address_1'] ?? null, 'b_address_1', 255);
        $this->billingAddress->address_2 = $this->validateString($data['b_address_2'] ?? null, 'b_address_2', 255);
        $countryName = $this->validateString($data['b_country'] ?? null, 'b_country', 255);
        if(isset($existingData['countryIds'][$countryName])) {
            $this->billingAddress->country_id = $existingData['countryIds'][$countryName];
        } 
        // else {
        //     $this->addError('billing_country', 'Country not found');
        // }
        $this->billingAddress->region_id = $this->validateInteger($data['b_region'] ?? null, 'b_region', 0);
        $this->billingAddress->city = $this->validateString($data['b_city'] ?? null, 'b_city', 255);
        $this->billingAddress->post_code = $this->validateString($data['b_post_code'] ?? null, 'b_post_code', 255);
        $this->billingAddress->is_billing = 1;

        // Validate billing shipping same
        $billingShippingSame = (isset($data['billing_shipping_same']) && (
            $data['billing_shipping_same'] == 'Yes' ||
            $data['billing_shipping_same'] == 'yes' ||
            $data['billing_shipping_same'] == 'Y' ||
            $data['billing_shipping_same'] == 'y' ||
            $data['billing_shipping_same'] == '1' ||
            $data['billing_shipping_same'] == ''
        )) ? true : false;

        // shipping address
        $this->shippingAddress->email = $this->customer->gmail_Id;
        $this->shippingAddress->first_name = ($billingShippingSame) ? $this->billingAddress->first_name : $this->validateString($data['s_first_name'] ?? null, 's_first_name', 255);
        $this->shippingAddress->first_name = ($billingShippingSame) ? $this->billingAddress->first_name : $this->validateString($data['s_first_name'] ?? null, 's_first_name', 255);
        $this->shippingAddress->last_name = ($billingShippingSame) ? $this->billingAddress->last_name : $this->validateString($data['s_last_name'] ?? null, 's_last_name', 255);
        $this->shippingAddress->company = ($billingShippingSame) ? $this->billingAddress->company : $this->validateString($data['s_company'] ?? null, 's_company', 255);
        $this->shippingAddress->address_1 = ($billingShippingSame) ? $this->billingAddress->address_1 : $this->validateString($data['s_address_1'] ?? null, 's_address_1', 255);
        $this->shippingAddress->address_2 = ($billingShippingSame) ? $this->billingAddress->address_2 : $this->validateString($data['s_address_2'] ?? null, 's_address_2', 255);
        $shippingCountryName = $this->validateString($data['s_country'] ?? null, 's_country', 255);
        if($billingShippingSame) {
            $this->shippingAddress->country_id = $this->billingAddress->country_id;
        } else {
            if(isset($existingData['countryIds'][$shippingCountryName])) {
                $this->shippingAddress->country_id = $existingData['countryIds'][$shippingCountryName];
            } 
            // else {
            //     $this->addError('shipping_country', 'Country not found');
            // }
        }
        $this->shippingAddress->region_id = ($billingShippingSame) ? $this->billingAddress->region_id : $this->validateInteger($data['s_region'] ?? null, 's_region', 0);
        $this->shippingAddress->city = ($billingShippingSame) ? $this->billingAddress->city : $this->validateString($data['s_city'] ?? null, 's_city', 255);
        $this->shippingAddress->post_code = ($billingShippingSame) ? $this->billingAddress->post_code : $this->validateString($data['s_post_code'] ?? null, 's_post_code', 255);
        $this->shippingAddress->is_shipping = 1;
    }

    public function toArray(): array
    {
        return [
            'customer' => (array)$this->customer,
            'user' => (array)$this->user,
            'billing_address' => (array)$this->billingAddress,
            'shipping_address' => (array)$this->shippingAddress,
        ];
    }

    public function getUniqueIdentifier(): string
    {
        if (!empty($this->customer->org_code)) {
            return 'customer_' . $this->customer->org_code;
        }

        // Fallback to parent's implementation
        return parent::getUniqueIdentifier();
    }
}

