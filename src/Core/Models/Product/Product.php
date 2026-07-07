<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use \stdClass;
use App\Core\Models\Site\Site;
use App\Core\Models\TaxonomyItem;
use App\Core\Models\User\DigitalAsset;
use App\Core\Models\Media\Image;

use function App\Core\System\utils\makeSlug;

class Product extends Model
{
    public ?int $product_id;
    public ?int $km_item_id;
    public ?int $product_type_id;
    public ?int $class_id;
    public ?int $company_id;
    public ?int $admin_id;
    public ?int $parent_id;
    public ?int $media_id;
    public ?string $description;
    public ?string $specifications;
    public ?string $warranty_period;
    public ?string $product_code;
    public ?string $product_family_code;

    // public function __set(string $name, $value): void
    // {
    //     if ($name === 'product_code') {
    //         $this->product_code = ucfirst(strtolower(str_replace(' ', '-', $value)));
    //         return;
    //     }
    //     parent::__set($name, $value);
    // }
    public ?string $factory_code;
    public ?string $sku;
    public ?string $isbn;
    public ?string $barcode;
    public bool|int|null $track_stock;
    public ?int $stock_quantity;
    public ?int $stock_status_id;
    public ?int $lead_days;
    public ?int $melbourne_lead_days;
    public ?int $safety_stock;
    public ?int $qty_alert;
    public ?string $image;
    public ?string $banner_image;
    public ?string $image_thumb;
    public ?string $main_image_one;
    public ?string $main_image_one_description;
    public ?string $main_image_two;
    public ?string $main_image_two_description;
    public ?string $feature_image_one;
    public ?string $feature_image_one_description;
    public ?string $feature_image_two;
    public ?string $feature_image_two_description;
    public ?string $feature_image_three;
    public string|array|null $specifications_image;
    public string|array|null $dimension_image;
    public ?string $feature_image_three_description;
    public ?int $manufacturer_id;
    public ?int $vendor_id;
    public ?int $import_vendor_id;
    public ?int $factory_vendor_id;
    public ?int $product_range_id;
    public ?int $product_category_id;
    public ?int $edgetape_colour_id;
    public bool|int|null $requires_shipping;
    public ?int $tax_type_id;
    public ?string $material;
    public float|string|null $weight;
    public ?int $weight_type_id;
    public float|string|null $length;
    public ?int $length_type_id;
    public float|string|null $width;
    public float|string|null $height;
    public float|string|null $depth;
    public float|string|null $carton_qm;
    public ?int $size;
    public float|string|null $carton_width;
    public float|string|null $carton_depth;
    public float|string|null $carton_height;
    public float|string|null $gross_weight;
    public ?string $date_available;
    public ?string $template;
    public ?int $views;
    public bool|int|null $subtract_stock;
    public bool|int|null $status;
    public bool|int|null $is_featured;
    public ?int $sort_order;
    public ?int $project_price_qty;
    public float|string|null $project_price_discount;
    public bool|int|null $active;
    public bool|int|null $archive;
    public ?string $created_at;
    public ?string $updated_at;

    public ?int $min_order_quantity;
    public ?string $out_of_stock_status;
    public ?string $length_class;
    public ?string $weight_class;
    public mixed $product_content;
    public ?string $feature_description;

    // Product Resource
    public ?int $product_resource_id;
    public ?int $design_resource_id;

    // Design Resource
    public ?string $img;
    public ?string $img2;
    public ?string $title;
    public ?string $product_title;
    public ?string $resource_type;
    public ?string $link_text;
    public ?string $grade;
    public ?string $slug;
    public ?string $type;
    public ?string $brand;
    public array|string|null $banner_way_points;
    public bool|int $ocean_plastic_used;
    public bool|int $show_configurator;
    public ?string $store_link;
    public ?string $catalogue_link;
    public array|string|null $video_url;
    public ?string $main_image_one_title;
    public ?string $main_image_two_title;
    public ?string $feature_image_one_title;
    public ?string $feature_image_two_title;
    public ?string $feature_image_three_title;

    // custom fields
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;
    // public ?string $name;
    // public ?string $tag_line;

    //Design Resource Document

    public function __construct()
    {
        parent::__construct();
    }

    public function content()
    {
        return $this->hasOne(ProductContent::class, 'product_id', 'product_id');
    }
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }
    public function options()
    {
        return $this->hasMany(ProductOption::class, 'product_id');
    }
    public function variantProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_variant',
            'product_variant',
            'product_id',
            'product_variant_id',
            'product_id',
            'product_id'
        );
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_related',
            'product_related',
            'product_id',
            'product_related_id',
            'product_id',
            'product_id'
        );
        // $relatedModel, 
        // $pivotTable = null, 
        // $pivotTableAlias = null,
        // $foreignPivotKey = null, 
        // $ownerPivotKey = null, 
        // $localKey = null, 
        // $ownerKey = null
    }

    public function digitalAssets()
    {
        return $this->belongsToMany(
            DigitalAsset::class,        // related model
            'product_to_digital_asset', // pivot table
            null,                       // extra add -- pivot table alias (optional, use null)
            'product_id',               // foreign key on pivot table pointing to this model (Product)
            'digital_asset_id',         // foreign key on pivot table pointing to related model (DigitalAsset)
            'product_id',               // local key on Product
            'digital_asset_id'          // local key on DigitalAsset
        );
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function stockStatus()
    {
        return $this->belongsTo(StockStatus::class, 'stock_status_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(ProductSubscription::class, 'product_id');
    }

    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class, 'product_id');
    }

    public function promotions()
    {
        return $this->hasMany(ProductPromotion::class, 'product_id');
    }

    public function points()
    {
        return $this->hasMany(ProductPoints::class, 'product_id');
    }

    public function sites()
    {
        return $this->belongsToMany(
            Site::class,
            'product_to_site',
            'product_id',
            'site_id',
            'product_id',
            'site_id'
        );
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    // public function categories(){
    //     return $this->belongsToMany(TaxonomyItem::class, 'product_to_taxonomy_item', 'product_id', 'taxonomy_item_id');
    // }
}

class ProductResponse
{
    public ?int $product_id;
    public ?int $km_item_id;
    public ?int $product_type_id;
    public ?int $class_id;
    public ?int $company_id;
    public ?int $admin_id;
    public ?int $parent_id;
    public ?string $description;
    public ?string $specifications;
    public ?string $warranty_period;
    public ?string $product_code;
    public ?string $product_family_code;
    public ?string $factory_code;
    public ?string $sku;
    public ?string $isbn;
    public ?string $barcode;
    public bool|int|null $track_stock;
    public ?int $stock_quantity;
    public ?int $stock_status_id;
    public ?int $lead_days;
    public ?int $melbourne_lead_days;
    public ?int $safety_stock;
    public ?int $qty_alert;
    public string|array|null $image;
    public string|array|null $banner_image;
    public string|array|null $image_thumb;
    public string|array|null $main_image_one;
    public ?string $main_image_one_description;
    public string|array|null $main_image_two;
    public ?string $main_image_two_description;
    public string|array|null $feature_image_one;
    public ?string $feature_image_one_description;
    public string|array|null $feature_image_two;
    public ?string $feature_image_two_description;
    public string|array|null $feature_image_three;
    public string|array|null $specifications_image;
    public string|array|null $dimension_image;
    public ?string $feature_image_three_description;
    public ?string $feature_description;
    /** @var Image[] */
    public ?array $images;
    public ?int $manufacturer_id;
    public ?int $vendor_id;
    public ?int $import_vendor_id;
    public ?int $factory_vendor_id;
    public ?int $product_range_id;
    public ?int $product_category_id;
    public ?int $edgetape_colour_id;
    public bool|int|null $requires_shipping;
    public ?int $tax_type_id;
    public ?string $material;
    public float|string|null $weight;
    public ?int $weight_type_id;
    public float|string|null $length;
    public ?int $length_type_id;
    public float|string|null $width;
    public float|string|null $height;
    public float|string|null $depth;
    public float|string|null $carton_qm;
    public ?int $size;
    public float|string|null $carton_width;
    public float|string|null $carton_depth;
    public float|string|null $carton_height;
    public float|string|null $gross_weight;
    public ?string $date_available;
    public ?string $template;
    public ?int $views;
    public bool|int|null $subtract_stock;
    public bool|int|null $status;
    public bool|int|null $is_featured;
    public ?int $sort_order;
    public ?int $project_price_qty;
    public float|string|null $project_price_discount;
    public bool|int|null $active;
    public bool|int $archive;
    public string|null $created_at;
    public string|null $updated_at;

