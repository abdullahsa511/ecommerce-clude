<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class PinboardDataValidation extends Validation
{
    public stdClass $pinboard;
    public stdClass $pinboard_item;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ['productMap' => [], 'projectMap' => [], 'mediaMap' => [], 'languageMap' => [], 'companyMap' => [], 'jobMap' => [], 'organisationMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->pinboard = new stdClass();
        $this->pinboard_item = new stdClass();

        // PINBOARD TABLE
        // mandatory fields
        $uuid = $this->generateUuid();
        $this->pinboard->uuid = $uuid; // unique id for pinboard
        $this->pinboard->reference_number = isset($data['reference_number']) ? $this->validateString($data['reference_number'], 'reference_number', 255) : null;
        $this->pinboard->company_id = isset($data['company_id'])
            ? $this->validateInteger($data['company_id'], 'company_id', 0, true)
            : (isset($data['company_name'])
                ? ($existingData['companyMap'][$data['company_name']] ?? 1)
                : 1);
        $this->pinboard->job_id = isset($data['job_id'])
            ? $this->validateInteger($data['job_id'], 'job_id', 0, true)
            : (isset($data['job_name'])
                ? ($existingData['jobMap'][$data['job_name']] ?? 1)
                : 1);
        // optional fields
        $this->pinboard->dispatch_location_id = isset($data['dispatch_location_id']) ? $this->validateInteger($data['dispatch_location_id'], 'dispatch_location_id', 0, true) : 0;
        $this->pinboard->job_title = isset($data['job_title']) ? $this->validateString($data['job_title'], 'job_title', 255) : null;
        $this->pinboard->pinboard_description = isset($data['pinboard_description']) ? $this->validateString($data['pinboard_description'], 'pinboard_description', 500) : null;
        $this->pinboard->account_manager_id = isset($data['account_manager_id']) ? $this->validateInteger($data['account_manager_id'], 'account_manager_id', 0, true) : 0;
        $this->pinboard->project_manager_id = isset($data['project_manager_id']) ? $this->validateInteger($data['project_manager_id'], 'project_manager_id', 0, true) : 0;
        $this->pinboard->user_id = isset($data['user_id']) ? $this->validateInteger($data['user_id'], 'user_id', 0, true) : 0;
        $this->pinboard->customer_po_number = isset($data['customer_po_number']) ? $this->validateString($data['customer_po_number'], 'customer_po_number', 255) : null;
        $this->pinboard->expiry_date = isset($data['expiry_date']) ? $this->validateDate($data['expiry_date'], 'expiry_date') : null;
        // $this->pinboard->organisation_code = isset($data['organisation_code']) ? $this->validateString($data['organisation_code'], 'organisation_code', 50) : null;
        $this->pinboard->organisation_id =  isset($data['organisation_code']) ? $existingData['organisationMap'][$data['organisation_code']] : 1 ?? 1;
        // $this->pinboard->organisation_name = isset($data['organisation_name']) ? $this->validateString($data['organisation_name'], 'organisation_name', 255) : null;
        $this->pinboard->zoho_id = isset($data['zoho_id']) ? $this->validateString($data['zoho_id'], 'zoho_id', 100) : null;
        $this->pinboard->terms = isset($data['terms']) ? $this->validateString($data['terms'], 'terms', 255) : null;
        $this->pinboard->deposit_percentage = isset($data['deposit_percentage']) ? $this->validateFloat($data['deposit_percentage'], 'deposit_percentage', 0, true) : 0.00;
        $this->pinboard->gst = isset($data['gst']) ? $this->validateString($data['gst'], 'gst', 20) : null;
        $this->pinboard->bill_to = isset($data['bill_to']) ? $this->validateString($data['bill_to'], 'bill_to', 255) : null;
        $this->pinboard->ship_to = isset($data['ship_to']) ? $this->validateString($data['ship_to'], 'ship_to', 255) : null;
        $this->pinboard->site_contacts = isset($data['site_contacts']) ? $this->validateString($data['site_contacts'], 'site_contacts', 500) : null;
        $this->pinboard->customer_balance = isset($data['customer_balance']) ? $this->validateFloat($data['customer_balance'], 'customer_balance', 0, true) : 0.00;
        $this->pinboard->sales_price_list = isset($data['sales_price_list']) ? $this->validateString($data['sales_price_list'], 'sales_price_list', 255) : null;
        // mandatory fields
        $this->pinboard->total_bp_ex_gst = isset($data['total_bp_ex_gst']) ? $this->validateFloat($data['total_bp_ex_gst'], 'total_bp_ex_gst', 0, true) : 0.00;
        $this->pinboard->total_bp_inc_gst = isset($data['total_bp_inc_gst']) ? $this->validateFloat($data['total_bp_inc_gst'], 'total_bp_inc_gst', 0, true) : 0.00;
        $this->pinboard->total_sp_ex_gst = isset($data['total_sp_ex_gst']) ? $this->validateFloat($data['total_sp_ex_gst'], 'total_sp_ex_gst', 0, true) : 0.00;
        $this->pinboard->total_sp_inc_gst = isset($data['total_sp_inc_gst']) ? $this->validateFloat($data['total_sp_inc_gst'], 'total_sp_inc_gst', 0, true) : 0.00;
        $this->pinboard->order_discount = isset($data['order_discount']) ? $this->validateFloat($data['order_discount'], 'order_discount', 0, true) : 0.00;
        $this->pinboard->discount_rate = isset($data['discount_rate']) ? $this->validateFloat($data['discount_rate'], 'discount_rate', 0, true) : 0.00;
        $this->pinboard->discount_amount = isset($data['discount_amount']) ? $this->validateFloat($data['discount_amount'], 'discount_amount', 0, true) : 0.00;
        $this->pinboard->grand_total_sp_ex_gst = isset($data['grand_total_sp_ex_gst']) ? $this->validateFloat($data['grand_total_sp_ex_gst'], 'grand_total_sp_ex_gst', 0, true) : 0.00;
        $this->pinboard->grand_total_sp_inc_gst = isset($data['grand_total_sp_inc_gst']) ? $this->validateFloat($data['grand_total_sp_inc_gst'], 'grand_total_sp_inc_gst', 0, true) : 0.00;
        $this->pinboard->pinboard_status_id = isset($data['pinboard_status_id']) ? $this->validateFloat($data['pinboard_status_id'], 'pinboard_status_id', 0, true) : 0;
        $this->pinboard->total = isset($data['total']) ? $this->validateFloat($data['total'], 'total', 0, true) : 0.00;
        // billing address fields (optional fields)
        $this->pinboard->bill_instructions = isset($data['bill_instructions']) ? $this->validateString($data['bill_instructions'], 'bill_instructions', 1000) : null;
        $this->pinboard->bill_address = isset($data['bill_address']) ? $this->validateString($data['bill_address'], 'bill_address', 255) : null;
        $this->pinboard->bill_suburb = isset($data['bill_suburb']) ? $this->validateString($data['bill_suburb'], 'bill_suburb', 30) : null;
        $this->pinboard->bill_state = isset($data['bill_state']) ? $this->validateString($data['bill_state'], 'bill_state', 10) : null;
        $this->pinboard->bill_postcode = isset($data['bill_postcode']) ? $this->validateString($data['bill_postcode'], 'bill_postcode', 10) : null;
        $this->pinboard->bill_country = isset($data['bill_country']) ? $this->validateString($data['bill_country'], 'bill_country', 20) : null;

        // PINBOARD ITEM TABLE
        if (isset($data['product_code']) || isset($data['product_id'])) {
            // mandatory fields
            $this->pinboard_item->uuid = $uuid . '-' . rand(1000, 9999); // unique id for pinboard item with random number
            $this->pinboard_item->language_id = isset($data['language_code']) ? $existingData['languageMap'][$data['language_code']] : 1;
            // foreign key fields
            $this->pinboard_item->product_id = isset($data['product_id'])
                ? $this->validateInteger($data['product_id'], 'product_id', 0, true)
                : (isset($data['product_code'])
                    ? ($existingData['productMap'][$data['product_code']] ?? 1)
                    : 1);
            $this->pinboard_item->project_id = isset($data['project_id'])
                ? $this->validateInteger($data['project_id'], 'project_id', 0, true)
                : (isset($data['project_name'])
                    ? ($existingData['projectMap'][$data['project_name']] ?? 1)
                    : 1);

            $this->pinboard_item->media_id = isset($data['media_id'])
                ? $this->validateInteger($data['media_id'], 'media_id', 0, true)
                : (isset($data['media_name'])
                    ? ($existingData['mediaMap'][$data['media_name']] ?? 1)
                    : 1);
            // end foreign key fields
            $this->pinboard_item->comment_id = isset($data['comment_id']) ? $this->validateInteger($data['comment_id'], 'comment_id', 0, true) : null;
            // mandatory fields
            $this->pinboard_item->description = isset($data['description']) ? $this->validateString($data['description'], 'description', 500) : null;
            $this->pinboard_item->quantity = isset($data['quantity']) ? $this->validateInteger($data['quantity'], 'quantity', 0, true) : 0;
            $this->pinboard_item->unit_price = isset($data['unit_price']) ? $this->validateFloat($data['unit_price'], 'unit_price', 0, true) : 0.00;
            $this->pinboard_item->total_price = isset($data['total_price']) ? $this->validateFloat($data['total_price'], 'total_price', 0, true) : 0.00;
            // optional fields
            $this->pinboard_item->photo = isset($data['photo']) ? $this->validateString($data['photo'], 'photo', 255) : null;
            $this->pinboard_item->sort_order = isset($data['sort_order']) ? $this->validateInteger($data['sort_order'], 'sort_order', 0, true) : 0;
        }
    }

    // no need to array for now
    public function toArray(): array
    {
        return [
            'pinboard' => (array)$this->pinboard,
            'pinboard_item' => (array)$this->pinboard_item,
        ];
    }
}
