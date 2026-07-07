<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductOptionDataValidation extends Validation
{
    public stdClass $productOption;
    // public stdClass $variant_content;

    public function __construct(array $data, 
    array $requiredFields = [], 
    array $textFields = [], 
    array $existingData = ['existingProductOptionMap' => [], 'groupsMap' => []], 
    array $optionIds = [])
    {
        $optionImagePath = "/media/item-options/";
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->productOption = new stdClass();

        if(!isset($data['option']) && !isset($data['product_option_id'])){
            $this->addError('option + product_option_id', 'Either Option name or product option id is required');
            return;
        }

        

        //check if issset product_id and product_variant_id
        $uniqueIdentifier = null;
        $groupUniqueIdentifier = null;
        if(isset($data['product_id']) && isset($data['product_variant_id']) && isset($data['product_option_group_id'])) {
            $this->productOption->product_id = $this->validateInteger($data['product_id'], 'product_id', 0, true);
            $this->productOption->product_variant_id = $this->validateInteger($data['product_variant_id'], 'product_variant_id', 0, true);
            $this->productOption->product_option_group_id = $this->validateInteger($data['product_option_group_id'], 'product_option_group_id', 0, true);
        } else {
            if(isset($data['web_product']) && isset($data['product_variant']) && isset($data['option_group']) && isset($data['option'])) {
                $uniqueIdentifier = $data['web_product'] 
                                    .'-'. $data['product_variant']
                                    .'-'.$data['option_group']
                                    .'-'.$data['option'];
                $groupUniqueIdentifier = $data['web_product'] 
                                    .'-'. $data['product_variant']
                                    .'-'.$data['option_group'];
            }else{
                if(!isset($data['web_product'])){
                    $this->addError('web_product_code', 'Product code is required');
                };
                if(!isset($data['product_variant'])){
                    $this->addError('web_product_variant', 'Product variant is required');
                };
                if(!isset($data['option_group'])){
                    $this->addError('option_group', 'Option group is required');
                };
            }
        }


        // Check if product option group is exist
        if($uniqueIdentifier && isset($existingData['existingProductOptionMap'][$uniqueIdentifier])) {
            $this->productOption->product_option_id = $existingData['existingProductOptionMap'][$uniqueIdentifier];
            $this->isExistingData = true;
        }
        // PRODUCT OPTION EXISTING DATA
        if (isset($data['product_option_id '])) {
            if (isset($optionIds[$data['product_option_id']])) {
                $this->productOption->product_option_id  = $this->validateInteger($data['product_option_id '], 'product_option_id ', 0, true);
                $this->isExistingData = true;
            }
        }


        //Check if only varaint_code and product_code is set
        if($groupUniqueIdentifier && isset($existingData['groupsMap'][$groupUniqueIdentifier])){
            $this->productOption->product_id = $existingData['groupsMap'][$groupUniqueIdentifier]['product_id']??null;
            $this->productOption->product_variant_id = $existingData['groupsMap'][$groupUniqueIdentifier]['product_variant_id']??null;
            $this->productOption->product_option_group_id = $existingData['groupsMap'][$groupUniqueIdentifier]['product_option_group_id']??null;
        }
        //Check if product_id and product_variant_id is not set and the record is not existing
        if(!$this->isExistingData 
        && (!isset($this->productOption->product_id)
        || !isset($this->productOption->product_variant_id)
        || !isset($this->productOption->product_option_group_id))
        ){
            if(!isset($this->productOption->product_id)){
                $this->addError('web_product', 'Product code not found');
            }
            if(!isset($this->productOption->product_variant_id)){
                $this->addError('product_variant', 'Product variant not found');
            }
            if(!isset($this->productOption->product_option_group_id)){
                $this->addError('option_group', 'Option group not found');
            }
        }
        
        // Set other properties 
        if(isset($data['option'])) $this->productOption->option_name = $this->validateString($data['option'], 'option', 191, true);
        if(isset($data['option_description'])) $this->productOption->option_description = $this->validateString($data['option_description'], 'option_description', 191, true);
        if(isset($data['price'])) $this->productOption->price = $this->validateInteger($data['price'], 'price', 0, true);
        if(isset($data['sort_order'])) $this->productOption->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0, true);
        if(isset($data['active_status'])) $this->productOption->active_status = $this->validateInteger($data['active_status'], 'active_status', 0, true);
        if(isset($data['type_id'])) $this->productOption->type_id = $this->validateInteger($data['type_id'], 'type_id', 1, true);
        if(isset($data['hex_color'])) $this->productOption->hex_color = $this->validateString($data['hex_color'], 'hex_color', 50);
        if(isset($data['option_image'])) $this->productOption->option_image = $this->validateJson($data['option_image'], 'option_image', $optionImagePath) ?? null;
    }

    public function toArray(): array
    {
        return (array) $this->productOption;
    }
}
