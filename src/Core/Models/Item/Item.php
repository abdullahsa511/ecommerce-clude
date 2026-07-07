<?php

declare(strict_types=1);

namespace App\Core\Models\Item;

use App\Core\Models\Base\Model;
use \stdClass;
use App\Core\Models\Product\Product;
use App\Core\Models\Variant\ProductVariant;

use function App\Core\System\utils\env;

class Item extends Model
{
    public ?int $item_id;
    public ?int $product_id;
    public ?int $product_variant_id;
    public ?int $km_item_id;
    public ?int $vendor_id;
    public ?int $import_vendor_id;
    public ?int $factory_vendor_id;
    public ?int $item_category_id;
    public ?int $item_type_id;
    public ?int $sort_order;
    public ?string $item_code;
    public ?string $web_sku;
    public ?string $class;
    public ?string $description;
    public ?string $specifications;
    public ?string $warranty_period;
    public bool|int|null $active;
    public float|string|null $width;
    public float|string|null $height;
    public float|string|null $depth;
    public ?string $display_width;
    public ?string $display_height;
    public ?string $display_depth;
    public float|string|null $carton_qm;
    public float|string|null $carton_width;
    public float|string|null $carton_depth;
    public float|string|null $carton_height;
    public float|string|null $gross_weight;
    public float|string|null $boardusages_sixteen;
    public float|string|null $boardusages_eighteen;
    public float|string|null $boardusages_twentyfive;
    public float|string|null $boardusages_thirtythree;
    public ?string $krost_zoho_id;
    public ?string $krost_qld_zoho_id;
    public ?string $meloz_zoho_id;
    public ?string $gregbar_zoho_id;
    public ?string $klein_zoho_id;
    public ?int $lead_days;
    public ?int $melbourne_lead_days;
    public ?int $brisbane_lead_days;
    public ?int $safety_stock;
    public string|array|null $quote_image;
    public ?string $delay_until;
    public ?string $delay_until_reason;
    public ?string $web_link;
    public ?int $products_per_cartoon;
    public bool|int|null $track_stock;
    public ?string $user_note;
    public bool|int|null $archive;
    public ?int $project_price_qty;
    public float|string|null $project_price_discount;
    public bool|int|null $is_default;
    public array|string|null $options;
    public array|string|null $dimensions_image;


    public function __construct()
    {
        parent::__construct();
    }

