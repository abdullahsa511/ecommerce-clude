<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;


class VariantItemDataValidation extends Validation
{
    public stdClass $variantItem;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], 
    array $existingData = ['existingItemVariantMap' => [], 'variantsMap' => [], 'itemIdsMap' => []])
    {
        parent::__construct($requiredFields, $textFields);

        $this->variantItem = new stdClass();
        $this->rawData = $data;
        
        if(!isset($data['item_code'])){
            $this->addError('item_code', 'Item code is required');
            return;
        }
        // $item_code = $data['item_code'];
        // $item_id = $existingData['itemIdsMap'][$item_code]??null;
        $this->variantItem->item_id = $existingData['itemIdsMap'][$data['item_code']]??null;
        
        if(!$this->variantItem->item_id){
            $this->addError('item_code', 'Item code not found');
            return;
        }

        $uniqueIdentifier = null;
        $variantUniqueIdentifier = null;

        if(isset($data['product_id']) && isset($data['product_variant_id'])) {
            $this->variantItem->product_id = $this->validateInteger($data['product_id'], 'product_id', 0, true);
            $this->variantItem->product_variant_id = $this->validateInteger($data['product_variant_id'], 'product_variant_id', 0, true);
        } else {
            if(isset($data['web_product_code']) && isset($data['web_product_variant']) && isset($data['item_code'])) {
                $uniqueIdentifier = $data['web_product_code'] 
                                    .'-'. $data['web_product_variant']
                                    .'-'.$data['item_code'];
                $variantUniqueIdentifier = $data['web_product_code'] 
                                    .'-'. $data['web_product_variant'];
            }else{
                if(!isset($data['web_product_code'])){
                    $this->addError('web_product_code', 'Product code is required');
                };
                if(!isset($data['web_product_variant'])){
                    $this->addError('web_product_variant', 'Product variant is required');
                };
            }
        }

        if($uniqueIdentifier && isset($existingData['existingItemVariantMap'][$uniqueIdentifier])) {
            $this->variantItem->variant_item_id = $existingData['existingItemVariantMap'][$uniqueIdentifier];
            $this->isExistingData = true;
        }

        if (isset($data['variant_item_id']) && $data['variant_item_id'] > 0) {
            $this->variantItem->variant_item_id = $this->validateInteger($data['variant_item_id'], 'variant_item_id', 0, true);
            if (isset($maps['variantItemUniqueToIdMap'][$data['variant_item_id']])) {
                $this->isExistingData = true;
            }
        }

        if($variantUniqueIdentifier && isset($existingData['variantsMap'][$variantUniqueIdentifier])){
            $this->variantItem->product_id = $existingData['variantsMap'][$variantUniqueIdentifier]['product_id']??null;
            $this->variantItem->product_variant_id = $existingData['variantsMap'][$variantUniqueIdentifier]['product_variant_id']??null;
        }
        if (!isset($this->variantItem->product_id)) {
            $this->addError('product_id', 'Product was not resolved from the provided data');
            return;
        }
        if (!isset($this->variantItem->product_variant_id)) {
            $this->addError('product_variant_id', 'Product variant was not resolved from the provided data');
            return;
        }
        if(isset($data['sort_order'])) $this->variantItem->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0, true);
        if(isset($data['active_status'])) $this->variantItem->active_status = $this->validateInteger($data['active_status'], 'active_status', 0, true);
        
    }

    public function toArray(): array
    {
        return (array) $this->variantItem;
    }
}
