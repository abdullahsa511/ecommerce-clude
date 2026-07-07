<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductTypeDataValidation extends Validation
{
    public stdClass $productType;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = [])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->productType = new stdClass();
        $image_path = '/media/Product-type/'; // TODO: change to the actual path

        // PRODUCT TYPE ID
        if (isset($data['product_type_id'])) {
            $this->productType->product_type_id = $this->validateInteger($data['product_type_id'], 'product_type_id', 0, true);
            if (isset($existingData[$data['name']])) {
                $this->productType->product_type_id = $existingData[$data['name']];
                $this->isExistingData = true;
            }
        }

        // PRODUCT TYPE DATA
        if (isset($data['name']) && !empty($data['name']) && $data['name']) {
            // required fields
            $this->productType->name = $this->validateString($data['name'], 'name', 191, true);
            $this->productType->type = $this->validateString($data['type'], 'type', 191, true);
            // optional fields
            $this->productType->plural = $this->validateString($data['plural'], 'plural', 191);
            $this->productType->icon = $this->validateString($data['icon'], 'icon', 191);
            $image = $this->validateString($data['image'], 'image', 191);
            $this->productType->image = $image_path . $image;
            $this->productType->source = $this->validateString($data['source'], 'source', 191);
            $this->productType->site_id = $this->validateInteger($data['site_id'], 'site_id', 0);
            // existing data
            if (isset($existingData[$this->productType->name])) {
                $this->productType->product_type_id = $existingData[$this->productType->name];
                $this->isExistingData = true;
            }
        } else {
            $this->addError('name', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return (array) $this->productType;
    }
}
