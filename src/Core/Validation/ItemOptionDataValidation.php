<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;


class ItemOptionDataValidation extends Validation
{
    public stdClass $itemOption;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], 
    array $existingData = ['existingItemOptionMap' => [], 'productOptions' => [], 'itemIdsMap' => [], 'typeIdsMap' => []])
    {
        parent::__construct($requiredFields, $textFields);

        $this->itemOption = new stdClass();
        $this->rawData = $data;

        $optionImagePath = "/media/item-options/";

        if(!isset($data['item_code'])){
            $this->addError('item_code', 'Item code is required');
            return;
        }
        $this->itemOption->item_id = $existingData['itemIdsMap'][$data['item_code']]??null;
        if(!$this->itemOption->item_id){
            $this->addError('item_code', 'Item code not found');
            return;
        }
        $uniqueIdentifier = null;
        $optionUniqueIdentifier = null;
        
        if(isset($data['product_id']) && isset($data['product_variant_id']) && isset($data['product_option_group_id']) && isset($data['product_option_id'])) {
            $this->itemOption->product_id = $this->validateInteger($data['product_id'], 'product_id', 0, true);
            $this->itemOption->product_variant_id = $this->validateInteger($data['product_variant_id'], 'product_variant_id', 0, true);
            $this->itemOption->product_option_group_id = $this->validateInteger($data['product_option_group_id'], 'product_option_group_id', 0, true);
            $this->itemOption->product_option_id = $this->validateInteger($data['product_option_id'], 'product_option_id', 0, true);
        } else {
            if(isset($data['web_product_code']) && isset($data['web_product_variant']) && isset($data['item_code']) && isset($data['option_group']) && isset($data['option'])) {
               // check existing item option map
                $optionUniqueIdentifier =  $data['item_code'] 
                .'-'. $data['web_product_variant']
                .'-'. $data['option_group']
                .'-'. $data['option'];

                // check existing product option map
                $uniqueIdentifier = $data['web_product_code'] 
                .'-'. $data['web_product_variant']
                .'-'. $data['option_group']
                .'-'. $data['option'];
            } else {
                if(!isset($data['web_product_code'])){
                    $this->addError('web_product_code', 'Product code is required');
                }
                if(!isset($data['web_product_variant'])){
                    $this->addError('web_product_variant', 'Product variant is required');
                }
                if(!isset($data['option_group'])){
                    $this->addError('option_group', 'Option group is required');
                }
                if(!isset($data['option'])){
                    $this->addError('option', 'Option name is required');
                }
            }
        }

        // check if item option is exist
        if($uniqueIdentifier && isset($existingData['existingItemOptionMap'][$optionUniqueIdentifier])) {
            $this->itemOption->item_option_id = $existingData['existingItemOptionMap'][$optionUniqueIdentifier];
            $this->isExistingData = true;
        }
        // check if item option id is exist
        if (isset($data['item_option_id']) && $data['item_option_id'] > 0) {
            $this->itemOption->item_option_id = $this->validateInteger($data['item_option_id'], 'item_option_id', 0, true);
            if (isset($existingData['existingItemOptionMap'][$data['item_option_id']])) {
                $this->isExistingData = true;
            }
        }
        //Check if only varaint_code and product_code is set
        if($uniqueIdentifier && isset($existingData['productOptions'][$uniqueIdentifier])){
            $this->itemOption->product_id = $existingData['productOptions'][$uniqueIdentifier]['product_id']??null;
            $this->itemOption->product_variant_id = $existingData['productOptions'][$uniqueIdentifier]['product_variant_id']??null;
            $this->itemOption->product_option_group_id = $existingData['productOptions'][$uniqueIdentifier]['product_option_group_id']??null;
            $this->itemOption->product_option_id = $existingData['productOptions'][$uniqueIdentifier]['product_option_id']??null;
        }

        if(!$this->isExistingData 
        && (!isset($this->itemOption->product_id)
        || !isset($this->itemOption->product_variant_id)
        || !isset($this->itemOption->product_option_group_id)
        || !isset($this->itemOption->product_option_id))
        ){
            if(!isset($this->itemOption->product_id)){
                $this->addError('web_product_code', 'Product code not found');
            }
            if(!isset($this->itemOption->product_variant_id)){
                $this->addError('web_product_variant', 'Product variant not found');
            }
            if(!isset($this->itemOption->product_option_group_id)){
                $this->addError('option_group', 'Option group not found');
            }
            if(!isset($this->itemOption->product_option_id)){
                $this->addError('option', 'Option not found');
            }
        }

        if(isset($data['option'])) $this->itemOption->option_name = $this->validateString($data['option'], 'option', 191, true);
        if(isset($data['description'])) $this->itemOption->option_description = $this->validateString($data['description'], 'description', 191, true);
        if(isset($data['price'])) $this->itemOption->price = $this->validateFloat($data['price'], 'price', 0, true);
        if(isset($data['sort_order'])) $this->itemOption->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0, true);
        if(isset($data['active_status'])) $this->itemOption->active_status = $this->validateInteger($data['active_status'], 'active_status', 0, true);
        if(isset($data['hex_color'])) $this->itemOption->hex_color = $this->validateString($data['hex_color'], 'hex_color', 50);
        if(isset($data['option_image'])) $this->itemOption->option_image = $this->validateJson($data['option_image'], 'option_image', $optionImagePath) ?? null;
        $this->itemOption->type_id = isset($data['type_id']) ? (isset($existingData['typeIdsMap'][$data['type_id']]) ? $existingData['typeIdsMap'][$data['type_id']] : 1) : 1;
    }

    public function toArray(): array
    {
        return (array) $this->itemOption;
    }
}
