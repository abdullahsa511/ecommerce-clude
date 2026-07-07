<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductAccessoriesValidation extends Validation
{
    public stdClass $productAccessories;
    public function __construct(
        array $data, 
        array $requiredFields = [], 
        array $textFields = [], 
        array $existingData = [
            'productIdMap' => [],
            'itemIdMap' => [],
            'existingDataMaps' => [],
        ]
      
        )
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->productAccessories = new stdClass();

        // validation check
       $validateParentProduct = $this->validateString($data['parent_product'], 'parent_product', 191, true);
       $validateWebProductRange = $this->validateString($data['web_product_range'], 'web_product_range', 191, true);
       $itemCode = $this->validateString($data['item_code'], 'item_code', 191, true);

       // set formatted parent product code and web product range
       $parentProductCode = strtolower(str_replace(' ', '-', $validateParentProduct)); 
       $webProductRange = strtolower(str_replace(' ', '-', $validateWebProductRange)); 

       // set parent product id and product id and item id
       $this->productAccessories->parent_product_id = $existingData['productIdMap'][$parentProductCode] ?? null;
       $this->productAccessories->product_id = $existingData['productIdMap'][$webProductRange] ?? null;
       $this->productAccessories->item_id = $existingData['itemIdMap'][$itemCode] ?? null;
       $this->productAccessories->price = $this->validateFloat($data['price'], 'price', 0, true);


        if(!$this->productAccessories->parent_product_id){
            $this->addError('parent_product', 'Parent product id not found');
        }
        if(!$this->productAccessories->product_id){
            $this->addError('web_product_range', 'Web product range id not found');
        }
        if(!$this->productAccessories->item_id){
            $this->addError('item_code', 'Item code not found');
        }

        // unique identifier
        $uniqueIdentifier = $this->productAccessories->parent_product_id . '-' . $this->productAccessories->product_id . '-' . $this->productAccessories->item_id;
        if(isset($existingData['existingDataMaps'][$uniqueIdentifier])){
            $this->productAccessories->product_accessories_id = $existingData['existingDataMaps'][$uniqueIdentifier];
            $this->isExistingData = true;
        }

        if(isset($data['product_accessories_id'])){
            $this->productAccessories->product_accessories_id = $this->validateInteger($data['product_accessories_id'], 'product_accessories_id', 0, true);
            if(isset($existingData['existingDataMaps'][$uniqueIdentifier])){
                $this->isExistingData = true;
            }
        }


    }


    public function toArray(): array
    {
        return [
            'data' => (array)$this->productAccessories,
        ];
    }
}
