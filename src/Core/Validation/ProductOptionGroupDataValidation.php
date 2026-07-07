<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductOptionGroupDataValidation extends Validation
{
    public stdClass $productOptionGroup;
    // public stdClass $variant_content;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], 
    array $existingData = ['existingProductOptionGroupMap' => [], 'variantsMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->productOptionGroup = new stdClass();

        if(!isset($data['option_group']) && !isset($data['product_option_group_id'])){
            $this->addError('option_group + product_option_group_id', 'Either Option group name or product option group id is required');
            return;
        }

        //check if issset product_id and product_variant_id
        $uniqueIdentifier = null;
        $variantUniqueIdentifier = null;
        
        if(isset($data['product_id']) && isset($data['product_variant_id'])) {
            $this->productOptionGroup->product_id = $this->validateInteger($data['product_id'], 'product_id', 0, true);
            $this->productOptionGroup->product_variant_id = $this->validateInteger($data['product_variant_id'], 'product_variant_id', 0, true);
        }
        else {
            if(isset($data['web_product_code']) && isset($data['web_product_variant'])) {
                $uniqueIdentifier = $data['web_product_code'] . '-' . $data['web_product_variant'].'-'.$data['option_group'];
                $variantUniqueIdentifier = $data['web_product_code'] . '-' . $data['web_product_variant'];
            }else{
                if(!isset($data['web_product_code'])){
                    $this->addError('web_product_code', 'Product code is required');
                };
                if(!isset($data['web_product_variant'])){
                    $this->addError('web_product_variant', 'Product variant is required');
                };
            }
        }


        // Check if product option group is exist
        if($uniqueIdentifier && isset($existingData['existingProductOptionGroupMap'][$uniqueIdentifier])) {
            $this->productOptionGroup->product_option_group_id = $existingData['existingProductOptionGroupMap'][$uniqueIdentifier];
            $this->isExistingData = true;
        }
         //Check if csv has product_option_group_id and if it is exist in existing data
        if (isset($data['product_option_group_id'])) {
            if (isset($existingData['existingProductOptionGroupMap'][$data['product_option_group_id']])) {
                $this->productOptionGroup->product_option_group_id  = $this->validateInteger($data['product_option_group_id '], 'product_option_group_id ', 0, true);
                $this->isExistingData = true;
            }
        }

        //Check if only varaint_code and product_code is set
        // if($variantUniqueIdentifier && isset($existingData['variantsMap'][$variantUniqueIdentifier])){
            $this->productOptionGroup->product_id = ($variantUniqueIdentifier && isset($existingData['variantsMap'][$variantUniqueIdentifier])) 
            ? $existingData['variantsMap'][$variantUniqueIdentifier]['product_id'] ?? null : null;
            $this->productOptionGroup->product_variant_id = ($variantUniqueIdentifier && isset($existingData['variantsMap'][$variantUniqueIdentifier])) 
            ? $existingData['variantsMap'][$variantUniqueIdentifier]['product_variant_id']??null : null;
        // }
        //Check if product_id and product_variant_id is not set and the record is not existing
        if(!$this->productOptionGroup->product_id && !$this->productOptionGroup->product_variant_id){
            if(!$this->productOptionGroup->product_id){
                $this->addError('web_product_code', 'Product code not found');
            }
            if(!$this->productOptionGroup->product_variant_id){
                $this->addError('web_product_variant', 'Product variant not found');
            }
        }
        
        // Set other properties 
        if(isset($data['option_group'])) $this->productOptionGroup->option_group_name = $this->validateString($data['option_group'], 'option_group', 191, true);
        if(isset($data['option_group_description'])) $this->productOptionGroup->option_group_description = $this->validateString($data['option_group_description'], 'option_group_description', 191, true);
        if(isset($data['sort_order'])) $this->productOptionGroup->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0, true);
        if(isset($data['active_status'])) $this->productOptionGroup->active_status = $this->validateInteger($data['active_status'], 'active_status', 0, true);
        if(isset($data['type'])) $this->productOptionGroup->group_type = $this->validateString($data['type'], 'type', 191);


    }

    public function toArray(): array
    {
        return [
            'productOptionGroup' => (array)$this->productOptionGroup,
        ];
    }
}