    public ?int $min_order_quantity;
    public ?string $out_of_stock_status;
    public ?string $length_class;
    public ?string $weight_class;
    public float|string|null $price;
    public float|string|null $old_price;
    public array|string|null $banner_way_points;
    public bool|int $ocean_plastic_used;
    public bool|int $show_configurator;
    public ?string $store_link;
    public ?string $catalogue_link;

    public ?string $main_image_one_title;
    public ?string $main_image_two_title;
    public ?string $feature_image_one_title;
    public ?string $feature_image_two_title;
    public ?string $feature_image_three_title;


    public array $categories;
    public array $tags;
    public array $certificates;
    public array $resources;
    public array $relatedProducts;
    public array $relatedProjects;
    public array $relatedResources;
    public array $variantProducts;
    public array $digitalAssets;
    public ProductContentData $content;
    public ManufacturerData $manufacturer;
    public VendorData $vendor;
    public array $attributes;
    public array $options;
    public array $promotions;
    public array $productVariants;
    public array $familyProducts;
    public array|string|null $video_url;
    public array $metadata;


    public function __construct(stdClass $data)
    {
        $this->product_id = $data->product_id ?? null;
        $this->km_item_id = $data->km_item_id;
        $this->product_type_id = $data->product_type_id;
        $this->class_id = $data->class_id;
        $this->company_id = $data->company_id;
        $this->admin_id = $data->admin_id;
        $this->parent_id = $data->parent_id ?? null;
        $this->description = $data->description ?? null;
        $this->feature_description = $data->feature_description ?? null;
        $this->specifications = $data->specifications ?? null;
        $this->warranty_period = $data->warranty_period ?? null;
        $this->product_code = $data->product_code ?? null;
        $this->product_family_code = $data->product_family_code ?? null;
        $this->factory_code = $data->factory_code ?? null;
        $this->sku = $data->sku ?? null;
        $this->isbn = $data->isbn ?? null;
        $this->barcode = $data->barcode ?? null;
        $this->track_stock = $data->track_stock ?? null;
        $this->stock_quantity = $data->stock_quantity ?? null;
        $this->stock_status_id = $data->stock_status_id ?? null;
        $this->lead_days = $data->lead_days ?? null;
        $this->melbourne_lead_days = $data->melbourne_lead_days ?? null;
        $this->safety_stock = $data->safety_stock ?? null;
        $this->qty_alert = $data->qty_alert ?? null;
        $this->banner_way_points = isset($data->banner_way_points) ? json_decode($data->banner_way_points, true) : null;
        $this->video_url = isset($data->video_url) ? json_decode($data->video_url, true) : [];
        // $this->image = is_array($data->image)
        //     ? $data->image
        //     : json_decode($data->image, true);
        // if (is_array($this->image) && count($this->image) > 0 && isset($this->image[0]['objectURL'])) {
        //     $this->image[0]['status'] = ['severity' => 'success', 'name' => 'Uploaded'];
        // }
        // response image formatting 
        $imageFields = ['image','banner_image','image_thumb','main_image_one','main_image_two','feature_image_one','feature_image_two','feature_image_three', 'specifications_image', 'dimension_image'];
                
        foreach ($imageFields as $field) {
            $value = $data->$field ?? null;
        
            if (is_array($value)) {
                $this->$field = $value;
            } elseif (is_string($value) && !empty($value)) {
                $this->$field = json_decode($value, true);
            } else {
                $this->$field = [];
            }
        
            if (
                is_array($this->$field) &&
                count($this->$field) > 0 &&
                isset($this->$field[0]['objectURL'])
            ) {
                // $config = app('config');
                $imageServer = ''; // $config['APP_URL'];
                $this->$field[0]['objectURL'] = $imageServer . $this->$field[0]['objectURL'];
                $this->$field[0]['status'] = ['severity' => 'success', 'name' => 'Uploaded'];
            }
        }

        if (isset($data->images) && !empty($data->images) && $data->images !== null) {
            $this->images = array_filter(array_map(function ($image) {
                if (isset($image['image']) && !empty($image['image']) && $image['image'] !== null) {
                    $img = new stdClass();
                    $image['image'] = json_decode($image['image'], true);
                    $image['status'] = json_decode((string) $image['status'], true);
                    if (isset($image['image'][0]['objectURL'])) {
                        $img = new Image($image['image'][0]);
                        $img->status = ['name' => 'Uploaded', 'severity' => 'success'];
                        $img->product_image_id = $image['product_image_id'];
                        $img->product_id = $image['product_id'];
                        $img->sort_order = $image['sort_order'];
                        return $img;
                    } else if (isset($image['image']['objectURL'])) {
                        $img = new Image($image['image']);
                        $img->status = ['name' => 'Uploaded', 'severity' => 'success'];
                        $img->product_id = $image['product_id'];
                        $img->product_image_id = $image['product_image_id'] ?? null;
                        $img->sort_order = $image['sort_order'];
                        return $img;
                    }
                }
                return null;
            }, $data->images), function ($item) {
                return $item !== null;
            });
        }
        
        if (isset($data->main_image_one_description)) $this->main_image_one_description = $data->main_image_one_description;
        if (isset($data->main_image_two_description)) $this->main_image_two_description = $data->main_image_two_description;
        if (isset($data->feature_image_one_description)) $this->feature_image_one_description = $data->feature_image_one_description;
        if (isset($data->feature_image_two_description)) $this->feature_image_two_description = $data->feature_image_two_description;
        if (isset($data->feature_image_three_description)) $this->feature_image_three_description = $data->feature_image_three_description;
        $this->manufacturer_id = $data->manufacturer_id ?? null;
        $this->vendor_id = $data->vendor_id ?? null;
        $this->import_vendor_id = $data->import_vendor_id ?? null;
        $this->factory_vendor_id = $data->factory_vendor_id ?? null;
        $this->product_range_id = $data->product_range_id ?? null;
        $this->product_category_id = $data->product_category_id ?? null;
        $this->edgetape_colour_id = $data->edgetape_colour_id ?? null;
        $this->requires_shipping = $data->requires_shipping ?? null;
        $this->tax_type_id = $data->tax_type_id ?? null;
        $this->material = $data->material ?? null;
        $this->weight = $data->weight ?? null;
        $this->weight_type_id = $data->weight_type_id ?? null;
        $this->length = $data->length ?? null;
        $this->length_type_id = $data->length_type_id ?? null;
        $this->width = $data->width ?? null;
        $this->height = $data->height ?? null;
        $this->depth = $data->depth ?? null;
        $this->carton_qm = $data->carton_qm ?? null;
        $this->size = $data->size ?? null;
        $this->carton_width = $data->carton_width ?? null;
        $this->carton_depth = $data->carton_depth ?? null;
        $this->carton_height = $data->carton_height ?? null;
        $this->gross_weight = $data->gross_weight ?? null;
        $this->date_available = $data->date_available ?? null;
        $this->template = $data->template ?? null;
        $this->views = $data->views ?? null;
        $this->subtract_stock = $data->subtract_stock ? 1 : 0;
        $this->status = $data->status ? 1 : 0;
        $this->is_featured = $data->is_featured ? 1 : 0;
        $this->sort_order = $data->sort_order ?? null;
        $this->project_price_qty = $data->project_price_qty ?? null;
        $this->project_price_discount = $data->project_price_discount ?? null;
        $this->active = $data->active ? 1 : 0;
        $this->archive = $data->archive ? 1 : 0;
        $this->created_at = $data->created_at;
        $this->updated_at = $data->updated_at;

        $this->min_order_quantity = $data->min_order_quantity ?? null;
        $this->out_of_stock_status = $data->out_of_stock_status ?? null;
        $this->length_class = $data->length_class ?? null;
        $this->weight_class = $data->weight_class ?? null;
        $this->price = $data->price ?? null;
        $this->old_price = $data->old_price ?? null;

        if (isset($data->content)) $this->content = is_array($data->content)
            ? new ProductContentData($data->content)
            : new ProductContentData(json_decode($data->content, true));
        if (isset($data->manufacturer)) $this->manufacturer = (is_array($data->manufacturer)) ? new ManufacturerData($data->manufacturer) : new ManufacturerData(json_decode($data->manufacturer, true));
        if (isset($data->vendor)) $this->vendor = (is_array($data->vendor)) ? new VendorData($data->vendor) : new VendorData(json_decode($data->vendor, true));
        if (isset($data->categories)) $this->categories = $data->categories;
        if (isset($data->tags)) $this->tags = $data->tags;
        if (isset($data->certificates)) $this->certificates = $data->certificates;
        if (isset($data->resources)) $this->resources = $data->resources;
        if (isset($data->relatedProducts)) $this->relatedProducts = is_array($data->relatedProducts)
            ? $data->relatedProducts
            : json_decode($data->relatedProducts, true);
        if (isset($this->relatedProducts) && is_array($this->relatedProducts)) $this->relatedProducts = array_filter($this->relatedProducts, function ($relatedProduct) {
            return isset($relatedProduct['product_code']);
        });
        if (isset($data->familyProducts)) $this->familyProducts = is_array($data->familyProducts)
            ? $data->familyProducts
            : json_decode($data->familyProducts, true);

        if (isset($data->relatedProjects)) $this->relatedProjects = is_array($data->relatedProjects)
            ? $data->relatedProjects
            : json_decode($data->relatedProjects, true);

        if (isset($data->relatedResources)) $this->relatedResources = is_array($data->relatedResources)
            ? $data->relatedResources
            : json_decode($data->relatedResources, true);
    
        if (isset($data->variantProducts)) $this->variantProducts = is_array($data->variantProducts)
            ? $data->variantProducts
            : json_decode($data->variantProducts, true);
        if (isset($this->variantProducts) && is_array($this->variantProducts)) $this->variantProducts = array_filter($this->variantProducts, function ($variantProduct) {
            return isset($variantProduct['product_code']);
        });
        if (isset($data->digitalAssets)) $this->digitalAssets = $data->digitalAssets;
        // if(isset($data->attributes)) $this->attributes = json_decode($data->attributes, true);
        if (isset($data->attributes)) $this->attributes = is_array($data->attributes)
            ? $data->attributes
            : json_decode($data->attributes, true);
        if (isset($data->options)) $this->options = is_array($data->options)
            ? $data->options
            : json_decode($data->options, true);
        if (isset($this->options) && is_array($this->options)) $this->options = array_filter($this->options, function ($option) {
            return isset($option['product_option_id']);
        });
        if (isset($data->promotions)) $this->promotions = $data->promotions;
        if (isset($data->productVariants)) $this->productVariants = $data->productVariants;
        if (isset($data->banner_way_points)) $this->banner_way_points = json_decode($data->banner_way_points, true);
        if (isset($data->ocean_plastic_used)) $this->ocean_plastic_used = $data->ocean_plastic_used ? true : false;
        if (isset($data->show_configurator)) $this->show_configurator = $data->show_configurator ? true : false;
        if (isset($data->store_link)) $this->store_link = $data->store_link;
        if (isset($data->catalogue_link)) $this->catalogue_link = $data->catalogue_link;

        if (isset($data->main_image_one_title)) $this->main_image_one_title = $data->main_image_one_title;
        if (isset($data->main_image_two_title)) $this->main_image_two_title = $data->main_image_two_title;
        if (isset($data->feature_image_one_title)) $this->feature_image_one_title = $data->feature_image_one_title;
        if (isset($data->feature_image_two_title)) $this->feature_image_two_title = $data->feature_image_two_title;
        if (isset($data->feature_image_three_title)) $this->feature_image_three_title = $data->feature_image_three_title;
        if (isset($data->metadata)) $this->metadata = $data->metadata;
    }
}