    public function options(){
        return $this->hasMany(ItemOption::class, 'item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

}


class QuoteImageData
{
    public int $id;
    public string $image;
    public string $name;
    public string $description;
    public string $size;
    public string $type;
    public string $objectURL;
    public string $file;
    public array $status;

    public function __construct(array $data)
    {
        $this->id = isset($data['item_id']) ? intval($data['item_id']) : null;
        $this->image = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->name = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->description = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->size = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->type = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->objectURL = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->file = isset($data['quote_image']) ? $data['quote_image'] : '';
        $this->status = isset($data['quote_image'])
            ? (is_array($data['quote_image']) ? $data['quote_image'] : ['name' => 'Uploaded', 'severity' => 'success'])
            : ['name' => 'Uploaded', 'severity' => 'success'];
    }

    public function toArray(): array
    {
        $config = env('APP_URL');
        $array = [
            'id' => $this->id,
            'image' => $this->image,
            'name' => $this->name,
            'description' => $this->description,
            'size' => $this->size,
            'type' => $this->type,
            'objectURL' => $this->objectURL,
            'file' => $this->file,
            'status' => $this->status,
        ];
        return $array;
    }
}

class ItemResponse
{
    public ?int $item_id;
    public ?int $product_id;
    public ?int $product_variant_id;
    public ?int $km_item_id;
    public ?int $vendor_id;
    public ?int $import_vendor_id;
    public ?int $factory_vendor_id;
    public ?int $item_category_id;
    public ?int $item_type_id;
    public ?int $sort_order;
    public ?string $item_code;
    public ?string $web_sku;
    public ?string $class;
    public ?string $description;
    public ?string $specifications;
    public ?string $warranty_period;
    public bool|int|null $active;
    public float|string|null $width;
    public float|string|null $height;
    public float|string|null $depth;
    public ?string $display_width;
    public ?string $display_height;
    public ?string $display_depth;
    public float|string|null $carton_qm;
    public float|string|null $carton_width;
    public float|string|null $carton_depth;
    public float|string|null $carton_height;
    public float|string|null $gross_weight;
    public float|string|null $boardusages_sixteen;
    public float|string|null $boardusages_eighteen;
    public float|string|null $boardusages_twentyfive;
    public float|string|null $boardusages_thirtythree;
    public ?string $krost_zoho_id;
    public ?string $krost_qld_zoho_id;
    public ?string $meloz_zoho_id;
    public ?string $gregbar_zoho_id;
    public ?string $klein_zoho_id;
    public ?int $lead_days;
    public ?int $melbourne_lead_days;
    public ?int $brisbane_lead_days;
    public ?int $safety_stock;
    public string|array|null $quote_image;
    public ?string $delay_until;
    public ?string $delay_until_reason;
    public ?string $web_link;
    public ?int $products_per_cartoon;
    public bool|int|null $track_stock;
    public ?string $user_note;
    public bool|int $archive;
    public ?int $project_price_qty;
    public float|string|null $project_price_discount;
    public string|null $created_at;
    public string|null $updated_at;
    public ?string $product_code;
    public ?string $product_variant;
    public array $variantItems;
    public array $dimensions_image;
    public bool|int $is_default;

    public function __construct(stdClass $data)
    {
        $this->item_id = $data->item_id ?? null;
        $this->product_id = $data->product_id ?? null;
        $this->product_variant_id = $data->product_variant_id ?? null;
        $this->product_code = $data->product_code ?? null;
        $this->product_variant = $data->product_variant ?? null;
        $this->km_item_id = $data->km_item_id ?? null;
        $this->vendor_id = $data->vendor_id ?? null;
        $this->import_vendor_id = $data->import_vendor_id ?? null;
        $this->factory_vendor_id = $data->factory_vendor_id ?? null;
        $this->item_category_id = $data->item_category_id ?? null;
        $this->item_type_id = $data->item_type_id ?? null;
        $this->sort_order = $data->sort_order ?? null;
        $this->item_code = $data->item_code ?? null;
        $this->web_sku = $data->web_sku ?? null;
        $this->class = $data->class ?? null;
        $this->description = $data->description ?? null;
        $this->specifications = $data->specifications ?? null;
        $this->warranty_period = $data->warranty_period ?? null;
        $this->active = $data->active ?? null;
        $this->width = $data->width ?? null;
        $this->height = $data->height ?? null;
        $this->depth = $data->depth ?? null;
        $this->display_width = $data->display_width ?? null;
        $this->display_height = $data->display_height ?? null;
        $this->display_depth = $data->display_depth ?? null;
        $this->carton_qm = $data->carton_qm ?? null;
        $this->carton_width = $data->carton_width ?? null;
        $this->carton_depth = $data->carton_depth ?? null;
        $this->carton_height = $data->carton_height ?? null;
        $this->gross_weight = $data->gross_weight ?? null;
        $this->boardusages_sixteen = $data->boardusages_sixteen ?? null;
        $this->boardusages_eighteen = $data->boardusages_eighteen ?? null;
        $this->boardusages_twentyfive = $data->boardusages_twentyfive ?? null;
        $this->boardusages_thirtythree = $data->boardusages_thirtythree ?? null;
        $this->krost_zoho_id = $data->krost_zoho_id ?? null;
        $this->krost_qld_zoho_id = $data->krost_qld_zoho_id ?? null;
        $this->meloz_zoho_id = $data->meloz_zoho_id ?? null;
        $this->gregbar_zoho_id = $data->gregbar_zoho_id ?? null;
        $this->klein_zoho_id = $data->klein_zoho_id ?? null;
        $this->lead_days = $data->lead_days ?? null;
        $this->melbourne_lead_days = $data->melbourne_lead_days ?? null;
        $this->brisbane_lead_days = $data->brisbane_lead_days ?? null;
        $this->safety_stock = $data->safety_stock ?? null;
        $this->variantItems = $data->variantItems ?? [];
        if(isset($data->dimensions_image) && is_string($data->dimensions_image)) {
            $this->dimensions_image = json_decode($data->dimensions_image, true);
        }
        // normalize quote_image: if incoming value is a JSON string, decode it
        if (isset($data->quote_image) && is_string($data->quote_image)) {
            $decodedQuoteImage = json_decode($data->quote_image, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedQuoteImage)) {
                $this->quote_image = $decodedQuoteImage;
            } else {
                $quote_image = new QuoteImageData((array) $data);
                $this->quote_image = $quote_image->toArray();
            }
        } else {
            $quote_image = new QuoteImageData((array) $data);
            $this->quote_image = $quote_image->toArray();
        }

        $this->delay_until = $data->delay_until ?? null;
        $this->delay_until_reason = $data->delay_until_reason ?? null;
        $this->web_link = $data->web_link ?? null;
        $this->products_per_cartoon = $data->products_per_cartoon ?? null;
        $this->track_stock = $data->track_stock ?? null;
        $this->user_note = $data->user_note ?? null;
        $this->archive = $data->archive ?? null;
        $this->project_price_qty = $data->project_price_qty ?? null;
        $this->project_price_discount = $data->project_price_discount ?? null;
        $this->created_at = $data->created_at ?? null;
        $this->updated_at = $data->updated_at ?? null;
        $this->is_default = $data->is_default;
    }
}

class ItemData
{
    public int $item_id;
    public int $product_id;
    public string $product_code;
    public int $product_variant_id;
    public string $product_variant;
    public ?int $km_item_id;
    public ?int $vendor_id;
    public ?int $import_vendor_id;
    public ?int $factory_vendor_id;
    public ?int $item_category_id;
    public ?int $item_type_id;
    public ?int $sort_order;
    public string $item_code;
    public ?string $web_sku;
    public ?string $class;
    public ?string $description;
    public ?string $specifications;
    public ?string $warranty_period;
    public bool|int $active;
    public float|string|null $width;
    public float|string|null $height;
    public float|string|null $depth;
    public float|string|null $carton_qm;
    public float|string|null $carton_width;
    public float|string|null $carton_depth;
    public float|string|null $carton_height;
    public float|string|null $gross_weight;
    public float|string|null $boardusages_sixteen;
    public float|string|null $boardusages_eighteen;
    public float|string|null $boardusages_twentyfive;
    public float|string|null $boardusages_thirtythree;
    public ?string $krost_zoho_id;
    public ?string $krost_qld_zoho_id;
    public ?string $meloz_zoho_id;
    public ?string $gregbar_zoho_id;
    public ?string $klein_zoho_id;
    public ?int $lead_days;
    public ?int $melbourne_lead_days;
    public ?int $brisbane_lead_days;
    public ?int $safety_stock;
    public string|array|null $quote_image;
    public ?string $delay_until;
    public ?string $delay_until_reason;
    public ?string $web_link;
    public ?int $products_per_cartoon;
    public bool|int $track_stock;
    public ?string $user_note;
    public bool|int $archive;
    public ?int $project_price_qty;
    public float|string|null $project_price_discount;
    public string $created_at;
    public string $updated_at;
    public array $categories;
    public array $tags;
    public array $relatedProducts;
    public array $variantProducts;
    public array $digitalAssets;
    public array $attributes;
    public array $images;
    public array $promotions;
    public array $metadata;
    public array $options;
    public string|array|null $image;
    public string|array|null $banner_image;
    public string|array|null $main_image_one;
    public string|array|null $main_image_two;
    public string|array|null $feature_image_one;
    public string|array|null $feature_image_two;
    public string|array|null $feature_image_three;
    public ?string $display_width;
    public ?string $display_height;
    public ?string $display_depth;
    public bool|int $is_default;

