<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

use function App\Core\System\utils\env;
use function PHPUnit\Framework\isEmpty;

class ProductDataValidation
{

    const SPECIFICATIONS_IMAGE_PATH = '/media/Products/specification/';
    const BANNER_IMAGE_PATH = '/media/Products/banner/';
    const IMAGE_PATH = '/media/Products/image/';
    const IMAGE_THUMB_PATH = '/media/Products/thumbnails/';
    const MAIN_IMAGE_ONE_PATH = '/media/Products/main-image-one/';
    const MAIN_IMAGE_TWO_PATH = '/media/Products/main-image-two/';
    const FEATURE_IMAGE_ONE_PATH = '/media/Products/feature/';

    private array $nullableIntegerFields;
    private bool $isValidData = true;
    public bool $isExistingData = false;
    private array $errors = [];
    private array $rawData = [];
    private array $requiredFields = [];

    public stdClass $product;
    public stdClass $product_content;
    public stdClass $media;

    public array $categories;
    public array $tags;
    // public array $new_categories_data;
    public array $categories_data;
    /**
     * @param array $categories = [ 'Workstations' => 1, 'Screens' => 2, 'Gaming' => 3, ... ]
     */

    public function __construct(array $data, array $categoryMap = [], array $tagMap = [], array $productMap = [])
    {
        // Check if category_one isset or .... 

        // If isset then find category id from $categories 

        // Check if product_id exsit then set $this->categores[] = [ 'category_id' => $category_id, 'product_id' => $product_id ]

        //If not product_id then use unique column which is product_code and set $this->categories_data[] = [ 'category_id' => $category_id, 'product_code' => $product_code ]

        // Then later in the repository after inserting product update each category_id with product_id by filtering id using product_code (Follow the post repository for reference)

        // Clean up nullable integer fields to ensure they are either valid integers or null
        // -----------------------------
        // Initialize nullable integer fields
        // These fields can be null if not provided or invalid in CSV
        // -----------------------------
        $this->nullableIntegerFields = [
            'parent_id',
            'manufacturer_id',
            'vendor_id',
            'import_vendor_id',
            'factory_vendor_id',
            'product_range_id',
            'edgetape_colour_id',
            'tax_type_id',
            'weight_type_id',
            'length_type_id',
            'out_of_stock_status',
            'size',
            'project_price_qty',
        ];
        // Clean up nullable decimal fields to ensure they are either valid decimals or null
        // -----------------------------
        // Nullable decimal fields
        // These will be validated as floats or null if missing/invalid
        // -----------------------------
        $nullableDecimalFields = [
            'width',
            'height',
            'depth',
            'price',
            'old_price',
            'carton_qm',
            'gross_weight',
            'weight',
            'length',
            'carton_width',
            'carton_depth',
            'carton_height',
            'project_price_discount'
        ];

        // Ensure required fields that cannot be null are properly set
        // -----------------------------
        // Required fields
        // These cannot be empty or null; validation will enforce them
        // -----------------------------
        $this->requiredFields = [
            'product_type_id',
            'product_code',
            'sku',
            'admin_id',
            'track_stock',
            'product_category_id',
            'is_featured',
            'sort_order',
            'active'
        ];

        // -----------------------------
        // Image fields (JSON strings)
        // For structured images (thumbnails, main images, feature images)
        // -----------------------------
        $imageFields = [
            'image',
            'image_thumb',
            'main_image_one',
            'main_image_two',
            'feature_image_one',
            'feature_image_two',
            'feature_image_three',
            'specifications_image',
            'banner_image'
        ];

        // -----------------------------
        // Initialize product objects
        // stdClass is used to hold validated product and product content
        // -----------------------------
        $this->product = new stdClass();
        $this->product_content = new stdClass();
        $this->rawData = $data;

        // -----------------------------
        // Initialize category arrays
        // categories_data will hold mapping using product_code for later insertion
        // -----------------------------
        $this->categories = [];
        // $this->new_categories_data = [];
        $this->categories_data = [];
        // intsertion tags
        $this->tags = [];

        // Check if product_id exsit then set $this->isExistingData = true
        if (isset($data['product_code']) && !empty($data['product_code']) && $data['product_code']) {
            if (isset($productMap[$data['product_code']]) && $productMap[$data['product_code']] > 0) {
                // $this->product->product_id = $productMap[$data['product_code']];
                $this->isExistingData = true;
            }
        }

        // Handle categories Normalize category input: allow both comma-separated string or array
        // -----------------------------
        // Normalize category input from CSV
        // Accept comma-separated string or array
        // Trim spaces and process categories into $this->categories_data
        // -----------------------------
        if (!empty($data['category'])) {
            $categories = is_string($data['category']) ? explode(',', $data['category']) : $data['category'];
            $data['category'] = array_map('trim', $categories);
            $this->processCategories($data, $categoryMap);
        }

        // -----------------------------
        // Normalize Tags input from CSV
        // Accept comma-separated string or array
        // Trim spaces and process tag into $this->tags
        // -----------------------------
        if (!empty($data['tags'])) {
            // Accept comma-separated string or array
            $tags = is_string($data['tags']) ? explode(',', $data['tags']) : $data['tags'];
            $data['tags'] = array_map('trim', $tags);
            $this->processTags($data, $tagMap); // i will pull all tags here
        }
        // $this->toArray();

        // Product main fields
        // -----------------------------
        // Validate and assign integer fields
        // Includes product_id, km_item_id, product_type_id, class_id, company_id, admin_id, parent_id
        // -----------------------------
        if (isset($data['product_id'])) $this->product->product_id = $this->validateInteger($data['product_id'], 'product_id') ?? null;
        if (isset($data['km_item_id'])) $this->product->km_item_id = $this->validateInteger($data['km_item_id'], 'km_item_id', 0) ?? 0;
        // if (isset($data['product_type_id'])) $this->product->product_type_id = $this->validateInteger($data['product_type_id'], 'product_type_id', 1, true) ?? 1;
        $this->product->product_type_id = isset($data['product_type_id']) 
            ? ($this->validateInteger($data['product_type_id'], 'product_type_id', 1, true) ?? 1)
            : 1;

        if (isset($data['class_id'])) $this->product->class_id = $this->validateInteger($data['class_id'], 'class_id', 1) ?? 1;
        if (isset($data['company_id'])) $this->product->company_id = $this->validateInteger($data['company_id'], 'company_id', 1) ?? 1;
        if (isset($data['admin_id'])) $this->product->admin_id = $this->validateInteger($data['admin_id'], 'admin_id', 1) ?? 1;
        if (isset($data['parent_id'])) $this->product->parent_id = $this->validateInteger($data['parent_id'], 'parent_id') ?? null;

        // String fields
        // -----------------------------
        // Validate string fields with max lengths
        // Prevent database errors and truncate long values
        // -----------------------------
        if (isset($data['model'])) $this->product->model = $this->validateString($data['model'], 'model', 64) ?? '';
        if (isset($data['main_image_one_description'])) $this->product->description = $this->validateString($data['main_image_one_description'], 'description', 500) ?? '';
        if (isset($data['specifications'])) $this->product->specifications = $this->validateString($data['specifications'], 'specifications', 1000) ?? '';
        if (isset($data['warranty_period'])) $this->product->warranty_period = $this->validateString($data['warranty_period'], 'warranty_period', 10) ?? '';
        if (isset($data['product_code'])) $this->product->product_code = $this->validateString($data['product_code'], 'product_code', 50, true) ?? '';
        if (isset($data['product_family'])) $this->product->product_family_code = $this->validateString($data['product_family'], 'product_family', 191) ?? '';
        if (isset($data['factory_code'])) $this->product->factory_code = $this->validateString($data['factory_code'], 'factory_code', 255) ?? '';
        if (isset($data['sku'])) $this->product->sku = $this->validateString($data['product_code'], 'sku', 64) ?? '';
        if (isset($data['isbn'])) $this->product->isbn = $this->validateString($data['isbn'], 'isbn', 17) ?? '';
        if (isset($data['barcode'])) $this->product->barcode = $this->validateString($data['barcode'], 'barcode', 13) ?? '';
        if (isset($data['store_link'])) $this->product->store_link = $this->validateString($data['store_link'], 'store_link', 255) ?? '';
        if (isset($data['catalogue_link'])) $this->product->catalogue_link = $this->validateString($data['catalogue_link'], 'catalogue_link', 255) ?? '';

        // Boolean fields as integers
        // -----------------------------
        // Boolean / tinyint fields as integers
        // Converts true/false or empty strings to 0/1 for DB
        // -----------------------------
        if (isset($data['track_stock'])) $this->product->track_stock = $this->validateInteger($data['track_stock'], 'track_stock', 0) ?? 0;
        if (isset($data['requires_shipping'])) $this->product->requires_shipping = $this->validateInteger($data['requires_shipping'], 'requires_shipping', 0) ?? 0;
        if (isset($data['subtract_stock'])) $this->product->subtract_stock = $this->validateInteger($data['subtract_stock'], 'subtract_stock', 0) ?? 0;
        if (isset($data['status'])) $this->product->status = $this->validateInteger($data['status'], 'status', 0) ?? 0;
        if (isset($data['is_featured'])) $this->product->is_featured = $this->validateInteger($data['is_featured'], 'is_featured', 0) ?? 0;
        if (isset($data['active'])) $this->product->active = $this->validateInteger($data['active'], 'active', 0) ?? 0;
        if (isset($data['archive'])) $this->product->archive = $this->validateInteger($data['archive'], 'archive', 0) ?? 0;

        // Integer fields
        // -----------------------------
        // Integer fields for stock, lead times, and IDs
        // Ensures numeric values are safe for DB
        // -----------------------------
        if (isset($data['stock_quantity'])) $this->product->stock_quantity = $this->validateInteger($data['stock_quantity'], 'stock_quantity', 0) ?? 0;
        if (isset($data['stock_status_id'])) $this->product->stock_status_id = $this->validateInteger($data['stock_status_id'], 'stock_status_id', 1) ?? 1;
        if (isset($data['lead_days'])) $this->product->lead_days = $this->validateInteger($data['lead_days'], 'lead_days', 0) ?? 0;
        if (isset($data['melbourne_lead_days'])) $this->product->melbourne_lead_days = $this->validateInteger($data['melbourne_lead_days'], 'melbourne_lead_days', 0) ?? 0;
        if (isset($data['safety_stock'])) $this->product->safety_stock = $this->validateInteger($data['safety_stock'], 'safety_stock', 0) ?? 0;
        if (isset($data['qty_alert'])) $this->product->qty_alert = $this->validateInteger($data['qty_alert'], 'qty_alert', 0) ?? 0;
        if (isset($data['manufacturer_id'])) $this->product->manufacturer_id = $this->validateInteger($data['manufacturer_id'], 'manufacturer_id') ?? null;
        if (isset($data['vendor_id'])) $this->product->vendor_id = $this->validateInteger($data['vendor_id'], 'vendor_id') ?? null;
        if (isset($data['import_vendor_id'])) $this->product->import_vendor_id = $this->validateInteger($data['import_vendor_id'], 'import_vendor_id') ?? null;
        if (isset($data['factory_vendor_id'])) $this->product->factory_vendor_id = $this->validateInteger($data['factory_vendor_id'], 'factory_vendor_id') ?? null;
        if (isset($data['product_range_id'])) $this->product->product_range_id = $this->validateInteger($data['product_range_id'], 'product_range_id') ?? null;
        // if (isset($data['category'])) $this->product->product_category_id = $categories[$data['category']] ?? null;
        // if (isset($data['product_category_id'])) $this->product->product_category_id = $this->validateInteger($data['product_category_id'], 'product_category_id', 1) ?? 1;
        if (isset($data['edgetape_colour_id'])) $this->product->edgetape_colour_id = $this->validateInteger($data['edgetape_colour_id'], 'edgetape_colour_id') ?? null;
        if (isset($data['tax_type_id'])) $this->product->tax_type_id = $this->validateInteger($data['tax_type_id'], 'tax_type_id') ?? null;
        if (isset($data['weight_type_id'])) $this->product->weight_type_id = $this->validateInteger($data['weight_type_id'], 'weight_type_id') ?? null;
        if (isset($data['length_type_id'])) $this->product->length_type_id = $this->validateInteger($data['length_type_id'], 'length_type_id') ?? null;
        if (isset($data['min_order_quantity'])) $this->product->min_order_quantity = $this->validateInteger($data['min_order_quantity'], 'min_order_quantity', 1) ?? 1;
        if (isset($data['views'])) $this->product->views = $this->validateInteger($data['views'], 'views', 0) ?? 0;
        if (isset($data['sort_order'])) $this->product->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0) ?? 0;
        if (isset($data['project_price_qty'])) $this->product->project_price_qty = $this->validateInteger($data['project_price_qty'], 'project_price_qty') ?? null;