class ProductData
{
    public int $product_id;
    public int $km_item_id;
    public int $product_type_id;
    public ?int $class_id;
    public ?int $company_id;
    public int $admin_id;
    public ?int $parent_id;
    public string $model;
    public ?string $description;
    public ?string $specifications;
    public ?string $warranty_period;
    public string $product_code;
    public ?string $product_family_code;
    public ?string $factory_code;
    public string $sku;
    public string $isbn;
    public string $barcode;
    public bool|int $track_stock;
    public int $stock_quantity;
    public int $stock_status_id;
    public int $lead_days;
    public int $melbourne_lead_days;
    public int $safety_stock;
    public int $qty_alert;
    public string|array|null $image;
    public string|array|null $banner_image;
    public string|array|null $image_thumb;
    public string|array|null $main_image_one;
    public ?string $main_image_one_description;
    public string|array|null $main_image_two;
    public ?string $main_image_two_description;
    public string|array|null $feature_image_one;
    public ?string $feature_image_one_description;
    public string|array|null $feature_image_two;
    public ?string $feature_image_two_description;
    public string|array|null $feature_image_three;
    public string|array|null $specifications_image;
    public string|array|null $dimension_image;
    public ?string $feature_image_three_description;
    public ?string $feature_description;
    public ?int $manufacturer_id;
    public ?int $vendor_id;
    public ?int $import_vendor_id;
    public ?int $factory_vendor_id;
    public ?int $product_range_id;
    public int $product_category_id;
    public ?int $edgetape_colour_id;
    public bool|int $requires_shipping;
    public ?int $tax_type_id;
    public string $material;
    public float|string|null $weight;
    public ?int $weight_type_id;
    public float|string|null $length;
    public ?int $length_type_id;
    public float|string|null $width;
    public float|string|null $height;
    public float|string|null $depth;
    public float|string|null $carton_qm;
    public ?int $size;
    public float|string|null $carton_width;
    public float|string|null $carton_depth;
    public float|string|null $carton_height;
    public float|string|null $gross_weight;
    public ?string $date_available;
    public string $template;
    public int $views;
    public bool|int $subtract_stock;
    public bool|int $status;
    public bool|int $is_featured;
    public int $sort_order;
    public ?int $project_price_qty;
    public float|string|null $project_price_discount;
    public bool|int $active;
    public bool|int $archive;
    public string $created_at;
    public string $updated_at;

    public ?int $min_order_quantity;
    public ?string $out_of_stock_status;
    public ?string $length_class;
    public ?string $weight_class;
    public float|string|null $price;
    public float|string|null $old_price;

    public ProductContentData $content;
    public ManufacturerData $manufacturer;
    public VendorData $vendor;
    public array $tags;
    public array $certificates;
    public array $resources;
    public array $categories;
    public array $relatedProducts;
    public array $relatedProjects;
    public array $relatedResources;
    public array $familyProducts;
    public array $variantProducts;
    public array $digitalAssets;
    public array $attributes;
    public array $images;
    // public InventoryData $inventory;
    public array $promotions;
    public array $metadata;
    public array $productVariants;  
    public array $options;
    public array|string|null $banner_way_points;
    public bool|int $ocean_plastic_used;
    public bool|int $show_configurator;
    public ?string $store_link;
    public ?string $catalogue_link;
    public string|array|null $video_url;
    public ?string $main_image_one_title;
    public ?string $main_image_two_title;
    public ?string $feature_image_one_title;
    public ?string $feature_image_two_title;
    public ?string $feature_image_three_title;