    public function __construct($data)
    {
        if (isset($data['item_id']) && $data['item_id'] > 0) $this->item_id = $data['item_id'];
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['product_code'])) $this->product_code = $data['product_code'];
        if (isset($data['product_variant_id'])) $this->product_variant_id = $data['product_variant_id'];
        if (isset($data['product_variant'])) $this->product_variant = $data['product_variant'];
        if (isset($data['km_item_id'])) $this->km_item_id = intval($data['km_item_id']);
        if (isset($data['vendor_id'])) $this->vendor_id = intval($data['vendor_id']);
        if (isset($data['import_vendor_id'])) $this->import_vendor_id = intval($data['import_vendor_id']);
        if (isset($data['factory_vendor_id'])) $this->factory_vendor_id = intval($data['factory_vendor_id']);
        if (isset($data['item_category_id'])) $this->item_category_id = intval($data['item_category_id']);
        if (isset($data['item_type_id'])) $this->item_type_id = intval($data['item_type_id']);
        if (isset($data['sort_order'])) $this->sort_order = intval($data['sort_order']);
        if (isset($data['item_code'])) $this->item_code = $data['item_code'];
        if (isset($data['web_sku'])) $this->web_sku = $data['web_sku'];
        if (isset($data['class'])) $this->class = $data['class'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['specifications'])) $this->specifications = $data['specifications'];
        if (isset($data['warranty_period'])) $this->warranty_period = $data['warranty_period'];
        if (isset($data['active'])) $this->active = $data['active'] ? 1 : 0;
        if (isset($data['width'])) $this->width = $data['width'];
        if (isset($data['height'])) $this->height = $data['height'];
        if (isset($data['depth'])) $this->depth = $data['depth'];
        if (isset($data['display_width'])) $this->display_width = $data['display_width'];
        if (isset($data['display_height'])) $this->display_height = $data['display_height'];
        if (isset($data['display_depth'])) $this->display_depth = $data['display_depth'];
        if (isset($data['carton_qm'])) $this->carton_qm = $data['carton_qm'];
        if (isset($data['carton_width'])) $this->carton_width = $data['carton_width'];
        if (isset($data['carton_depth'])) $this->carton_depth = $data['carton_depth'];
        if (isset($data['carton_height'])) $this->carton_height = $data['carton_height'];
        if (isset($data['gross_weight'])) $this->gross_weight = $data['gross_weight'];
        if (isset($data['boardusages_sixteen'])) $this->boardusages_sixteen = $data['boardusages_sixteen'];
        if (isset($data['boardusages_eighteen'])) $this->boardusages_eighteen = $data['boardusages_eighteen'];
        if (isset($data['boardusages_twentyfive'])) $this->boardusages_twentyfive = $data['boardusages_twentyfive'];
        if (isset($data['boardusages_thirtythree'])) $this->boardusages_thirtythree = $data['boardusages_thirtythree'];
        if (isset($data['krost_zoho_id'])) $this->krost_zoho_id = $data['krost_zoho_id'];
        if (isset($data['krost_qld_zoho_id'])) $this->krost_qld_zoho_id = $data['krost_qld_zoho_id'];
        if (isset($data['meloz_zoho_id'])) $this->meloz_zoho_id = $data['meloz_zoho_id'];
        if (isset($data['gregbar_zoho_id'])) $this->gregbar_zoho_id = $data['gregbar_zoho_id'];
        if (isset($data['klein_zoho_id'])) $this->klein_zoho_id = $data['klein_zoho_id'];
        if (isset($data['lead_days'])) $this->lead_days = intval($data['lead_days']);
        if (isset($data['melbourne_lead_days'])) $this->melbourne_lead_days = intval($data['melbourne_lead_days']);
        if (isset($data['brisbane_lead_days'])) $this->brisbane_lead_days = intval($data['brisbane_lead_days']);
        if (isset($data['safety_stock'])) $this->safety_stock = intval($data['safety_stock']);
        if (isset($data['quote_image'])) $this->quote_image = isset($data['quote_image']['objectURL']) ? $data['quote_image']['objectURL'] : null;
        if (isset($data['delay_until'])) $this->delay_until = $data['delay_until'];
        if (isset($data['delay_until_reason'])) $this->delay_until_reason = $data['delay_until_reason'];
        if (isset($data['web_link'])) $this->web_link = $data['web_link'];
        if (isset($data['products_per_cartoon'])) $this->products_per_cartoon = (int) $data['products_per_cartoon'];
        if (isset($data['track_stock'])) $this->track_stock = $data['track_stock'] ? 1 : 0;
        if (isset($data['user_note'])) $this->user_note = $data['user_note'];
        if (isset($data['archive'])) $this->archive = $data['archive'] ? 1 : 0;
        if (isset($data['project_price_qty'])) $this->project_price_qty = (int) $data['project_price_qty'];
        if (isset($data['project_price_discount'])) $this->project_price_discount = $data['project_price_discount'];
        if (isset($data['created_at'])) $this->created_at = $data['created_at'];
        if (isset($data['updated_at'])) $this->updated_at = $data['updated_at'];
        if (isset($data['categories'])) $this->categories = $data['categories'] ?? [];
        if (isset($data['tags'])) $this->tags = $data['tags'] ?? [];
        if (isset($data['relatedProducts'])) $this->relatedProducts = $data['relatedProducts'] ?? [];
        if (isset($data['variantProducts'])) $this->variantProducts = $data['variantProducts'] ?? [];
        if (isset($data['digitalAssets'])) $this->digitalAssets = $data['digitalAssets'] ?? [];
        if (isset($data['attributes'])) $this->attributes = $data['attributes'] ?? [];
        if (isset($data['images'])) $this->images = $data['images'] ?? [];
        if (isset($data['promotions'])) $this->promotions = $data['promotions'] ?? [];
        if (isset($data['metadata'])) $this->metadata = $data['metadata'] ?? [];
        if (isset($data['options'])) $this->options = $data['options'] ?? [];
        if (isset($data['content'])) $this->content = new ProductContentData($data['content']);
        if (isset($data['manufacturer'])) $this->manufacturer = new ManufacturerData($data['manufacturer']);
        if (isset($data['vendor'])) $this->vendor = new VendorData($data['vendor']);
        if (isset($data['image'])) $this->image = is_array($data['image']) ? json_encode($data['image']) : $data['image'];
        if (isset($data['banner_image'])) $this->banner_image = is_array($data['banner_image']) ? json_encode($data['banner_image']) : $data['banner_image'];
        if (isset($data['main_image_one'])) $this->main_image_one = is_array($data['main_image_one']) ? json_encode($data['main_image_one']) : $data['main_image_one'];
        if (isset($data['main_image_two'])) $this->main_image_two = is_array($data['main_image_two']) ? json_encode($data['main_image_two']) : $data['main_image_two'];
        if (isset($data['feature_image_one'])) $this->feature_image_one = is_array($data['feature_image_one']) ? json_encode($data['feature_image_one']) : $data['feature_image_one'];
        if (isset($data['feature_image_two'])) $this->feature_image_two = is_array($data['feature_image_two']) ? json_encode($data['feature_image_two']) : $data['feature_image_two'];
        if (isset($data['feature_image_three'])) $this->feature_image_three = is_array($data['feature_image_three']) ? json_encode($data['feature_image_three']) : $data['feature_image_three'];
        if (isset($data['is_default'])) $this->is_default = $data['is_default'] ? 1 : 0;
    }

    public function toArray(): array
    {
        $data = [];
        if (isset($this->item_id)) $data['item_id'] = $this->item_id;
        if (isset($this->product_id)) $data['product_id'] = $this->product_id;
        if (isset($this->product_code)) $data['product_code'] = $this->product_code;
        if (isset($this->product_variant_id)) $data['product_variant_id'] = $this->product_variant_id;
        if (isset($this->product_variant)) $data['product_variant'] = $this->product_variant;
        if (isset($this->km_item_id)) $data['km_item_id'] = $this->km_item_id;
        if (isset($this->vendor_id)) $data['vendor_id'] = $this->vendor_id;
        if (isset($this->import_vendor_id)) $data['import_vendor_id'] = $this->import_vendor_id;
        if (isset($this->factory_vendor_id)) $data['factory_vendor_id'] = $this->factory_vendor_id;
        if (isset($this->item_category_id)) $data['item_category_id'] = $this->item_category_id;
        if (isset($this->item_type_id)) $data['item_type_id'] = $this->item_type_id;
        if (isset($this->sort_order)) $data['sort_order'] = $this->sort_order;
        if (isset($this->item_code)) $data['item_code'] = $this->item_code;
        if (isset($this->web_sku)) $data['web_sku'] = $this->web_sku;
        if (isset($this->class)) $data['class'] = $this->class;
        if (isset($this->description)) $data['description'] = $this->description;
        if (isset($this->specifications)) $data['specifications'] = $this->specifications;
        if (isset($this->warranty_period)) $data['warranty_period'] = $this->warranty_period;
        if (isset($this->active)) $data['active'] = $this->active ? 1 : 0;
        if (isset($this->width)) $data['width'] = $this->width;
        if (isset($this->height)) $data['height'] = $this->height;
        if (isset($this->depth)) $data['depth'] = $this->depth;
        if (isset($this->display_width)) $data['display_width'] = $this->display_width;
        if (isset($this->display_height)) $data['display_height'] = $this->display_height;
        if (isset($this->display_depth)) $data['display_depth'] = $this->display_depth;
        if (isset($this->carton_qm)) $data['carton_qm'] = $this->carton_qm;
        if (isset($this->carton_width)) $data['carton_width'] = $this->carton_width;
        if (isset($this->carton_depth)) $data['carton_depth'] = $this->carton_depth;
        if (isset($this->carton_height)) $data['carton_height'] = $this->carton_height;
        if (isset($this->gross_weight)) $data['gross_weight'] = $this->gross_weight;
        if (isset($this->boardusages_sixteen)) $data['boardusages_sixteen'] = $this->boardusages_sixteen;
        if (isset($this->boardusages_eighteen)) $data['boardusages_eighteen'] = $this->boardusages_eighteen;
        if (isset($this->boardusages_twentyfive)) $data['boardusages_twentyfive'] = $this->boardusages_twentyfive;
        if (isset($this->boardusages_thirtythree)) $data['boardusages_thirtythree'] = $this->boardusages_thirtythree;
        if (isset($this->krost_zoho_id)) $data['krost_zoho_id'] = $this->krost_zoho_id;
        if (isset($this->krost_qld_zoho_id)) $data['krost_qld_zoho_id'] = $this->krost_qld_zoho_id;
        if (isset($this->meloz_zoho_id)) $data['meloz_zoho_id'] = $this->meloz_zoho_id;
        if (isset($this->gregbar_zoho_id)) $data['gregbar_zoho_id'] = $this->gregbar_zoho_id;
        if (isset($this->klein_zoho_id)) $data['klein_zoho_id'] = $this->klein_zoho_id;
        if (isset($this->lead_days)) $data['lead_days'] = $this->lead_days;
        if (isset($this->melbourne_lead_days)) $data['melbourne_lead_days'] = $this->melbourne_lead_days;
        if (isset($this->brisbane_lead_days)) $data['brisbane_lead_days'] = $this->brisbane_lead_days;
        if (isset($this->safety_stock)) $data['safety_stock'] = $this->safety_stock;
        if (isset($this->quote_image)) {
            if (is_array($this->quote_image) || is_object($this->quote_image)) {
                $data['quote_image'] = $this->quote_image;
            } else {
                $data['quote_image'] = 'local/'.$this->quote_image;
            }
        }
        // convert delay_until to date
        if (isset($this->delay_until)) $data['delay_until'] = date('Y-m-d', strtotime($this->delay_until));
        if (isset($this->delay_until_reason)) $data['delay_until_reason'] = $this->delay_until_reason;
        if (isset($this->web_link)) $data['web_link'] = $this->web_link;
        if (isset($this->products_per_cartoon)) $data['products_per_cartoon'] = $this->products_per_cartoon;
        if (isset($this->track_stock)) $data['track_stock'] = $this->track_stock ? 1 : 0;
        if (isset($this->user_note)) $data['user_note'] = $this->user_note;
        if (isset($this->archive)) $data['archive'] = $this->archive ? 1 : 0;
        if (isset($this->project_price_qty)) $data['project_price_qty'] = $this->project_price_qty;
        if (isset($this->project_price_discount)) $data['project_price_discount'] = $this->project_price_discount;
        if (isset($this->is_default)) $data['is_default'] = $this->is_default;

        return $data;
    }
}