        // Float fields
        // -----------------------------
        // Float fields
        // Converts numeric values to decimals, defaults if missing
        // -----------------------------
        if (isset($data['weight'])) $this->product->weight = $this->validateFloat($data['weight'], 'weight', 0.0) ?? 0.0;
        if (isset($data['length'])) $this->product->length = $this->validateFloat($data['length'], 'length', 0.0) ?? 0.0;
        if (isset($data['width'])) $this->product->width = $this->validateFloat($data['width'], 'width') ?? null;
        if (isset($data['height'])) $this->product->height = $this->validateFloat($data['height'], 'height') ?? null;
        if (isset($data['depth'])) $this->product->depth = $this->validateFloat($data['depth'], 'depth') ?? null;
        if (isset($data['price'])) $this->product->price = $this->validateFloat($data['price'], 'price') ?? null;
        if (isset($data['old_price'])) $this->product->old_price = $this->validateFloat($data['old_price'], 'old_price') ?? null;
        if (isset($data['carton_qm'])) $this->product->carton_qm = $this->validateFloat($data['carton_qm'], 'carton_qm') ?? null;
        if (isset($data['carton_width'])) $this->product->carton_width = $this->validateFloat($data['carton_width'], 'carton_width', 0.0) ?? 0.0;
        if (isset($data['carton_depth'])) $this->product->carton_depth = $this->validateFloat($data['carton_depth'], 'carton_depth', 0.0) ?? 0.0;
        if (isset($data['carton_height'])) $this->product->carton_height = $this->validateFloat($data['carton_height'], 'carton_height', 0.0) ?? 0.0;
        if (isset($data['gross_weight'])) $this->product->gross_weight = $this->validateFloat($data['gross_weight'], 'gross_weight') ?? null;
        if (isset($data['project_price_discount'])) $this->product->project_price_discount = $this->validateFloat($data['project_price_discount'], 'project_price_discount', 0.0) ?? 0.0;