    public function __construct($data)
    {
        $this->options = [];
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['km_item_id'])) $this->km_item_id = $data['km_item_id'];
        if (isset($data['product_type_id'])) $this->product_type_id = $data['product_type_id'];
        if (isset($data['class_id'])) $this->class_id = $data['class_id'];
        if (isset($data['company_id'])) $this->company_id = $data['company_id'];
        if (isset($data['admin_id'])) $this->admin_id = $data['admin_id'];
        if (isset($data['parent_id'])) $this->parent_id = $data['parent_id'];
        if (isset($data['model'])) $this->model = $data['model'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['specifications'])) $this->specifications = $data['specifications'];
        if (isset($data['warranty_period'])) $this->warranty_period = $data['warranty_period'];
        if (isset($data['product_code'])) $this->product_code = $data['product_code'];
        // if (isset($data['product_family_code'])) $this->product_family_code = $data['product_family_code'];
        if (isset($data['product_family_code'])) {
            $this->product_family_code = $data['product_family_code'];
        } elseif (isset($data['product_code'])) {
            $this->product_family_code = $data['product_code'];
        }
        if (isset($data['factory_code'])) $this->factory_code = $data['factory_code'];
        if (isset($data['sku'])) $this->sku = $data['sku'];
        if (isset($data['isbn'])) $this->isbn = $data['isbn'];
        if (isset($data['barcode'])) $this->barcode = $data['barcode'];
        if (isset($data['track_stock'])) $this->track_stock = $data['track_stock'] ? 1 : 0;
        if (isset($data['stock_quantity'])) $this->stock_quantity = (int)$data['stock_quantity'];
        if (isset($data['stock_status_id'])) $this->stock_status_id = $data['stock_status_id'];
        if (isset($data['lead_days'])) $this->lead_days = $data['lead_days'];
        if (isset($data['melbourne_lead_days'])) $this->melbourne_lead_days = $data['melbourne_lead_days'];
        if (isset($data['safety_stock'])) $this->safety_stock = $data['safety_stock'];
        if (isset($data['qty_alert'])) $this->qty_alert = $data['qty_alert'];

        if (isset($data['image'])) $this->image = is_array($data['image']) ? json_encode($data['image']) : $data['image'];
        if (isset($data['banner_image'])) $this->banner_image = is_array($data['banner_image']) ? json_encode($data['banner_image']) : $data['banner_image'];
        if (isset($data['image_thumb'])) $this->image_thumb = is_array($data['image_thumb']) ? json_encode($data['image_thumb']) : $data['image_thumb'];
        if (isset($data['main_image_one'])) $this->main_image_one = is_array($data['main_image_one']) ? json_encode($data['main_image_one']) : $data['main_image_one'];
        if (isset($data['main_image_two'])) $this->main_image_two = is_array($data['main_image_two']) ? json_encode($data['main_image_two']) : $data['main_image_two'];
        if (isset($data['feature_image_one'])) $this->feature_image_one = is_array($data['feature_image_one']) ? json_encode($data['feature_image_one']) : $data['feature_image_one'];
        if (isset($data['feature_image_two'])) $this->feature_image_two = is_array($data['feature_image_two']) ? json_encode($data['feature_image_two']) : $data['feature_image_two'];
        if (isset($data['feature_image_three'])) $this->feature_image_three = is_array($data['feature_image_three']) ? json_encode($data['feature_image_three']) : $data['feature_image_three'];
        if (isset($data['specifications_image'])) $this->specifications_image = is_array($data['specifications_image']) ? json_encode($data['specifications_image']) : $data['specifications_image'];
        if (isset($data['dimension_image'])) $this->dimension_image = is_array($data['dimension_image']) ? json_encode($data['dimension_image']) : $data['dimension_image'];
        if (isset($data['main_image_one_description'])) $this->main_image_one_description = $data['main_image_one_description'];
        if (isset($data['main_image_two_description'])) $this->main_image_two_description = $data['main_image_two_description'];
        if (isset($data['feature_image_one_description'])) $this->feature_image_one_description = $data['feature_image_one_description'];
        if (isset($data['feature_image_two_description'])) $this->feature_image_two_description = $data['feature_image_two_description'];
        if (isset($data['feature_image_three_description'])) $this->feature_image_three_description = $data['feature_image_three_description'];
        if (isset($data['feature_description'])) $this->feature_description = $data['feature_description'];
        if (isset($data['manufacturer_id']) || isset($data['manufacturer'])) $this->manufacturer_id = $data['manufacturer_id'] ?? $data['manufacturer']['manufacturer_id'];
        if (isset($data['vendor_id']) || isset($data['vendor'])) $this->vendor_id = $data['vendor_id'] ?? $data['vendor']['vendor_id'];
        if (isset($data['import_vendor_id'])) $this->import_vendor_id = $data['import_vendor_id'];
        if (isset($data['factory_vendor_id'])) $this->factory_vendor_id = $data['factory_vendor_id'];
        if (isset($data['product_range_id'])) $this->product_range_id = $data['product_range_id'];
        if (isset($data['product_category_id'])) $this->product_category_id = $data['product_category_id'];
        if (isset($data['edgetape_colour_id'])) $this->edgetape_colour_id = $data['edgetape_colour_id'];
        if (isset($data['requires_shipping'])) $this->requires_shipping = $data['requires_shipping'];
        if (isset($data['tax_type_id'])) $this->tax_type_id = $data['tax_type_id'];
        if (isset($data['material'])) $this->material = $data['material'];
        if (isset($data['weight'])) $this->weight = $data['weight'];
        if (isset($data['weight_type_id'])) $this->weight_type_id = $data['weight_type_id'];
        if (isset($data['length'])) $this->length = $data['length'];
        if (isset($data['length_type_id'])) $this->length_type_id = $data['length_type_id'];
        if (isset($data['width'])) $this->width = $data['width'];
        if (isset($data['height'])) $this->height = $data['height'];
        if (isset($data['depth'])) $this->depth = $data['depth'];
        if (isset($data['carton_qm'])) $this->carton_qm = $data['carton_qm'];
        if (isset($data['size'])) $this->size = $data['size'];
        if (isset($data['carton_width'])) $this->carton_width = $data['carton_width'];
        if (isset($data['carton_depth'])) $this->carton_depth = $data['carton_depth'];
        if (isset($data['carton_height'])) $this->carton_height = $data['carton_height'];
        if (isset($data['gross_weight'])) $this->gross_weight = $data['gross_weight'];
        if (isset($data['date_available'])) {
            $timestamp = strtotime($data['date_available']);
            $this->date_available = ($timestamp !== false) ? date('Y-m-d', $timestamp) : null;
        }
        if (isset($data['template'])) $this->template = $data['template'];
        if (isset($data['views'])) $this->views = $data['views'];
        if (isset($data['subtract_stock'])) $this->subtract_stock = $data['subtract_stock'] ? 1 : 0;
        if (isset($data['status'])) $this->status = $data['status'] ? 1 : 0;
        if (isset($data['is_featured'])) $this->is_featured = $data['is_featured'] ? 1 : 0;
        if (isset($data['sort_order'])) $this->sort_order = $data['sort_order'];
        if (isset($data['project_price_qty'])) $this->project_price_qty = $data['project_price_qty'];
        if (isset($data['project_price_discount'])) $this->project_price_discount = $data['project_price_discount'];
        if (isset($data['active'])) $this->active = $data['active'] ? 1 : 0;
        if (isset($data['archive'])) $this->archive = $data['archive'] ? 1 : 0;
        // if (isset($data['created_at'])) $this->created_at = $data['created_at'];
        // if (isset($data['updated_at'])) $this->updated_at = $data['updated_at'];
        $this->created_at = date('Y-m-d');
        $this->updated_at = date('Y-m-d');
        if (isset($data['min_order_quantity'])) $this->min_order_quantity = (int)$data['min_order_quantity'];
        if (isset($data['out_of_stock_status'])) $this->out_of_stock_status = $data['out_of_stock_status'];
        if (isset($data['length_class'])) $this->length_class = $data['length_class'];
        if (isset($data['weight_class'])) $this->weight_class = $data['weight_class'];
        if (isset($data['price'])) $this->price = $data['price'];
        if (isset($data['old_price'])) $this->old_price = $data['old_price'];

        if (isset($data['content'])) $this->content = new ProductContentData($data['content']);
        if (isset($data['manufacturer'])) $this->manufacturer = new ManufacturerData($data['manufacturer']);
        if (isset($data['vendor'])) $this->vendor = new VendorData($data['vendor']);
        if (isset($data['categories'])) $this->categories = $data['categories'] ?? [];
        if (isset($data['tags'])) $this->tags = $data['tags'] ?? [];
        if (isset($data['certificates'])) $this->certificates = $data['certificates'] ?? [];
        if (isset($data['resources'])) $this->resources = $data['resources'] ?? [];
        if (isset($data['relatedProducts'])) $this->relatedProducts = $data['relatedProducts'] ?? [];
        if (isset($data['familyProducts'])) $this->familyProducts = $data['familyProducts'] ?? [];
        if (isset($data['relatedProjects'])) $this->relatedProjects = $data['relatedProjects'] ?? [];
        if (isset($data['relatedResources'])) $this->relatedResources = $data['relatedResources'] ?? [];
        if (isset($data['variantProducts'])) $this->variantProducts = $data['variantProducts'] ?? [];
        if (isset($data['digitalAssets'])) $this->digitalAssets = $data['digitalAssets'] ?? [];
        if (isset($data['options'])) $this->options = $data['options'] ?? [];
        if (isset($data['attributes'])) $this->attributes = $data['attributes'] ?? [];
        if (isset($data['promotions'])) $this->promotions = $data['promotions'] ?? [];
        if (isset($data['images'])) $this->images = $data['images'] ?? [];
        $this->metadata = $data['metadata'] ?? self::getDefaultMetadata();
        if (isset($data['productVariants'])) $this->productVariants = $data['productVariants'] ?? [];
        if (isset($data['banner_way_points'])) $this->banner_way_points = is_string($data['banner_way_points']) ? $data['banner_way_points'] : json_encode($data['banner_way_points']);
        if (isset($data['ocean_plastic_used'])) $this->ocean_plastic_used = $data['ocean_plastic_used'] ? 1 : 0;
        if (isset($data['show_configurator'])) $this->show_configurator = $data['show_configurator'] ? 1 : 0;
        if (isset($data['store_link'])) $this->store_link = $data['store_link'];
        if (isset($data['catalogue_link'])) $this->catalogue_link = $data['catalogue_link'];
        if (isset($data['video_url'])) $this->video_url = is_array($data['video_url']) ? json_encode($data['video_url']) : $data['video_url'];

        if (isset($data['main_image_one_title'])) $this->main_image_one_title = $data['main_image_one_title'];
        if (isset($data['main_image_two_title'])) $this->main_image_two_title = $data['main_image_two_title'];
        if (isset($data['feature_image_one_title'])) $this->feature_image_one_title = $data['feature_image_one_title'];
        if (isset($data['feature_image_two_title'])) $this->feature_image_two_title = $data['feature_image_two_title'];
        if (isset($data['feature_image_three_title'])) $this->feature_image_three_title = $data['feature_image_three_title'];
    }

    public function toArray(): array
    {
        $data = [];
        if (isset($this->product_id)) $data['product_id'] = $this->product_id;
        if (isset($this->km_item_id)) $data['km_item_id'] = $this->km_item_id;
        if (isset($this->product_type_id)) $data['product_type_id'] = $this->product_type_id;
        if (isset($this->class_id)) $data['class_id'] = $this->class_id;
        if (isset($this->company_id)) $data['company_id'] = $this->company_id;
        if (isset($this->admin_id)) $data['admin_id'] = $this->admin_id;
        if (isset($this->parent_id)) $data['parent_id'] = $this->parent_id;
        if (isset($this->model)) $data['model'] = $this->model;
        if (isset($this->description)) $data['description'] = $this->description;
        if (isset($this->feature_description)) $data['feature_description'] = $this->feature_description;
        if (isset($this->specifications)) $data['specifications'] = $this->specifications;
        if (isset($this->warranty_period)) $data['warranty_period'] = $this->warranty_period;
        if (isset($this->product_code)) $data['product_code'] = $this->product_code;
        if (isset($this->product_family_code)) $data['product_family_code'] = $this->product_family_code;
        if (isset($this->factory_code)) $data['factory_code'] = $this->factory_code;
        if (isset($this->sku)) $data['sku'] = $this->sku;
        if (isset($this->isbn)) $data['isbn'] = $this->isbn;
        if (isset($this->barcode)) $data['barcode'] = $this->barcode;
        if (isset($this->track_stock)) $data['track_stock'] = $this->track_stock ? 1 : 0;
        if (isset($this->stock_quantity)) $data['stock_quantity'] = $this->stock_quantity;
        if (isset($this->stock_status_id)) $data['stock_status_id'] = $this->stock_status_id;
        if (isset($this->lead_days)) $data['lead_days'] = $this->lead_days;
        if (isset($this->melbourne_lead_days)) $data['melbourne_lead_days'] = $this->melbourne_lead_days;
        if (isset($this->safety_stock)) $data['safety_stock'] = $this->safety_stock;
        if (isset($this->qty_alert)) $data['qty_alert'] = $this->qty_alert;
        if (isset($this->banner_way_points)) $data['banner_way_points'] = $this->banner_way_points;
        if (isset($this->ocean_plastic_used)) $data['ocean_plastic_used'] = $this->ocean_plastic_used ? 1 : 0;
        if (isset($this->show_configurator)) $data['show_configurator'] = $this->show_configurator ? 1 : 0;
        if (isset($this->store_link)) $data['store_link'] = $this->store_link;
        if (isset($this->catalogue_link)) $data['catalogue_link'] = $this->catalogue_link;
        // $data['image'] = [];
        // $img = $this->image ? json_decode($this->image, true) : [];
        // if (is_array($img) && count($img) > 0 && isset($img[0]['objectURL'])) {
        //     $img[0]['status'] = ['severity' => 'success', 'name' => 'Uploaded'];
        //     $data['image'] = $img;
        // }

        $imageFields = ['image','banner_image', 'image_thumb','main_image_one','main_image_two','feature_image_one','feature_image_two','feature_image_three', 'specifications_image', 'dimension_image'];
        
        // foreach ($imageFields as $field) {
        //     $data[$field] = [];
        //     $img = $this->$field ? json_decode($this->$field, true) : [];
        
        //     if (is_array($img) && count($img) > 0 && isset($img[0]['objectURL'])) {
        //         $img[0]['status'] = ['severity' => 'success', 'name' => 'Uploaded'];
        //         $data[$field] = $img;
        //     }
        // }

        foreach ($imageFields as $field) {
            $data[$field] = null;
            if (!isset($this->$field) || !$this->$field) {
                continue;
            }
            $img = is_string($this->$field) ? json_decode($this->$field, true) : $this->$field;
            if (is_array($img) && count($img) > 0 && isset($img[0]['objectURL'])) {
                $img[0]['status'] = ['severity' => 'success', 'name' => 'Uploaded'];
                $data[$field] = json_encode($img);
            } elseif (is_string($this->$field)) {
                $data[$field] = $this->$field;
            }
        }

        if (isset($this->main_image_one_description)) $data['main_image_one_description'] = $this->main_image_one_description;
        if (isset($this->main_image_two_description)) $data['main_image_two_description'] = $this->main_image_two_description;
        if (isset($this->feature_image_one_description)) $data['feature_image_one_description'] = $this->feature_image_one_description;
        if (isset($this->feature_image_two_description)) $data['feature_image_two_description'] = $this->feature_image_two_description;
        if (isset($this->feature_image_three_description)) $data['feature_image_three_description'] = $this->feature_image_three_description;
        if (isset($this->manufacturer_id)) $data['manufacturer_id'] = $this->manufacturer_id;
        if (isset($this->vendor_id)) $data['vendor_id'] = $this->vendor_id;
        if (isset($this->import_vendor_id)) $data['import_vendor_id'] = $this->import_vendor_id;
        if (isset($this->factory_vendor_id)) $data['factory_vendor_id'] = $this->factory_vendor_id;
        if (isset($this->product_range_id)) $data['product_range_id'] = $this->product_range_id;
        if (isset($this->product_category_id)) $data['product_category_id'] = $this->product_category_id;
        if (isset($this->edgetape_colour_id)) $data['edgetape_colour_id'] = $this->edgetape_colour_id;
        if (isset($this->requires_shipping)) $data['requires_shipping'] = $this->requires_shipping ? 1 : 0;
        if (isset($this->tax_type_id)) $data['tax_type_id'] = $this->tax_type_id;
        if (isset($this->material)) $data['material'] = $this->material;
        if (isset($this->weight)) $data['weight'] = $this->weight;
        if (isset($this->weight_type_id)) $data['weight_type_id'] = $this->weight_type_id;
        if (isset($this->length)) $data['length'] = $this->length;
        if (isset($this->length_type_id)) $data['length_type_id'] = $this->length_type_id;
        if (isset($this->width)) $data['width'] = $this->width;
        if (isset($this->height)) $data['height'] = $this->height;
        if (isset($this->depth)) $data['depth'] = $this->depth;
        if (isset($this->carton_qm)) $data['carton_qm'] = $this->carton_qm;
        if (isset($this->size)) $data['size'] = $this->size;
        if (isset($this->carton_width)) $data['carton_width'] = $this->carton_width;
        if (isset($this->carton_depth)) $data['carton_depth'] = $this->carton_depth;
        if (isset($this->carton_height)) $data['carton_height'] = $this->carton_height;
        if (isset($this->gross_weight)) $data['gross_weight'] = $this->gross_weight;
        if (isset($this->date_available)) {
            $timestamp = strtotime($this->date_available);
            $data['date_available'] = ($timestamp !== false) ? date('Y-m-d', $timestamp) : null;
        }
        if (isset($this->template)) $data['template'] = $this->template;
        if (isset($this->views)) $data['views'] = $this->views;
        if (isset($this->subtract_stock)) $data['subtract_stock'] = $this->subtract_stock ? 1 : 0;
        if (isset($this->status)) $data['status'] = $this->status ? 1 : 0;
        if (isset($this->is_featured)) $data['is_featured'] = $this->is_featured ? 1 : 0;
        if (isset($this->sort_order)) $data['sort_order'] = $this->sort_order;
        if (isset($this->project_price_qty)) $data['project_price_qty'] = $this->project_price_qty;
        if (isset($this->project_price_discount)) $data['project_price_discount'] = $this->project_price_discount;
        if (isset($this->active)) $data['active'] = $this->active ? 1 : 0;
        if (isset($this->archive)) $data['archive'] = $this->archive ? 1 : 0;
        // if (isset($this->created_at)) $data['created_at'] = $this->created_at;
        // if (isset($this->updated_at)) $data['updated_at'] = $this->updated_at;
        $this->created_at = date('Y-m-d');
        $this->updated_at = date('Y-m-d');

        if (isset($this->min_order_quantity)) $data['min_order_quantity'] = $this->min_order_quantity;
        if (isset($this->out_of_stock_status)) $data['out_of_stock_status'] = $this->out_of_stock_status;
        // if(isset($this->length_class)) $data ['length_class'] = $this->length_class;
        // if(isset($this->weight_class)) $data ['weight_class'] = $this->weight_class;
        if (isset($this->price)) $data['price'] = $this->price;
        if (isset($this->old_price)) $data['old_price'] = $this->old_price;
        if (isset($this->banner_way_points)) $data['banner_way_points'] = $this->banner_way_points;
        if (isset($this->ocean_plastic_used)) $data['ocean_plastic_used'] = $this->ocean_plastic_used ? 1 : 0;
        if (isset($this->show_configurator)) $data['show_configurator'] = $this->show_configurator ? 1 : 0;
        if (isset($this->store_link)) $data['store_link'] = $this->store_link;
        if (isset($this->catalogue_link)) $data['catalogue_link'] = $this->catalogue_link;
        if (isset($this->video_url)) $data['video_url'] = $this->video_url ?? json_encode([]);


        if (isset($this->main_image_one_title)) $data['main_image_one_title'] = $this->main_image_one_title;
        if (isset($this->main_image_two_title)) $data['main_image_two_title'] = $this->main_image_two_title;
        if (isset($this->feature_image_one_title)) $data['feature_image_one_title'] = $this->feature_image_one_title;
        if (isset($this->feature_image_two_title)) $data['feature_image_two_title'] = $this->feature_image_two_title;
        if (isset($this->feature_image_three_title)) $data['feature_image_three_title'] = $this->feature_image_three_title;
        
        return $data;
    }
    public function getProductContent(): array|null
    {
        return isset($this->content) ? $this->content->toArray() : null;
    }
    public function getManufacturer(): array|null
    {
        return isset($this->manufacturer) ? $this->manufacturer->toArray() : null;
    }
    public function getVendor(): array|null
    {
        return isset($this->vendor) ? $this->vendor->toArray() : null;
    }
    public function getTags($product_id): array
    {
        $tags = [];
        if (!isset($product_id)) {
            return $tags;
        }
        foreach ($this->tags as $tag) {
            if (isset($tag['taxonomy_item_id'])) {
                $data['taxonomy_item_id'] = $tag['taxonomy_item_id'];
                $data['product_id'] = $product_id;
                $tags[] = $data;
            }
        }
        return $tags;
    }
    public function getCertificates(int $product_id, array $incomingCertificates): array
    {
        $certificates = [];
        
        if (empty($product_id) || empty($incomingCertificates)) {
            return $certificates;
        }

        // Define the static structure for the Green Logo
        $greenLogo = [
            [
                "id" => null,
                "file" => [
                    "name" => "green-afrdi-logo.png",
                    "size" => 0,
                    "type" => "image/jpeg",
                    "error" => 0,
                    "tmp_name" => "/media/Certificates//green-afrdi-logo.png",
                    "full_path" => "green-afrdi-logo.png"
                ],
                "name" => "green-afrdi-logo.png",
                "size" => 0,
                "type" => "image/jpeg",
                "image" => "/media/Certificates//green-afrdi-logo.png",
                "status" => [
                    "name" => "Uploaded",
                    "severity" => "success"
                ],
                "media_id" => null,
                "objectURL" => "/media/Certificates//green-afrdi-logo.png",
                "created_at" => "",
                "description" => "",
                "product_image_id" => null
            ]
        ];

        // Define the static structure for the Blue Logo
        $blueLogo = [
            [
                "id" => null,
                "file" => [
                    "name" => "blue-afrdi-logo.png",
                    "size" => 0,
                    "type" => "image/jpeg",
                    "error" => 0,
                    "tmp_name" => "/media/Certificates//blue-afrdi-logo.png",
                    "full_path" => "blue-afrdi-logo.png"
                ],
                "name" => "blue-afrdi-logo.png",
                "size" => 0,
                "type" => "image/jpeg",
                "image" => "/media/Certificates//blue-afrdi-logo.png",
                "status" => [
                    "name" => "Uploaded",
                    "severity" => "success"
                ],
                "media_id" => null,
                "objectURL" => "/media/Certificates//blue-afrdi-logo.png",
                "created_at" => "",
                "description" => "",
                "product_image_id" => null
            ]
        ];
        

        foreach ($incomingCertificates as $index => $certificateName) {
           
            $assignedLogo = null;
        
            if (stripos($certificateName, 'Green') !== false) {
                $assignedLogo = json_encode($greenLogo);
            } elseif (stripos($certificateName, 'Blue') !== false) {
                $assignedLogo = json_encode($blueLogo);
            } else {
                $assignedLogo = json_encode(null); // Fallback case if it matches neither
            }

            $certificates[] = [
                'product_id'           => $product_id,
                'certificate_type'     => $certificateName,
                'certificate_provider' => null,        
                'file_format' => "PDF",        
                'title' => $certificateName,       
                'logo'                 => $assignedLogo, 
                'certificate_file'     => json_encode(null),
                'sort_order'           => 0,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s')
            ];
        }

        return $certificates;
    }
    public function getCategories($product_id): array
    {
        $categories = [];
        if (!isset($product_id)) {
            return $categories;
        }
        $sort = 100;
        $sort_orders = array_column($this->categories, 'product_sort_order');
        if($sort_orders && is_array($sort_orders) && count($sort_orders) > 0){
            $sort = max($sort_orders) + 1;
        }
       
        foreach ($this->categories as $category) {
            if (isset($category['id'])) {
                $category['product_id'] = $product_id;
                if(!isset($category['product_sort_order'])){
                    $category['product_sort_order'] = $sort;
                    $sort++;
                }
                $categoryObject = new ProductCategory($category);
                $data = $categoryObject->toArray();
                if (isset($data['product_id']) && isset($data['taxonomy_item_id'])) {
                    $categories[] = $data;
                }
            }
        }
        return $categories;
    }

    public function getCategoriesIdentifiers($product_id): array
    {
        $categories = [];
        foreach ($this->categories as $category) {
            $category['product_id'] = $product_id;
            $categoryObject = new ProductCategory($category);
            $categories[] = $categoryObject->concat();
        }
        return $categories;
    }
    public function getRelatedProducts($product_id): array
    {
        $relatedProducts = [];
        if (!isset($product_id)) {
            return $relatedProducts;
        }
        foreach ($this->relatedProducts as $relatedProduct) {
            if (isset($relatedProduct['product_id'])) {
                $relatedProduct['product_related_id'] = $relatedProduct['product_id'];
                $relatedProduct['product_id'] = $product_id;
                $relatedProductObject = new ProductRelateds($relatedProduct);
                $data = $relatedProductObject->toArray();
                $relatedProducts[] = $data;
            }
        }
        return $relatedProducts;
    }
    public function getFamilyProducts($product_id, $product_family_code): array
    {
        $familyProducts = [];
        if (!isset($product_family_code)) {
            return $familyProducts;
        }
        foreach ($this->familyProducts as $familyProduct) {
            if (isset($familyProduct['product_id'])) {
                $data = [];
                $data['product_id'] = $familyProduct['product_id'];
                $data['product_code'] = $familyProduct['product_code'];
                $data['product_family_code'] = $product_family_code;
                $familyProducts[] = $data;
            }
        }
        return $familyProducts;
    }
    public function getProductRelatedProjects(int $product_id): array
    {
        $projects = [];
        if (!isset($product_id)) {
            return $projects;
        }
        foreach ($this->relatedProjects as $key => $project) {
            if (isset($project['project_id'])) {
                $data = [];
                $data['project_id'] = $project['project_id'];
                $data['product_id'] = $product_id;
                $data['sort_order'] = $key +1;
                $projects[] = $data;
            }
        }
        return $projects;
    }
    public function getProductRelatedResources(int $product_id): array
    {
        $resources = [];
        if (!isset($product_id)) {
            return $resources;
        }
        foreach ($this->relatedResources as $key => $resource) {
            if (isset($resource['design_resource_id'])) {
                $data = [];
                $data['design_resource_id'] = $resource['design_resource_id'];
                $data['resource_type'] = $resource['type'];
                $data['product_id'] = $product_id;
                $data['sort_order'] = $key +1;
                $resources[] = $data;
            }
        }
        return $resources;
    }
    public function getVariantProducts($product_id): array
    {
        $variantProducts = [];
        if (!isset($product_id)) {
            return $variantProducts;
        }
        foreach ($this->variantProducts as $variantProduct) {
            if (isset($variantProduct['product_id'])) {
                $variantProduct['product_variant_id'] = $variantProduct['product_id'];
                $variantProduct['product_id'] = $product_id;
                $variantProductObject = new ProductVariants($variantProduct);
                $data = $variantProductObject->toArray();
                $variantProducts[] = $data;
            }
        }
        return $variantProducts;
    }
    public function getDigitalAssets($product_id): array
    {
        $digitalAssets = [];
        if (!isset($product_id)) {
            return $digitalAssets;
        }
        foreach ($this->digitalAssets as $digitalAsset) {
            if (isset($digitalAsset['digital_asset_id'])) {
                $digitalAsset['product_id'] = $product_id;
                $digitalAssetObject = new ProductDigitalAsset($digitalAsset);
                $data = $digitalAssetObject->toArray();
                $digitalAssets[] = $data;
            }
        }
        return $digitalAssets;
    }
    public function getAttributes($product_id, $language_id = 1): array
    {
        $attributes = [];
        $attributeContents = [];
        if (!isset($product_id)) {
            return $attributes;
        }
        foreach ($this->attributes as $attribute) {
            //Prepare attribute and attribute content arrays
            if (isset($attribute['product_id']) && isset($attribute['attribute_id'])) {
                $attribute['product_id'] = $product_id;
                $attribute['language_id'] = $language_id;
                $attributeObject = new ProductAttributeModel($attribute);
                $data = $attributeObject->toArray();
                // $attributes[] = $data;
                $attributes[] = [
                    'attribute_code' => str_replace(' ', '-', strtolower(trim($data['name']))),
                    'attribute_group_id' => $data['attribute_group_id'],
                    'sort_order' => $data['sort_order'],
                ];
                $attributeContents[] = [
                    'attribute_id' => $data['attribute_id'],
                    'language_id' => $language_id,
                    'name' => $data['name'],
                ];
            }
        }
        //Need to return attribute and attribute content arrays
        return [$attributes, $attributeContents];
    }
    public function getProductOptions($product_id): array
    {
        $options = [];
        if (!isset($product_id)) {
            return $options;
        }

        // Debug: Log the options array
        foreach ($this->options as $index => $option) {
            error_log("Processing option at index {$index}: " . json_encode($option));


            try {
                $option['product_id'] = $product_id;
                error_log("Creating ProductOptionModel with data: " . json_encode($option));

                $optionObject = new ProductOptionModel($option);
                $data = $optionObject->toArray();
                $options[] = $data;

                error_log("Successfully created option: " . json_encode($data));
            } catch (\Throwable $e) {
                // Log the error for debugging
                error_log("Error creating ProductOptionModel: " . $e->getMessage());
                error_log("Error file: " . $e->getFile() . " line: " . $e->getLine());
                error_log("Option data: " . json_encode($option));
                error_log("Stack trace: " . $e->getTraceAsString());

                // You can also throw the exception if you want to stop processing
                // throw $e;

                // Or continue with the next option
                continue;
            }
        }
        return $options;
    }
    public function getProductPromotions($product_id): array
    {
        $promotions = [];
        if (!isset($product_id)) {
            return $promotions;
        }
        foreach ($this->promotions as $promotion) {
            $promotion['product_id'] = $product_id;
            $promotionObject = new ProductPromotionModel($promotion);
            $data = $promotionObject->toArray();
            if (isset($data['product_id']) && isset($data['promotion_id'])) {
                $promotions[] = $data;
            }
        }
        return $promotions;
    }
    public function getProductImages($product_id): array
    {
        $images = [];
        if (!isset($product_id)) {
            return $images;
        }
        foreach ($this->images as $img) {
            $image = [];
            $image['product_id'] = $product_id;
            $image['image'] = $img['objectURL'];
            // 
            // $image['image'] = json_encode(['url' => $img['objectURL']]);
            $images[] = $image;
        }
        $ss = $images;
        return $images;
    }
    public static function getDefaultMetadata(): array
    {
        return [
            'enSeo' => [
                'meta_keywords' => '',
                'meta_description' => '',
                'meta_content' => '',
            ],
            'openGraph' => [
                'openGraphTitle' => '',
                'openGraphDescription' => '',
            ],
            'twitter' => [
                'twitterTitle' => '',
                'twitterDescription' => '',
                'twitterLabel1' => '',
                'twitterLabel2' => '',
                'twitterData1' => '',
                'twitterData2' => '',
            ],
        ];
    }

    public function getProductMetadata($product_id): array
    {
        $data = [];
        if (!isset($product_id)) {
            return $data;
        }
        foreach ($this->metadata as $namespace => $meta) {
            $metadata = [];
            $metadata['namespace'] = $namespace;
            foreach ($meta as $key => $value) {
                $metadata['product_id'] = $product_id;
                $metadata['key'] = $key;
                $metadata['value'] = $value;
                $data[] = $metadata;
            }
        }
        return $data;
    }
}

