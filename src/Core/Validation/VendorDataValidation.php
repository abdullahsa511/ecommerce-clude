<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class VendorDataValidation extends Validation
{
    public stdClass $vendor;
    // public stdClass $variant_content;

    public function __construct(array $data, 
    array $requiredFields = [], 
    array $textFields = [], 
    array $existingVendorIds = [])
    {
        $path = '/media/vendors/';
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->vendor = new stdClass();

        if(isset($data['vendor_id'])){
            $this->vendor->vendor_id = $this->validateInteger($data['vendor_id'], 'vendor_id', 0, true);
            if(isset($existingVendorIds[$data['vendor_code']])){
                $this->vendor->vendor_id = $existingVendorIds[$data['vendor_code']];
                $this->isExistingData = true;
            }
        }

        if(isset($data['vendor_code']) && isset($existingVendorIds[$data['vendor_code']])){
            $this->vendor->vendor_id = $existingVendorIds[$data['vendor_code']];
            $this->isExistingData = true;
        }

        if(!isset($data['name'])){
            $this->addError('name', 'Name is required');
        }

        //Set vendor properties
        $this->vendor->vendor_code = $this->validateString($data['vendor_code'], 'vendor_code', 191, true);
        $this->vendor->name = $this->validateString($data['name'], 'name', 191, true);
        $this->vendor->slug = $this->validateSlug($data['name'], 'slug');
        $this->vendor->image = $this->validateJson($data['image'], 'image', $path);
        $this->vendor->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0, true);
    }

    public function toArray(): array
    {
        return (array) $this->vendor;
    }
}
