<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;


class ItemDataValidation extends Validation
{
    public stdClass $item;
    public bool $isItemIdExists = false;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], 
    array $maps = ['existingItemMap' => [], 'variantsMap' => [], 'itemIdsMap' => []])
    {
        parent::__construct($requiredFields, $textFields);

        $this->item = new stdClass();
        $this->rawData = $data;
        $imagePath = '/media/Products/items-images/';

        if (isset($data['item_id']) && $data['item_id'] > 0) {
            if (in_array($this->item->item_id, $maps['itemIdsMap'])) {
                $this->item->item_id = $this->validateInteger($data['item_id'], 'item_id', 0, true);
                $this->isExistingData = true;
                $this->isItemIdExists = true;
            }
        }
        if(!isset($data['item_code']) && !isset($data['item_id'])){
            $this->addError('item_code', 'Item code is required');
            return;
        }
        if(!$this->isItemIdExists){
            if(isset($maps['existingItemMap'][$data['item_code']])){
                $this->item->item_id = $maps['existingItemMap'][$data['item_code']];
                $this->isExistingData = true;
            }
        }
        $this->item->item_code = (isset($data['item_code']) && !empty($data['item_code'])) ? $this->validateString($data['item_code'], 'item_code', 50, true) : '';

        // -----------------------------
        // Mandatory Required Fields
        // -----------------------------
        $uniqueIdentifier = null;
        $uniqueVariantIdentifier = null;
        if(isset($data['product_id']) && isset($data['product_variant_id'])){
            $this->item->product_id = $this->validateInteger($data['product_id'], 'product_id', 0, true);
            $this->item->product_variant_id = $this->validateInteger($data['product_variant_id'], 'product_variant_id', 0, true);
        }else{
            if(isset($data['product_code']) && isset($data['product_variant']) && isset($data['item_code'])){
                $uniqueIdentifier = trim($data['product_code']) .'-'. trim($data['product_variant']) .'-'. trim($data['item_code']);
                $uniqueVariantIdentifier = trim($data['product_code']) .'-'. trim($data['product_variant']);
            }else{
                if(!isset($data['product_code'])){
                    $this->addError('product_code', 'Product code is required');
                }
                if(!isset($data['product_variant'])){
                    $this->addError('product_variant', 'Product variant is required');
                }
                if(!isset($data['item_code'])){
                    $this->addError('item_code', 'Item code is required');
                }
            }
        }

        if($uniqueIdentifier && isset($maps['existingItemMap'][$uniqueIdentifier])){
            $this->item->item_id = $maps['existingItemMap'][$uniqueIdentifier];
            $this->isExistingData = true;
        }
        if(isset($data['item_id']) && $data['item_id'] > 0){
            if(isset($maps['itemIdsMap'][$data['item_id']])){
                $this->item->item_id = $maps['itemIdsMap'][$data['item_id']];
                $this->isExistingData = true;
            }
        }
        if(!isset($this->item->item_code) && !isset($this->item->product_id)){
            $this->addError('item_code', 'Item code not found');
            return;
        }

        if($uniqueVariantIdentifier && isset($maps['variantsMap'][$uniqueVariantIdentifier])){
            $this->item->product_id = $maps['variantsMap'][$uniqueVariantIdentifier]['product_id']??null;
            $this->item->product_variant_id = $maps['variantsMap'][$uniqueVariantIdentifier]['product_variant_id']??null;
        }
        if(!isset($this->item->product_id)){
            $this->addError('product_code', 'Product code not found');
        }
        if(!isset($this->item->product_variant_id)){
            $this->addError('product_variant', 'Product variant not found');
        }

        
        // -----------------------------
        // Optional Fields Start Below
        // -----------------------------

        // Integer fields
        if (isset($data['km_item_id'])) $this->item->km_item_id = $this->validateInteger($data['km_item_id'], 'km_item_id', 0) ?? 0;
        if (isset($data['item_type_id'])) $this->item->item_type_id = $this->validateInteger($data['item_type_id'], 'item_type_id', 1, true) ?? 1;
        if (isset($data['vendor_id'])) $this->item->vendor_id = $this->validateInteger($data['vendor_id'], 'vendor_id') ?? null;
        if (isset($data['import_vendor_id'])) $this->item->import_vendor_id = $this->validateInteger($data['import_vendor_id'], 'import_vendor_id') ?? null;
        if (isset($data['factory_vendor_id'])) $this->item->factory_vendor_id = $this->validateInteger($data['factory_vendor_id'], 'factory_vendor_id') ?? null;
        if (isset($data['item_category_id'])) $this->item->item_category_id = $this->validateInteger($data['item_category_id'], 'item_category_id', 1) ?? 1;
        if (isset($data['is_default'])) $this->item->is_default = $this->validateInteger($data['is_default'], 'is_default', 0) ?? 0;
        if (isset($data['sort_order'])) $this->item->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0) ?? 0;
        if (isset($data['lead_days'])) $this->item->lead_days = $this->validateInteger($data['lead_days'], 'lead_days', 0) ?? 0;
        if (isset($data['melbourne_lead_days'])) $this->item->melbourne_lead_days = $this->validateInteger($data['melbourne_lead_days'], 'melbourne_lead_days', 0) ?? 0;
        if (isset($data['brisbane_lead_days'])) $this->item->brisbane_lead_days = $this->validateInteger($data['brisbane_lead_days'], 'brisbane_lead_days', 0) ?? 0;
        if (isset($data['safety_stock'])) $this->item->safety_stock = $this->validateInteger($data['safety_stock'], 'safety_stock', 0) ?? 0;
        if (isset($data['products_per_cartoon'])) $this->item->products_per_cartoon = $this->validateInteger($data['products_per_cartoon'], 'products_per_cartoon') ?? null;
        if (isset($data['project_price_qty'])) $this->item->project_price_qty = $this->validateInteger($data['project_price_qty'], 'project_price_qty') ?? null;

        // String fields
        if (isset($data['web_sku'])) $this->item->web_sku = $this->validateString($data['web_sku'], 'web_sku', 25) ?? '';
        if (isset($data['class'])) $this->item->class = $this->validateString($data['class'], 'class', 50) ?? '';
        if (isset($data['description'])) $this->item->description = $this->validateString($data['description'], 'description', 500) ?? '';
        if (isset($data['specifications'])) $this->item->specifications = $this->validateString($data['specifications'], 'specifications', 1000) ?? '';
        if (isset($data['warranty_period'])) $this->item->warranty_period = $this->validateString($data['warranty_period'], 'warranty_period', 10) ?? '';
        if (isset($data['krost_zoho_id'])) $this->item->krost_zoho_id = $this->validateString($data['krost_zoho_id'], 'krost_zoho_id', 255) ?? '';
        if (isset($data['krost_qld_zoho_id'])) $this->item->krost_qld_zoho_id = $this->validateString($data['krost_qld_zoho_id'], 'krost_qld_zoho_id', 255) ?? '';
        if (isset($data['klein_zoho_id'])) $this->item->klein_zoho_id = $this->validateString($data['klein_zoho_id'], 'klein_zoho_id', 255) ?? '';
        if (isset($data['meloz_zoho_id'])) $this->item->meloz_zoho_id = $this->validateString($data['meloz_zoho_id'], 'meloz_zoho_id', 255) ?? '';
        if (isset($data['gregbar_zoho_id'])) $this->item->gregbar_zoho_id = $this->validateString($data['gregbar_zoho_id'], 'gregbar_zoho_id', 255) ?? '';
        // if (isset($data['quote_image'])) $this->item->quote_image = $this->validateString($data['quote_image'], 'quote_image', 255) ?? '';
        // quote image varchar(255) / not json 
        if (isset($data['quote_image'])) {
            $this->item->quote_image = $this->validateString($data['quote_image'], 'quote_image', 255) ?? '';
            if (!empty($this->item->quote_image)) {
                $this->item->quote_image = $imagePath . $this->item->quote_image;
            }
        }
                
        if (isset($data['delay_until_reason'])) $this->item->delay_until_reason = $this->validateString($data['delay_until_reason'], 'delay_until_reason', 500) ?? '';
        if (isset($data['web_link'])) $this->item->web_link = $this->validateString($data['web_link'], 'web_link', 250) ?? '';
        if (isset($data['user_note'])) $this->item->user_note = $this->validateString($data['user_note'], 'user_note', 500) ?? '';

        // Boolean / tinyint
        if (isset($data['active'])) $this->item->active = $this->validateInteger($data['active'], 'active', 1) ?? 1;
        if (isset($data['track_stock'])) $this->item->track_stock = $this->validateInteger($data['track_stock'], 'track_stock', 0) ?? 0;
        if (isset($data['archive'])) $this->item->archive = $this->validateInteger($data['archive'], 'archive', 0) ?? 0;

        // Float fields
        if (isset($data['width'])) $this->item->width = $this->validateFloat($data['width'] ?? $data['display_width'], 'width', 0.0) ?? 0.0;
        if (isset($data['height'])) $this->item->height = $this->validateFloat($data['height'] ?? $data['display_height'], 'height', 0.0) ?? 0.0;
        if (isset($data['depth'])) $this->item->depth = $this->validateFloat($data['depth'] ?? $data['display_depth'], 'depth', 0.0) ?? 0.0;
        if (isset($data['carton_qm'])) $this->item->carton_qm = $this->validateFloat($data['carton_qm'], 'carton_qm', 0.0) ?? 0.0;
        if (isset($data['carton_width'])) $this->item->carton_width = $this->validateFloat($data['carton_width'], 'carton_width', 0.0) ?? 0.0;
        if (isset($data['carton_depth'])) $this->item->carton_depth = $this->validateFloat($data['carton_depth'], 'carton_depth', 0.0) ?? 0.0;
        if (isset($data['carton_height'])) $this->item->carton_height = $this->validateFloat($data['carton_height'], 'carton_height', 0.0) ?? 0.0;
        if (isset($data['gross_weight'])) $this->item->gross_weight = $this->validateFloat($data['gross_weight'], 'gross_weight', 0.0) ?? 0.0;
        if (isset($data['boradusages_sixteen'])) $this->item->boradusages_sixteen = $this->validateFloat($data['boradusages_sixteen'], 'boradusages_sixteen', 0.0) ?? 0.0;
        if (isset($data['boardusages_eighteen'])) $this->item->boardusages_eighteen = $this->validateFloat($data['boardusages_eighteen'], 'boardusages_eighteen', 0.0) ?? 0.0;
        if (isset($data['boardusages_twentyfive'])) $this->item->boardusages_twentyfive = $this->validateFloat($data['boardusages_twentyfive'], 'boardusages_twentyfive', 0.0) ?? 0.0;
        if (isset($data['boardusages_thirtythree'])) $this->item->boardusages_thirtythree = $this->validateFloat($data['boardusages_thirtythree'], 'boardusages_thirtythree', 0.0) ?? 0.0;

        // Date
        if (isset($data['delay_until'])) $this->item->delay_until = $this->validateDate($data['delay_until'], 'delay_until') ?? null;

        // Discount
        if (isset($data['project_price_discount'])) $this->item->project_price_discount = $this->validateFloat($data['project_price_discount'], 'project_price_discount', 0.0) ?? 0.0;

        
    }

    public function toArray(): array
    {
        return (array) $this->item;
    }
}