class ProductContentData
{
    public int $language_id;
    public ?string $name;
    public ?string $title;
    public ?string $slug;
    public ?string $content;
    public ?string $tag;
    public ?string $tag_line;
    public ?string $rules;
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;

    public function __construct(array $data)
    {
        if (isset($data['language_id'])) $this->language_id = $data['language_id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['title'])) $this->title = $data['title'];
        if (isset($data['slug'])) $this->slug = makeSlug($data['slug']);
        if (isset($data['content'])) $this->content = $data['content'];
        if (isset($data['tag_line'])) $this->tag_line = $data['tag_line'];
        if (isset($data['rules'])) $this->rules = $data['rules'];
        if (isset($data['tag'])) $this->tag = $data['tag'];
        if (isset($data['meta_title'])) $this->meta_title = $data['meta_title'];
        if (isset($data['meta_description'])) $this->meta_description = $data['meta_description'];
        if (isset($data['meta_keywords'])) $this->meta_keywords = $data['meta_keywords'];
    }
    public function toArray(): array
    {
        return [
            'language_id' => $this->language_id,
            'name' => $this->name,
            'title' => $this->title,
            'slug' => makeSlug($this->slug),
            'content' => $this->content,
            'tag_line' => $this->tag_line,
            'rules' => $this->rules,
            'tag' => $this->tag,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
        ];
    }
}

class ManufacturerData
{
    public int $manufacturer_id;
    public string $name;
    public string $slug;
    public string $image;
    public int $sort_order;

