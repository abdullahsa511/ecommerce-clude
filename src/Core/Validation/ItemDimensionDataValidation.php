<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;


class ItemDimensionDataValidation extends Validation
{
    public stdClass $item;
    public bool $isItemIdNotExists = false;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $itemMap = [])
    {
        parent::__construct($requiredFields, $textFields);

        $this->item = new stdClass();
        $this->rawData = $data;
        $dimensionImagePath = '/media/Products/dimensions/';

        if(!isset($data['item_code'])){
            $this->addError('item_code', 'Item code is required');
            return;
        }

        // existing item code is not found in the item map
        if(!isset($itemMap[$data['item_code']]['item_id'])){
            $this->isItemIdNotExists = true;
            $this->item->item_id = $itemMap[$data['item_code']]['item_id'];
        }

        // $productCode = $this->validateString($data['product_code'], 'product_code', 255) ?? '';
        $this->item->item_code = (isset($data['item_code']) && !empty($data['item_code'])) ? $this->validateString($data['item_code'], 'item_code', 50, true) : '';

        if(!isset($this->item->item_code)){
            $this->addError('item_code', 'Item code not found');
            return;
        }
        // item code is not found in the item map
        if(!isset($itemMap[$this->item->item_code])){
            $this->addError('item_code', 'Item code not found');
            return;
        }
        $this->item->product_id = $itemMap[$this->item->item_code]['product_id'];
        $this->item->product_variant_id = $itemMap[$this->item->item_code]['product_variant_id'];
        // $this->item->item_id = $itemMap[$this->item->item_code]['item_id'];


        if (isset($data['display_width'])) $this->item->display_width = $this->validateString($data['display_width'], 'display_width', 255) ?? '';
        if (isset($data['display_height'])) $this->item->display_height = $this->validateString($data['display_height'], 'display_height', 255) ?? '';
        if (isset($data['display_depth'])) $this->item->display_depth = $this->validateString($data['display_depth'], 'display_depth', 255) ?? '';
        if(isset($data['dimensions_image'])) $this->item->dimensions_image = $this->validateJson($dimensionImagePath.$data['dimensions_image'], 'dimensions_image') ?? null;
        if(isset($data['is_default'])) $this->item->is_default = $this->validateInteger($data['is_default'], 'is_default', 0, true) ?? 0;
    }

    public function toArray(): array
    {
        return (array) $this->item;
    }
}
