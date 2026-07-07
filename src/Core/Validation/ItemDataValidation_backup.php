<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;


class ItemDataValidation extends Validation
{
    public stdClass $item;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $maps = ['productMap' => [], 'variantMap' => [], 'itemMap' => []])
    {
        parent::__construct($requiredFields, $textFields);
        // -----------------------------
        // Initialize item objects
        // stdClass is used to hold validated item and item content
        // -----------------------------
        $this->item = new stdClass();
        $this->rawData = $data;

        if(isset($data['item_id'])) {
            $itemIds = array_values($maps['itemMap']);
            $this->item->item_id = $this->validateInteger($data['item_id'], 'item_id', 0, true);
            if(in_array($this->item->item_id, $itemIds)) {
                $this->isExistingData = true;
            }
        }

        if(isset($data['web_product_range'])) {
            $product_code = $this->validateString($data['web_product_range'], 'web_product_range', 191);
            $product_code = str_replace(' ', '-', strtolower(trim($product_code)));
            if(!isset($maps['productMap'][$product_code])){
                $this->addError('web_product_range', 'Product code not found');
            }else{
                $this->item->product_id = $maps['productMap'][$product_code];
            }
        }

        if(isset($data['web_product_variant'])) {
            $variant_code = $this->validateString($data['web_product_variant'], 'web_product_variant', 191);
            $variant_code = str_replace(' ', '-', strtolower(trim($variant_code)));
            if(!isset($maps['variantMap'][$variant_code])){
                $this->addError('web_product_variant', 'Variant code not found');
            }else{
                $this->item->product_variant_id = $maps['variantMap'][$variant_code];
            }
        }

        if (isset($data['item_id'])) $this->item->item_id = $this->validateInteger($data['item_id'], 'item_id') ?? null;
        if (isset($data['km_item_id'])) $this->item->km_item_id = $this->validateInteger($data['km_item_id'], 'km_item_id', 0) ?? 0;
        if (isset($data['item_type_id'])) $this->item->item_type_id = $this->validateInteger($data['item_type_id'], 'item_type_id', 1, true) ?? 1;
        if (isset($data['class_id'])) $this->item->class_id = $this->validateInteger($data['class_id'], 'class_id', 1) ?? 1;
        if (isset($data['company_id'])) $this->item->company_id = $this->validateInteger($data['company_id'], 'company_id', 1) ?? 1;
        if (isset($data['admin_id'])) $this->item->admin_id = $this->validateInteger($data['admin_id'], 'admin_id', 1) ?? 1;
        if (isset($data['parent_id'])) $this->item->parent_id = $this->validateInteger($data['parent_id'], 'parent_id') ?? null;

        // String fields
        // -----------------------------
        // Validate string fields with max lengths
        // Prevent database errors and truncate long values
        // -----------------------------
        if (isset($data['model'])) $this->item->model = $this->validateString($data['model'], 'model', 64) ?? '';
        if (isset($data['description'])) $this->item->description = $this->validateString($data['description'], 'description', 500) ?? '';
        if (isset($data['specifications'])) $this->item->specifications = $this->validateString($data['specifications'], 'specifications', 1000) ?? '';
        if (isset($data['warranty_period'])) $this->item->warranty_period = $this->validateString($data['warranty_period'], 'warranty_period', 10) ?? '';
        if (isset($data['item_code'])) $this->item->item_code = $this->validateString($data['item_code'], 'item_code', 50, true) ?? '';
        if (isset($data['factory_code'])) $this->item->factory_code = $this->validateString($data['factory_code'], 'factory_code', 255) ?? '';
        if (isset($data['sku'])) $this->item->sku = $this->validateString($data['sku'], 'sku', 64) ?? '';
        if (isset($data['isbn'])) $this->item->isbn = $this->validateString($data['isbn'], 'isbn', 17) ?? '';
        if (isset($data['barcode'])) $this->item->barcode = $this->validateString($data['barcode'], 'barcode', 13) ?? '';

        // Boolean fields as integers
        // -----------------------------
        // Boolean / tinyint fields as integers
        // Converts true/false or empty strings to 0/1 for DB
        // -----------------------------
        if (isset($data['track_stock'])) $this->item->track_stock = $this->validateInteger($data['track_stock'], 'track_stock', 0) ?? 0;
        if (isset($data['requires_shipping'])) $this->item->requires_shipping = $this->validateInteger($data['requires_shipping'], 'requires_shipping', 0) ?? 0;
        if (isset($data['subtract_stock'])) $this->item->subtract_stock = $this->validateInteger($data['subtract_stock'], 'subtract_stock', 0) ?? 0;
        if (isset($data['status'])) $this->item->status = $this->validateInteger($data['status'], 'status', 0) ?? 0;
        if (isset($data['is_featured'])) $this->item->is_featured = $this->validateInteger($data['is_featured'], 'is_featured', 0) ?? 0;
        if (isset($data['active'])) $this->item->active = $this->validateInteger($data['active'], 'active', 0) ?? 0;
        if (isset($data['archive'])) $this->item->archive = $this->validateInteger($data['archive'], 'archive', 0) ?? 0;

        // Integer fields
        // -----------------------------
        // Integer fields for stock, lead times, and IDs
        // Ensures numeric values are safe for DB
        // -----------------------------
        if (isset($data['stock_quantity'])) $this->item->stock_quantity = $this->validateInteger($data['stock_quantity'], 'stock_quantity', 0) ?? 0;
        if (isset($data['stock_status_id'])) $this->item->stock_status_id = $this->validateInteger($data['stock_status_id'], 'stock_status_id', 1) ?? 1;
        if (isset($data['lead_days'])) $this->item->lead_days = $this->validateInteger($data['lead_days'], 'lead_days', 0) ?? 0;
        if (isset($data['melbourne_lead_days'])) $this->item->melbourne_lead_days = $this->validateInteger($data['melbourne_lead_days'], 'melbourne_lead_days', 0) ?? 0;
        if (isset($data['safety_stock'])) $this->item->safety_stock = $this->validateInteger($data['safety_stock'], 'safety_stock', 0) ?? 0;
        if (isset($data['qty_alert'])) $this->item->qty_alert = $this->validateInteger($data['qty_alert'], 'qty_alert', 0) ?? 0;
        if (isset($data['manufacturer_id'])) $this->item->manufacturer_id = $this->validateInteger($data['manufacturer_id'], 'manufacturer_id') ?? null;
        if (isset($data['vendor_id'])) $this->item->vendor_id = $this->validateInteger($data['vendor_id'], 'vendor_id') ?? null;
        if (isset($data['import_vendor_id'])) $this->item->import_vendor_id = $this->validateInteger($data['import_vendor_id'], 'import_vendor_id') ?? null;
        if (isset($data['factory_vendor_id'])) $this->item->factory_vendor_id = $this->validateInteger($data['factory_vendor_id'], 'factory_vendor_id') ?? null;
        if (isset($data['item_range_id'])) $this->item->item_range_id = $this->validateInteger($data['item_range_id'], 'item_range_id') ?? null;
        // if (isset($data['category'])) $this->item->item_category_id = $categories[$data['category']] ?? null;
        // if (isset($data['item_category_id'])) $this->item->item_category_id = $this->validateInteger($data['item_category_id'], 'item_category_id', 1) ?? 1;
        if (isset($data['edgetape_colour_id'])) $this->item->edgetape_colour_id = $this->validateInteger($data['edgetape_colour_id'], 'edgetape_colour_id') ?? null;
        if (isset($data['tax_type_id'])) $this->item->tax_type_id = $this->validateInteger($data['tax_type_id'], 'tax_type_id') ?? null;
        if (isset($data['weight_type_id'])) $this->item->weight_type_id = $this->validateInteger($data['weight_type_id'], 'weight_type_id') ?? null;
        if (isset($data['length_type_id'])) $this->item->length_type_id = $this->validateInteger($data['length_type_id'], 'length_type_id') ?? null;
        if (isset($data['min_order_quantity'])) $this->item->min_order_quantity = $this->validateInteger($data['min_order_quantity'], 'min_order_quantity', 1) ?? 1;
        if (isset($data['views'])) $this->item->views = $this->validateInteger($data['views'], 'views', 0) ?? 0;
        if (isset($data['sort_order'])) $this->item->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0) ?? 0;
        if (isset($data['project_price_qty'])) $this->item->project_price_qty = $this->validateInteger($data['project_price_qty'], 'project_price_qty') ?? null;

        // Float fields
        // -----------------------------
        // Float fields
        // Converts numeric values to decimals, defaults if missing
        // -----------------------------
        if (isset($data['weight'])) $this->item->weight = $this->validateFloat($data['weight'], 'weight', 0.0) ?? 0.0;
        if (isset($data['length'])) $this->item->length = $this->validateFloat($data['length'], 'length', 0.0) ?? 0.0;
        if (isset($data['width'])) $this->item->width = $this->validateFloat($data['width'], 'width') ?? null;
        if (isset($data['height'])) $this->item->height = $this->validateFloat($data['height'], 'height') ?? null;
        if (isset($data['depth'])) $this->item->depth = $this->validateFloat($data['depth'], 'depth') ?? null;
        if (isset($data['price'])) $this->item->price = $this->validateFloat($data['price'], 'price') ?? null;
        if (isset($data['old_price'])) $this->item->old_price = $this->validateFloat($data['old_price'], 'old_price') ?? null;
        if (isset($data['carton_qm'])) $this->item->carton_qm = $this->validateFloat($data['carton_qm'], 'carton_qm') ?? null;
        if (isset($data['carton_width'])) $this->item->carton_width = $this->validateFloat($data['carton_width'], 'carton_width', 0.0) ?? 0.0;
        if (isset($data['carton_depth'])) $this->item->carton_depth = $this->validateFloat($data['carton_depth'], 'carton_depth', 0.0) ?? 0.0;
        if (isset($data['carton_height'])) $this->item->carton_height = $this->validateFloat($data['carton_height'], 'carton_height', 0.0) ?? 0.0;
        if (isset($data['gross_weight'])) $this->item->gross_weight = $this->validateFloat($data['gross_weight'], 'gross_weight') ?? null;
        if (isset($data['project_price_discount'])) $this->item->project_price_discount = $this->validateFloat($data['project_price_discount'], 'project_price_discount', 0.0) ?? 0.0;

        // Additional string fields
        // -----------------------------
        // String fields
        // Converts string values to string, defaults if missing
        // -----------------------------
        if (isset($data['material'])) $this->item->material = $this->validateString($data['material'], 'material', 64) ?? '';
        if (isset($data['out_of_stock_status'])) $this->item->out_of_stock_status = $this->validateString($data['out_of_stock_status'], 'out_of_stock_status', 100) ?? '';
        if (isset($data['size'])) $this->item->size = $this->validateString($data['size'], 'size', 255) ?? '';
        if (isset($data['date_available'])) $this->item->date_available = $this->validateString($data['date_available'], 'date_available', 255) ?? '';
        if (isset($data['template'])) $this->item->template = $this->validateString($data['template'], 'template', 191) ?? '';
        if (isset($data['video_link'])) $this->item->video_link = $this->validateString($data['video_link'], 'video_link', 191) ?? '';

        // JSON fields
        // -----------------------------
        // JSON fields
        // Converts string values to JSON, defaults if missing
        // -----------------------------
        if (isset($data['image'])) $this->item->image = $this->validateJson($data['image'], 'image', 'image') ?? null;
        // if (isset($data['specifications_image'])) $this->item->specifications_image = $this->validateJson($data['specifications_image'], 'specifications_image') ?? null;
        // if (isset($data['banner_image'])) $this->item->banner_image = $this->validateJson($data['banner_image'], 'banner_image', 'banner') ?? null;
        // if (isset($data['image_thumb'])) $this->item->image_thumb = $this->validateJson($data['image_thumb'], 'image_thumb', 'thumbnails') ?? null;
        // if (isset($data['main_image_one'])) $this->item->main_image_one = $this->validateJson($data['main_image_one'], 'main_image_one', 'main-image-one') ?? null;
        // if (isset($data['main_image_two'])) $this->item->main_image_two = $this->validateJson($data['main_image_two'], 'main_image_two', 'main-image-two') ?? null;
        // if (isset($data['feature_image_one'])) $this->item->feature_image_one = $this->validateJson($data['feature_image_one'], 'feature_image_one', 'feature') ?? null;
        // if (isset($data['feature_image_two'])) $this->item->feature_image_two = $this->validateJson($data['feature_image_two'], 'feature_image_two', 'feature') ?? null;
        // if (isset($data['feature_image_three'])) $this->item->feature_image_three = $this->validateJson($data['feature_image_three'], 'feature_image_three', 'feature') ?? null;

       
    }

    public function toArray(): array
    {
        return (array) $this->item;
    }
}