    public function __construct(array $data)
    {
        if (isset($data['manufacturer_id'])) $this->manufacturer_id = $data['manufacturer_id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['slug'])) $this->slug = makeSlug($data['slug']);
        if (isset($data['image'])) $this->image = $data['image'];
        if (isset($data['sort_order'])) $this->sort_order = $data['sort_order'];
    }
    public function toArray(): array
    {
        return [
            'manufacturer_id' => $this->manufacturer_id,
            'name' => $this->name,
            'slug' => makeSlug($this->slug),
            'image' => $this->image,
            'sort_order' => $this->sort_order,
        ];
    }
}

class VendorData
{
    public int $vendor_id;
    public int $admin_id;
    public string $name;
    public string $slug;
    public string $image = '';
    public int $sort_order;

    public function __construct(array $data)
    {
        if (isset($data['vendor_id'])) $this->vendor_id = $data['vendor_id'];
        if (isset($data['admin_id'])) $this->admin_id = $data['admin_id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['slug'])) $this->slug = makeSlug($data['slug']);
        // if (isset($data['image'])) $this->image = $data['image'];
        if (isset($data['image'])) {
            if (is_array($data['image'])) {
                // Uploaded file array হলে filename নিবে
                $this->image = (string) ($data['image']['name'] ?? '');
            } else {
                $this->image = (string) $data['image'];
            }
        }
        if (isset($data['sort_order'])) $this->sort_order = $data['sort_order'];
    }
    public function toArray(): array
    {
        return [
            'vendor_id' => $this->vendor_id,
            'admin_id' => $this->admin_id,
            'name' => $this->name,
            'slug' => makeSlug($this->slug),
            'image' => $this->image,
            'sort_order' => $this->sort_order,
        ];
    }
}

class ProductCategory
{
    public int $product_id;
    public int $taxonomy_item_id;
    public int $sort_order;

