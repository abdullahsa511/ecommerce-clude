<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductVariantDataValidation extends Validation
{
    public stdClass $variant;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], 
    array $existingData = ["productMap" => [], 'variantMap' => [], 'variantIds' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->variant = new stdClass();
        $path = '/media/Products/items-images/';

        // VARIANT EXISTING DATA
        if (isset($data['product_variant_id'])) {
            $this->variant->product_variant_id = $this->validateInteger($data['product_variant_id'], 'product_variant_id', 0, true);
            if (isset($existingData['variantMap'][$data['product_variant_id']])) {
                $this->isExistingData = true;
            }
        }
      
        // VARIANT TABLE
        if (isset($data['web_product_variant']) && !empty($data['web_product_variant']) && $data['web_product_variant']) {
            $product_code = $this->validateString($data['web_product_code'], 'web_product_code', 191);
            $product_code = str_replace(' ', '-', strtolower(trim($product_code)));
            
            if (!isset($existingData['productMap'][$product_code])) {
                $this->addError('web_product_code', 'Product code not found');
            } else {
                $isAccessories = isset($data['is_accessories']) && !empty($data['is_accessories']) ? 1 : 0;
                $this->variant->is_accessories = $this->validateInteger($isAccessories, 'is_accessories', 0, true);
                $this->variant->product_id = $existingData['productMap'][$product_code];
                $this->variant->variant_name = $this->validateString($data['web_product_variant'], 'web_product_variant', 191);
                // $this->variant->code = str_replace(' ', '-', strtolower(trim($this->variant->variant_name)));
                $this->variant->sort_order = $this->validateInteger($data['sort_order'] ?? 0, 'sort_order', 0, true);
                // Existing data check
                if (isset($existingData['variantMap'][strtolower($this->variant->product_id . '-' . $this->variant->variant_name)])) {
                    $this->variant->product_variant_id = $existingData['variantMap'][strtolower($this->variant->product_id . '-' . $this->variant->variant_name)];
                    $this->isExistingData = true;
                }
                if(isset($data['variant_description'])){
                    $this->variant->variant_description = $this->validateString($data['variant_description'], 'variant_description', 500);
                }
                if(isset($data['image']) && !empty($data['image'])){
                    $this->variant->image = $this->validateJson($data['image'], 'image', $path);
                }
            }
        } else {
            $this->addError('web_product_variant', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return [
            'variant' => (array)$this->variant,
            'variant_content' => (array)$this->variant_content,
        ];
    }
}