        // Additional string fields
        // -----------------------------
        // String fields
        // Converts string values to string, defaults if missing
        // -----------------------------
        if (isset($data['material'])) $this->product->material = $this->validateString($data['material'], 'material', 64) ?? '';
        if (isset($data['out_of_stock_status'])) $this->product->out_of_stock_status = $this->validateString($data['out_of_stock_status'], 'out_of_stock_status', 100) ?? '';
        if (isset($data['size'])) $this->product->size = $this->validateString($data['size'], 'size', 255) ?? '';
        if (isset($data['date_available'])) $this->product->date_available = $this->validateString($data['date_available'], 'date_available', 255) ?? '';
        if (isset($data['template'])) $this->product->template = $this->validateString($data['template'], 'template', 191) ?? '';
        if (isset($data['video_link'])) $this->product->video_link = $this->validateString($data['video_link'], 'video_link', 191) ?? '';

        // Use product_title for name if available, otherwise use model or product_code
        $productName = $data['product_code'] ?? '';
        $this->product_content->name = $this->validateString($productName, 'name', 191) ?? '';
        $productTitle = $data['web_product']??'';
        $this->product_content->title = $this->validateString($productTitle, 'title', 191) ?? '';
        $productTagLine = $data['tag_line']??'';
        $this->product_content->tag_line = $this->validateString($productTagLine, 'tag_line', 500) ?? '';