    public function __construct(array $data)
    {
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['id'])) $this->taxonomy_item_id = $data['id'];
        if (isset($data['product_sort_order'])) $this->sort_order = $data['product_sort_order'];
    }
    public function toArray(): array
    {
        if (isset($this->product_id) && isset($this->taxonomy_item_id)) {
            return [
                'product_id' => $this->product_id,
                'taxonomy_item_id' => $this->taxonomy_item_id,
                'sort_order' => $this->sort_order,
            ];
        }
        return [];
    }
    public function concat(): string
    {
        return $this->product_id . '-' . $this->taxonomy_item_id;
    }
}

class ProductRelateds
{
    public int $product_id;
    public int $product_related_id;

    public function __construct(array $data)
    {
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['product_related_id'])) $this->product_related_id = $data['product_related_id'];
    }
    public function toArray(): array
    {
        if (isset($this->product_id) && isset($this->product_related_id)) {
            return [
                'product_id' => $this->product_id,
                'product_related_id' => $this->product_related_id,
            ];
        }
        return [];
    }
}

class ProductVariants
{
    public int $product_id;
    public int $product_variant_id;

    public function __construct(array $data)
    {
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['product_variant_id'])) $this->product_variant_id = $data['product_variant_id'];
    }
    public function toArray(): array
    {
        if (isset($this->product_id) && isset($this->product_variant_id)) {
            return [
                'product_id' => $this->product_id,
                'product_variant_id' => $this->product_variant_id,
            ];
        }
        return [];
    }
}

