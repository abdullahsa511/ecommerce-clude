<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ManufacturerDataValidation extends Validation
{
    public stdClass $manufacturer;
    // public stdClass $variant_content;

    public function __construct(array $data, 
    array $requiredFields = [], 
    array $textFields = [], 
    array $existingManufacturerIds = [])
    {
        $path = '/media/manufacturers/';
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->manufacturer = new stdClass();

        if(isset($data['manufacturer_id'])){
            $this->manufacturer->manufacturer_id = $this->validateInteger($data['manufacturer_id'], 'manufacturer_id', 0, true);
            if(isset($existingManufacturerIds[$data['manufacturer_code']])){
                $this->manufacturer->manufacturer_id = $existingManufacturerIds[$data['manufacturer_code']];
                $this->isExistingData = true;
            }
        }

        if(isset($data['manufacturer_code']) && isset($existingManufacturerIds[$data['manufacturer_code']])){
            $this->manufacturer->manufacturer_id = $existingManufacturerIds[$data['manufacturer_code']];
            $this->isExistingData = true;
        }

        if(!isset($data['name'])){
            $this->addError('name', 'Name is required');
        }

        //Set manufacturer properties
        $this->manufacturer->manufacturer_code = $this->validateString($data['manufacturer_code'], 'manufacturer_code', 191, true);
        $this->manufacturer->name = $this->validateString($data['name'], 'name', 191, true);
        $this->manufacturer->slug = $this->validateSlug($data['name'], 'slug');
        $this->manufacturer->image = $this->validateJson($data['image'], 'image', $path);
        $this->manufacturer->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0, true);
    }

    public function toArray(): array
    {
        return (array) $this->manufacturer;
    }
}