        // Generate slug from name
        $this->product_content->slug = $this->product->product_code; // $this->generateSlugFromName($productName, 'slug') ?? '';
        if($this->product->product_code != $this->product_content->slug && !empty($this->product->product_code) && !empty($this->product_content->slug)){
            $this->addError("Slug", "Slug is not equal to product code");
        }
        // Use feature_description for content
        if (isset($data['feature_description'])) $this->product_content->content = $this->validateText($data['feature_description'], 'content') ?? '';

         // Meta fields - use product_title or fallback to name
         $this->product_content->meta_title = $this->validateString($data['meta_title'] ?? $productName, 'meta_title', 191) ?? '';
         $this->product_content->meta_description = $this->validateString($data['meta_description'] ?? ($data['feature_description'] ?? ''), 'meta_description', 191) ?? '';
         $this->product_content->meta_keywords = $this->validateString($data['meta_keywords'] ?? $productName, 'meta_keywords', 191) ?? '';
        // JSON fields
        // -----------------------------
        // JSON fields
        // Converts string values to JSON, defaults if missing
        // -----------------------------
        if (isset($data['image'])) {
            $this->product->image = $this->validateJson($data['image'], 'image', 'image') ?? null;
            if(isset($this->product->image) && !empty($this->product->image)){
                $image = json_decode($this->product->image, true);
                $image = $image[0]??[];
                if(isset($image) && !empty($image['objectURL']) && isset($image['file'])){
                    $this->media = new stdClass();
                    $this->media->file = json_encode([
                        'name' => $image['name'],
                        'size' => $image['size'],
                        'type' => $image['type'],
                        'objectURL' => $image['objectURL'],
                        'tmp_name' => $image['file']['tmp_name'],
                        'full_path' => $image['file']['full_path'],
                    ]);
                    $this->media->path = $image['objectURL'];
                    $this->media->name = $image['name'];
                    $this->media->meta = $this->product->product_code;
                }
            }
        }
        if (isset($data['specifications_image'])) $this->product->specifications_image = $this->validateJson($data['specifications_image'], 'specifications_image', self::SPECIFICATIONS_IMAGE_PATH) ?? null;
        if (isset($data['banner_image'])) $this->product->banner_image = $this->validateJson($data['banner_image'], 'banner_image', self::BANNER_IMAGE_PATH) ?? null;
        if (isset($data['image_thumb'])) $this->product->image_thumb = $this->validateJson($data['image_thumb'], 'image_thumb', self::IMAGE_THUMB_PATH) ?? null;
        if (isset($data['main_image_one'])) $this->product->main_image_one = $this->validateJson($data['main_image_one'], 'main_image_one', self::MAIN_IMAGE_ONE_PATH) ?? null;
        if (isset($data['main_image_two'])) $this->product->main_image_two = $this->validateJson($data['main_image_two'], 'main_image_two', self::MAIN_IMAGE_TWO_PATH) ?? null;
        if (isset($data['feature_image_one'])) $this->product->feature_image_one = $this->validateJson($data['feature_image_one'], 'feature_image_one', self::FEATURE_IMAGE_ONE_PATH) ?? null;
        if (isset($data['feature_image_two'])) $this->product->feature_image_two = $this->validateJson($data['feature_image_two'], 'feature_image_two', self::FEATURE_IMAGE_TWO_PATH) ?? null;
        if (isset($data['feature_image_three'])) $this->product->feature_image_three = $this->validateJson($data['feature_image_three'], 'feature_image_three', self::FEATURE_IMAGE_THREE_PATH) ?? null;