class ProductDigitalAsset
{
    public int $product_id;
    public int $digital_asset_id;

    public function __construct(array $data)
    {
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['digital_asset_id'])) $this->digital_asset_id = $data['digital_asset_id'];
    }
    public function toArray(): array
    {
        if (isset($this->product_id) && isset($this->digital_asset_id)) {
            return [
                'product_id' => $this->product_id,
                'digital_asset_id' => $this->digital_asset_id,
            ];
        }
        return [];
    }
}

class ProductAttributeModel
{
    public int $product_id = 0;
    public ?int $attribute_id = null;
    public ?int $attribute_group_id = 1;
    public ?int $sort_order = 0;
    public ?int $language_id = 1;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $metadata = null;
    public string|int|null $type = null;
    public string|int|null $value = null;
    public string|array|null $image = null;

    public function __construct(array $data)
    {
        $this->product_id = $data['product_id'] ?? 0;
        $this->attribute_id = $data['attribute_id'] ?? null;
        $this->attribute_group_id = $data['attribute_group_id'] ?? 1;
        $this->sort_order = $data['sort_order'] ?? 0;
        $this->language_id = $data['language_id'] ?? 1;
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->metadata = $data['metadata'] ?? null;
        $this->type = isset($data['type']) ? (string)$data['type'] : null;
        $this->value = isset($data['value']) ? (string)$data['value'] : null;

        if (isset($data['image'])) {
            if (is_array($data['image']) && isset($data['image'][0]['image'])) {
                $this->image = $data['image'][0]['image'];
            } else {
                $this->image = $data['image'];
            }
        }
    }

    public function toArray(): array
    {
        return [
            'attribute_group_id' => $this->attribute_group_id,
            'sort_order' => $this->sort_order,
            'name' => $this->name,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'type' => $this->type,
            'value' => $this->value,
            'image' => $this->image,
            'language_id' => $this->language_id,
            'attribute_id' => $this->attribute_id,
        ];
    }
}



class ProductOptionModel
{
    public ?int $product_option_id = null;
    public ?int $product_id = null;
    public int $option_id = 1;
    public ?string $name = null;
    public mixed $type = null;
    public mixed $value = null;
    public ?string $metadata = null;

    public function __construct(array $data)
    {
        if (isset($data['product_option_id'])) $this->product_option_id = $data['product_option_id'];
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['option_id'])) $this->option_id = $data['option_id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['value'])) $this->value = json_encode($data['value']);
        if (isset($data['metadata'])) $this->metadata = $data['metadata'];
    }

    public function toArray(): array
    {
        $data = [];
        if (isset($this->product_id)) $data['product_id'] = $this->product_id;
        if (isset($this->option_id)) $data['option_id'] = $this->option_id;
        if (isset($this->name)) $data['name'] = $this->name;
        if (isset($this->type['type_id'])) $data['type_id'] = $this->type['type_id'];
        if (isset($this->value)) $data['value'] = $this->value;
        if (isset($this->metadata)) $data['meta_description'] = $this->metadata;

        return $data;
    }
}

class ProductInventoryData
{
    public string $model;
    public string $sku;
    public string $upc;
    public string $ean;
    public string $jan;
    public string $isbn;
    public string $mpn;
    public string $storage_location;
    public float $price;
    public float $old_price;
    public int $points;
    public string $tax_class;
    public int $quantity;
    public int $min_order_quantity;
    public bool $subtract_stock;
    public string $out_of_stock_status;
    public string $date_available;
    public bool $status;

    public function __construct(array $data)
    {
        if (isset($data['model'])) $this->model = $data['model'];
        if (isset($data['sku'])) $this->sku = $data['sku'];
        if (isset($data['upc'])) $this->upc = $data['upc'];
        if (isset($data['ean'])) $this->ean = $data['ean'];
        if (isset($data['jan'])) $this->jan = $data['jan'];
        if (isset($data['isbn'])) $this->isbn = $data['isbn'];
        if (isset($data['mpn'])) $this->mpn = $data['mpn'];
        if (isset($data['storage_location'])) $this->storage_location = $data['storage_location'];
        if (isset($data['price'])) $this->price = $data['price'];
        if (isset($data['old_price'])) $this->old_price = $data['old_price'];
        if (isset($data['points'])) $this->points = $data['points'];
        if (isset($data['tax_class'])) $this->tax_class = $data['tax_class'];
        if (isset($data['quantity'])) $this->quantity = $data['quantity'];
        if (isset($data['min_order_quantity'])) $this->min_order_quantity = $data['min_order_quantity'];
        if (isset($data['subtract_stock'])) $this->subtract_stock = $data['subtract_stock'];
        if (isset($data['out_of_stock_status'])) $this->out_of_stock_status = $data['out_of_stock_status'];
        if (isset($data['date_available'])) $this->date_available = $data['date_available'];
        if (isset($data['status'])) $this->status = $data['status'];
    }
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'sku' => $this->sku,
            'upc' => $this->upc,
            'ean' => $this->ean,
            'jan' => $this->jan,
            'isbn' => $this->isbn,
            'mpn' => $this->mpn,
            'storage_location' => $this->storage_location,
            'price' => $this->price,
            'old_price' => $this->old_price,
            'points' => $this->points,
            'tax_class' => $this->tax_class,
            'quantity' => $this->quantity,
            'min_order_quantity' => $this->min_order_quantity,
            'subtract_stock' => $this->subtract_stock,
            'out_of_stock_status' => $this->out_of_stock_status,
            'date_available' => $this->date_available,
            'status' => $this->status,
        ];
    }
}

class ProductPromotionModel
{
    public int $product_id;
    public int $user_group_id = 1;
    public string $user_group_name;
    public int $priority;
    public float $price;
    public string $from_date;
    public string $to_date;

    public function __construct(array $data)
    {
        if (isset($data['product_id'])) $this->product_id = $data['product_id'];
        if (isset($data['user_group_id'])) $this->user_group_id = $data['user_group_id'];
        if (isset($data['user_group_name'])) $this->user_group_name = $data['user_group_name'];
        if (isset($data['priority'])) $this->priority = (int)$data['priority'];
        if (isset($data['price'])) $this->price = (float)$data['price'];
        if (isset($data['from_date'])) $this->from_date = $data['from_date'];
        if (isset($data['to_date'])) $this->to_date = $data['to_date'];
    }
    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'user_group_id' => $this->user_group_id,
            'user_group_name' => $this->user_group_name,
            'priority' => $this->priority,
            'price' => $this->price,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ];
    }
}
