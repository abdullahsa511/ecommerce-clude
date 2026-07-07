<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class CompanyDataValidation extends Validation
{
    public stdClass $company;

    public function __construct(
        array $data,
        array $requiredFields = [],
        array $textFields = [],
        array $existingData = ["companyMap" => [], "companyIds" => []]
    ) {
        $this->rawData = $data;
        
        // Set required fields for company
        $requiredFields = $requiredFields ?: [
            'company_name',
            'company_code',
            'company_prefix',
            'vendor_id',
        ];
        
        // Set text fields for company
        $textFields = $textFields ?: [
            'company_name',
            'company_entity',
            'company_short',
            'company_code',
            'company_prefix',
            'company_trade_name',
            'company_entity_name',
            'phone_main',
            'ship_building',
            'ship_street',
            'ship_suburb',
            'ship_state',
            'ship_postcode',
            'ship_country',
            'bill_building',
            'bill_street',
            'bill_suburb',
            'bill_state',
            'bill_postcode',
            'bill_country',
            'po_box',
            'po_box_suburb',
            'po_box_state',
            'abn',
            'bsb',
            'account_number',
            'vendor_id',
            'krost_org_id',
            'krost_qld_org_id',
            'klein_org_id',
            'meloz_org_id',
            'gregbar_org_id',
        ];
        
        parent::__construct($requiredFields, $textFields);
        $this->company = new stdClass();

        // Check if company exists by ID
        if (isset($data['company_id'])) {
            $this->company->company_id = $this->validateInteger($data['company_id'], 'company_id', 0);
            if (isset($existingData['companyMap'][$data['company_id']])) {
                $this->isExistingData = true;
            }
        }

        // Check if company exists by code
        if (isset($data['company_code'])) {
            $companyCode = trim($data['company_code']);
            if (isset($existingData['companyMap'][$companyCode])) {
                $this->company->company_id = $existingData['companyMap'][$companyCode];
                $this->isExistingData = true;
            }
        }

        // Required fields
        $this->company->company_name = $this->validateString($data['company_name'] ?? '', 'company_name', 255, true);
        $this->company->company_code = $this->validateString($data['company_code'] ?? '', 'company_code', 255, true);
        $this->company->company_prefix = $this->validateString($data['company_prefix'] ?? '', 'company_prefix', 255, true);
        $this->company->vendor_id = $this->validateString($data['vendor_id'] ?? '', 'vendor_id', 255, true);
        $this->company->company_entity = $this->validateString($data['company_entity'] ?? '', 'company_entity', 255, true);
        // $this->company->company_short = $this->validateString($data['company_short'] ?? '', 'company_short', 50, true);

        // Optional fields
        $this->company->sort_order = isset($data['sort_order']) ? $this->validateInteger($data['sort_order'], 'sort_order', 0) : 0;
        $this->company->company_trade_name = $this->validateString($data['company_trade_name'] ?? null, 'company_trade_name', 255);
        $this->company->company_entity_name = $this->validateString($data['company_entity_name'] ?? null, 'company_entity_name', 255);
        $this->company->phone_main = $this->validateString($data['phone_main'] ?? null, 'phone_main', 255);
        $this->company->krost_org_id = $this->validateString($data['krost_org_id'] ?? null, 'krost_org_id', 255);
        $this->company->krost_qld_org_id = $this->validateString($data['krost_qld_org_id'] ?? null, 'krost_qld_org_id', 255);
        $this->company->klein_org_id = $this->validateString($data['klein_org_id'] ?? null, 'klein_org_id', 255);
        $this->company->meloz_org_id = $this->validateString($data['meloz_org_id'] ?? null, 'meloz_org_id', 255);
        $this->company->gregbar_org_id = $this->validateString($data['gregbar_org_id'] ?? null, 'gregbar_org_id', 255);
        
        // Address fields
        $this->company->ship_building = $this->validateString($data['ship_building'] ?? null, 'ship_building', 255);
        $this->company->ship_street = $this->validateString($data['ship_street'] ?? null, 'ship_street', 255);
        $this->company->ship_suburb = $this->validateString($data['ship_suburb'] ?? null, 'ship_suburb', 255);
        $this->company->ship_state = $this->validateString($data['ship_state'] ?? null, 'ship_state', 255);
        $this->company->ship_postcode = $this->validateString($data['ship_postcode'] ?? null, 'ship_postcode', 255);
        $this->company->ship_country = $this->validateString($data['ship_country'] ?? null, 'ship_country', 255);
        $this->company->bill_building = $this->validateString($data['bill_building'] ?? null, 'bill_building', 255);
        $this->company->bill_street = $this->validateString($data['bill_street'] ?? null, 'bill_street', 255);
        $this->company->bill_suburb = $this->validateString($data['bill_suburb'] ?? null, 'bill_suburb', 255);
        $this->company->bill_state = $this->validateString($data['bill_state'] ?? null, 'bill_state', 255);
        $this->company->bill_postcode = $this->validateString($data['bill_postcode'] ?? null, 'bill_postcode', 255);
        $this->company->bill_country = $this->validateString($data['bill_country'] ?? null, 'bill_country', 255);
        $this->company->po_box = $this->validateString($data['po_box'] ?? null, 'po_box', 255);
        $this->company->po_box_suburb = $this->validateString($data['po_box_suburb'] ?? null, 'po_box_suburb', 255);
        $this->company->po_box_state = $this->validateString($data['po_box_state'] ?? null, 'po_box_state', 255);
        
        // Financial fields
        $this->company->abn = $this->validateString($data['abn'] ?? null, 'abn', 255);
        $this->company->bsb = $this->validateString($data['bsb'] ?? null, 'bsb', 255);
        $this->company->account_number = $this->validateString($data['account_number'] ?? null, 'account_number', 255);
        $this->company->bpay_biller_code = isset($data['bpay_biller_code']) ? $this->validateInteger($data['bpay_biller_code'], 'bpay_biller_code', null) : null;
    }

    public function toArray(): array
    {
        return [
            'company' => (array)$this->company,
        ];
    }

    public function getUniqueIdentifier(): string
    {
        if (!empty($this->company->company_code)) {
            return 'company_' . $this->company->company_code;
        }

        // Fallback to parent's implementation
        return parent::getUniqueIdentifier();
    }

}