        // Product content fields - populate from CSV data
        // -----------------------------
        // PRODUCT CONTENT
        // -----------------------------
        $this->product_content->language_id = $this->validateInteger($data['language_id'] ?? 1, 'language_id', 1) ?? 1;
       

        // Text fields for product (not content)
        if (isset($data['main_image_one_title'])) $this->product->main_image_one_title = $this->validateString($data['main_image_one_title'], 'main_image_one_title', 191) ?? '';
        if (isset($data['main_image_one_description'])) $this->product->main_image_one_description = $this->validateText($data['main_image_one_description'], 'main_image_one_description') ?? '';
        if (isset($data['main_image_two_title'])) $this->product->main_image_two_title = $this->validateString($data['main_image_two_title'], 'main_image_two_title', 191) ?? '';
        if (isset($data['main_image_two_description'])) $this->product->main_image_two_description = $this->validateText($data['main_image_two_description'], 'main_image_two_description') ?? '';
        if (isset($data['feature_description'])) $this->product->feature_description = $this->validateText($data['feature_description'], 'feature_description') ?? '';
        if (isset($data['feature_image_one_title'])) $this->product->feature_image_one_title = $this->validateString($data['feature_image_one_title'], 'feature_image_one_title', 191) ?? '';
        if (isset($data['feature_image_one_description'])) $this->product->feature_image_one_description = $this->validateText($data['feature_image_one_description'], 'feature_image_one_description') ?? '';
        if (isset($data['feature_image_two_title'])) $this->product->feature_image_two_title = $this->validateString($data['feature_image_two_title'], 'feature_image_two_title', 191) ?? '';
        if (isset($data['feature_image_two_description'])) $this->product->feature_image_two_description = $this->validateText($data['feature_image_two_description'], 'feature_image_two_description') ?? '';
        if (isset($data['feature_image_three_title'])) $this->product->feature_image_three_title = $this->validateString($data['feature_image_three_title'], 'feature_image_three_title', 191) ?? '';
        if (isset($data['feature_image_three_description'])) $this->product->feature_image_three_description = $this->validateText($data['feature_image_three_description'], 'feature_image_three_description') ?? '';
    }

    /**
     * Process categories and store product_id or product_code relationships
     */
    private function processCategories(array $data, array $categoryMap): void
    {
        $categories = $data['category'];
        foreach ($categories as $categoryName) {
            $categoryName = trim($categoryName);
            if (empty($categoryName)) {
                continue;
            }

            if (!empty($categoryName)) {
                // Check if category exists in mapping
                $categoryId = $categoryMap[$categoryName] ?? null;

                if ($categoryId) {
                    // old categories
                    $this->categories_data[] = [
                        'product_code' => $data['product_code'] ?? null,
                        'category_id' => $categoryId,
                    ];
                } else {
                    $msg = "Category not found: {$categoryName} (field: {$categoryName}) for product: " . ($data['product_code'] ?? 'unknown');
                    $this->addError("Category", $msg);
                }
            }
        }
    }
    /**
     * Process categories and store product_id or product_code relationships
     */
    private function processTags(array $data, array $tagsMap): void
    {
        $tags = $data['tags'];
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) {
                continue;
            }

            if (!empty($tagName)) {
                // Check if category exists in mapping
                $tagId = $tagsMap[$tagName] ?? null;

                if ($tagId) {
                    // old categories
                    $this->tags[] = [
                        'product_code' => $data['product_code'] ?? null,
                        'tag_id' => $tagId,
                    ];
                } else {
                    $msg = "Tag not found: {$tagName} (field: {$tagName}) for product: " . ($data['product_code'] ?? 'unknown');
                    $this->addError("Tag", $msg);
                }
            }
        }
    }

    private function generateSlugFromName(string $value, string $field): string
    {
        $value = $this->fixTextEncoding($value, $field);
        if (!$value) return '';
        return $this->validateSlug($value, $field) ?? '';
    }

    private function validateInteger($value, string $field, ?int $default = null, bool $isMandatory = false): ?int
    {
        $value = $this->fixTextEncoding($value, $field);
        // Nullable integer fields
        if (in_array($field, $this->nullableIntegerFields) && ($value == null || isEmpty($value))) {
            return null;
        }
        // Mandatory check
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }

        // Default value if empty
        if ($value === null || $value === '') {
            return $default;
        }
        // Numeric check
        if (!is_numeric($value)) {
            $this->addError($field, 'must be a valid integer');
            return $default;
        }
        // Positive integer check
        $int = (int)$value;
        if ($int < 0) {
            $this->addError($field, 'must be a positive integer');
            return $default;
        }
        // Required field check
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $int;
    }

    private function validateFloat($value, string $field, ?float $default = null, bool $isMandatory = false): ?float
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') {
            return $default;
        }
        if (!is_numeric($value)) {
            $this->addError($field, 'must be a valid number');
            return $default;
        }
        $float = (float)$value;
        if ($float < 0) {
            $this->addError($field, 'must be a positive number');
            return $default;
        }
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $float;
    }

    private function validateString($value, string $field, int $maxLength, bool $isMandatory = false): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }
        $s = trim($value);
        if (strlen($s) > $maxLength) {
            $s = substr($s, 0, $maxLength);
        }
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $s;
    }

    private function validateText($value, string $field, bool $isMandatory = false): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return trim($value);
    }

    private function validateSlug($value, string $field): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }
        $slug = strtolower(trim($value));
        // $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        // $slug = preg_replace('/-+/', '-', $slug);
        // spaces → -
        $slug = preg_replace('/\s+/', '-', $slug);
        // remove all special characters except -
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        // remove multiple -
        $slug = preg_replace('/-+/', '-', $slug);
        // trim - from start & end
        $slug = trim($slug, '-');
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return substr($slug, 0, 191);
    }

    private function validateJson_backup($value, string $field, $dir = null): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        $dir = $dir === null ? '' : trim($dir, '/') . '/';


        if ($value === '' || $value === null) {
            return '[]';
        }
        // ensrure string
        $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);

        if (!$this->isValidJson($value)) { // If not JSON

            // If not JSON, create a simple JSON structure for products
            // if (!str_contains($value, '/media/Products/'.$dir)) { $value = "/media/Products/{$value}"; }
            if (!str_contains($value, '/media/Products/' . $dir)) {
                $value = '/media/Products/' . $dir . $value;
            }
            $data = [['id' => null, 'file' => ['name' => basename($value), 'size' => 0, 'type' => 'image/jpeg', 'error' => 0, 'tmp_name' => $value, 'full_path' => basename($value)], 'name' => basename($value), 'size' => 0, 'type' => 'image/jpeg', 'image' => $value, 'status' => ['name' => 'Expected', 'severity' => 'info'], 'media_id' => null, 'objectURL' => $value, 'created_at' => '', 'description' => '', 'product_image_id' => null]];
            return json_encode($data) ?: '[]';
        }
        return $value;
    }

    private function validateJson($imageValue, string $field, $dir = null): ?string
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'heic', 'bmp'];
        $dir = $dir ? trim($dir, '/') . '/' : '';


        // Ensure input is a non-empty string
        if (!is_string($imageValue) || trim($imageValue) === '') {
            $this->addError($field, 'must be a string');
            return "[]";
        }

        // If already valid JSON, return as-is
        if ($this->isValidJson($imageValue)) {
            return "[]";
        }
        // check valid _ or -
        if ($this->isOnlyUnderscoreOrHyphen($imageValue)) {
            return "[]";
        }
        // Check file extension
        $extension = strtolower(pathinfo($imageValue, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            $this->addError($field, 'Extension not allow');
            return "[]";
        }

        if (!str_contains($imageValue, $dir)) {
            if(str_contains($dir.$imageValue, 'media/Products/')){
                $imageValue = '/'.$dir.$imageValue;
            } else {
                $imageValue = '/media/Products/' . $dir . $imageValue;
            }
        }

        // $imageValue = str_contains($imageValue, '/media/Products/' . $dir . '/') ? $imageValue : "/media/Products/{$dir}/{$imageValue}";
        // //Check if the image is exists in the $imageValue path 
        $imgPath = str_replace('//', '/', ROOT_DIR . PUBLIC_PATH . $imageValue);
        // //Retrive image file object
        $imageFile = new \SplFileInfo($imgPath);
        $imageServer = env('APP_URL');
        $imageServerPath = $imageValue;

        // Check if image file exists
        if ($imageFile->isFile()) {
            $imageData = [
                [
                    'id' => null,
                    'file' => [
                        'name' => $imageFile->getFilename(),
                        'size' => $imageFile->getSize(),
                        'type' => 'image/jpeg',
                        'error' => 0,
                        'tmp_name' => $imgPath,
                        'full_path' => $imageFile->getFilename()
                    ],
                    'name' => $imageFile->getFilename(),
                    'size' => $imageFile->getSize(),
                    'type' => 'image/jpeg',
                    'image' => $imageValue,
                    'status' => ['name' => 'Uploaded', 'severity' => 'success'],
                    'media_id' => null,
                    'objectURL' => $imageServerPath,
                    'created_at' => '',
                    'description' => '',
                    'post_image_id' => null,
                    'project_image_id' => null
                ]
            ];
        } else {
            // Even if file doesn't exist, store the expected path for future use
            $imageData = [
                [
                    'id' => null,
                    'file' => [
                        'name' => basename($imageValue),
                        'size' => 0,
                        'type' => 'image/jpeg',
                        'error' => 0,
                        'tmp_name' => $imageValue,
                        'full_path' => basename($imageValue)
                    ],
                    'name' => basename($imageValue),
                    'size' => 0,
                    'type' => 'image/jpeg',
                    'image' => $imageValue,
                    'status' => ['name' => 'Expected', 'severity' => 'info'],
                    'media_id' => null,
                    'objectURL' => $imageServerPath,
                    'created_at' => '',
                    'description' => '',
                    'post_image_id' => null,
                    'project_image_id' => null
                ]
            ];
        }

        $jsonResult = json_encode($imageData);
        if ($jsonResult === false) {
            // Fallback if json_encode fails
            return '[]';
        }
        return $jsonResult;
    }

    private function isOnlyUnderscoreOrHyphen($value)
    {
        // ^ start, $ end, [_-]+ one or more _ or -
        return preg_match('/^[_-]+$/', $value) === 1;
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }



    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
        $this->isValidData = false;
    }

    public function validate(): bool|self
    {
        // If the data has already been marked as invalid, return false
        if (!$this->isValidData) {
            return false;
        }
        // If data is valid, return the current object instance for chaining
        return $this;
    }

    public function getErrors(): array
    {
        // Return the array of validation errors collected so far
        return $this->errors;
    }

    public function toArray(): array
    {
        return [
            'product' => (array)$this->product, // Convert the product object to an array and include it in the result
            'product_content' => (array)$this->product_content, // Convert the product content object to an array and include it in the result
            'categories' => $this->categories, // (optional) Include the categories array as-is
            'categories_data' => $this->categories_data, // Include the processed category relationships array as-is
            'tags_data' => $this->tags // Include the processed tags relationships array as-is
        ];
    }

    public function getUniqueIdentifier(): string
    {
        // If product_code exists, use it as the unique identifier
        if (!empty($this->product->product_code)) {
            return 'product_code_' . $this->product->product_code;
        }

        // If product_id exists (and product_code was missing), use it as the unique identifier
        if (!empty($this->product->product_id)) {
            return 'product_id_' . $this->product->product_id;
        }

        // If SKU exists (and both product_code and product_id were missing), use it as the unique identifier
        if (!empty($this->product->sku)) {
            return 'sku_' . $this->product->sku;
        }

        // If none of the above exist, generate a unique identifier using uniqid()
        return 'unique_' . uniqid();
    }


    private function fixTextEncoding(string|int|float|null $value, string $field): string|int|float|null
    {
        $textFields = [
            'model',
            'description',
            'specifications',
            'warranty_period',
            'product_code',
            'factory_code',
            'sku',
            'isbn',
            'barcode',
            'material',
            'out_of_stock_status',
            'size',
            'date_available',
            'template',
            'video_link',
            'name',
            'slug',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'main_image_one_title',
            'main_image_one_description',
            'main_image_two_title',
            'main_image_two_description',
            'feature_description',
            'feature_image_one_title',
            'feature_image_one_description',
            'feature_image_two_title',
            'feature_image_two_description',
            'feature_image_three_title',
            'feature_image_three_description'
        ];

        if (in_array($field, $textFields)) {
            if (isset($value) && is_string($value) && $value !== '') {
                if (mb_check_encoding($value, 'UTF-8')) {
                    $replacements = [
                        "\x92" => "'",
                        "\x93" => '"',
                        "\x94" => '"',
                        "\x96" => "–",
                        "\x97" => "—",
                        "\x85" => "…",
                        "\x91" => "'",
                        "\x82" => ",",
                        "\x84" => "„",
                        "\x8B" => "‹",
                        "\x9B" => "›",
                    ];
                    $value = strtr($value, $replacements);
                }
            }
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
        return $value;
    }
}
