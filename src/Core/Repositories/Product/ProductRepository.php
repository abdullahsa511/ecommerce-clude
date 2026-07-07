<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Design\DesignResource;
use PDO;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductContent;
use App\Core\Models\Product\ProductRelated;
use App\Core\Models\Product\ProductVariant;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Product\ProductData;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\PostCategory\TaxonomyItemContent;
use App\Core\Models\Product\ProductToDigitalAsset;
use App\Core\Models\Product\ProductToTaxonomyItem;
use App\Core\Models\User\DigitalAsset;
use App\Core\Models\Product\Manufacturer;
use App\Core\Models\Product\Vendor;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Attribute\Attribute;
use App\Core\Models\Attribute\AttributeContent;
use App\Core\Models\Design\DesignResourceDocument;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Media\Media;
use App\Core\Models\Product\ProductAttribute;
use App\Core\Models\Option\Option;
use App\Core\Models\Product\ProductImage;
use App\Core\Models\Product\ProductMeta;
use App\Core\Models\Product\ProductOption;
use App\Core\Models\Product\ProductPromotion;
use App\Core\Models\Design\ResourceImageData;
use App\Core\Models\Item\Item;
use App\Core\Models\Product\ProductCertificate;
use App\Core\Models\Product\ProductResource;
use App\Core\Models\Type\Type;
// use DateTime;
use Illuminate\Database\Eloquent\Builder;
use League\Csv\Reader;
use Exception;
use App\Core\Validation\ProductDataValidation;
use App\Core\Validation\ProductCertificateDataValidation;
use App\Core\Validation\ProductRelatedProjectDataValidation;
use App\Core\Models\Product\ProductRelatedProject;
use App\Core\Models\Project\Project;
use App\Core\Repositories\Design\DesignResourceRepository;

use function App\Core\System\utils\app;
use function App\Core\System\utils\env;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    private ProductContent $productContent;
    private ProductRelated $productRelated;
    private ProductVariant $productVariant;
    private Item $item;
    private ProductOptionGroup $productOptionGroup;
    private TaxonomyItem $taxonomyItem;
    private DigitalAsset $digitalAsset;
    private ProductToTaxonomyItem $productToTaxonomyItem;
    private ProductToDigitalAsset $productToDigitalAsset;
    private Manufacturer $manufacturer;
    private Vendor $vendor;
    private Attribute $attribute;
    private AttributeContent $attributeContent;
    private Language $language;
    private Type $type;
    private ProductAttribute $productAttribute;
    private Option $option;
    private ProductOption $productOption;
    private ProductPromotion $productPromotion;
    private ProductImage $productImage;
    private ProductMeta $productMetadata;
    private TaxonomyItemContent $taxonomyItemContent;
    private Media $media;
    private DesignResourceDocument $designResourceDocument;
    private DesignResource $designResource;
    private ProductCertificate $productCertificate;
    private ProductRelatedProject $productRelatedProject;
    private ProductResource $productResource;
    private Project $project;
    private DesignResourceRepository $designResourceRepository;

    public function __construct(
        PDO $db,
        ProductContent $productContent,
        ProductRelated $productRelated,
        ProductVariant $productVariant,
        Item $item,
        ProductOptionGroup $productOptionGroup,
        TaxonomyItem $taxonomyItem,
        DigitalAsset $digitalAsset,
        ProductToTaxonomyItem $productToTaxonomyItem,
        ProductToDigitalAsset $productToDigitalAsset,
        Manufacturer $manufacturer,
        Vendor $vendor,
        Attribute $attribute,
        AttributeContent $attributeContent,
        Language $language,
        Type $type,
        ProductAttribute $productAttribute,
        Option $option,
        ProductOption $productOption,
        ProductPromotion $productPromotion,
        ProductImage $productImage,
        ProductMeta $productMetadata,
        TaxonomyItemContent $taxonomyItemContent,
        Media $media,
        DesignResourceDocument $designResourceDocument,
        DesignResource $designResource,
        ProductCertificate $productCertificate,
        ProductRelatedProject $productRelatedProject,
        ProductResource $productResource,
        Project $project,
        DesignResourceRepository $designResourceRepository
    ) {
        parent::__construct($db, 'product', Product::class);
        $this->productContent = $productContent;
        $this->productContent->setDb($db);
        $this->productRelated = $productRelated;
        $this->productRelated->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->item = $item;
        $this->item->setDb($db);
        $this->productOptionGroup = $productOptionGroup;
        $this->productOptionGroup->setDb($db);
        $this->taxonomyItem = $taxonomyItem;
        $this->taxonomyItem->setDb($db);
        $this->taxonomyItemContent = $taxonomyItemContent;
        $this->taxonomyItemContent->setDb($db);
        $this->digitalAsset = $digitalAsset;
        $this->digitalAsset->setDb($db);
        $this->productToTaxonomyItem = $productToTaxonomyItem;
        $this->productToTaxonomyItem->setDb($db);
        $this->productToDigitalAsset = $productToDigitalAsset;
        $this->productToDigitalAsset->setDb($db);
        $this->manufacturer = $manufacturer;
        $this->manufacturer->setDb($db);
        $this->vendor = $vendor;
        $this->vendor->setDb($db);
        $this->attribute = $attribute;
        $this->attribute->setDb($db);
        $this->attributeContent = $attributeContent;
        $this->attributeContent->setDb($db);
        $this->language = $language;
        $this->language->setDb($db);
        $this->type = $type;
        $this->type->setDb($db);
        $this->productAttribute = $productAttribute;
        $this->productAttribute->setDb($db);
        $this->option = $option;
        $this->option->setDb($db);
        $this->productOption = $productOption;
        $this->productOption->setDb($db);
        $this->productPromotion = $productPromotion;
        $this->productPromotion->setDb($db);
        $this->productImage = $productImage;
        $this->productImage->setDb($db);
        $this->productMetadata = $productMetadata;
        $this->productMetadata->setDb($db);
        $this->media = $media;
        $this->media->setDb($db);
        $this->designResourceDocument = $designResourceDocument;
        $this->designResourceDocument->setDb($db);
        $this->designResource = $designResource;
        $this->designResource->setDb($db);
        $this->productCertificate = $productCertificate;
        $this->productCertificate->setDb($db);
        $this->productRelatedProject = $productRelatedProject;
        $this->productRelatedProject->setDb($db);
        $this->productResource = $productResource;
        $this->productResource->setDb($db);
        $this->project = $project;
        $this->project->setDb($db);
        $this->designResourceRepository = $designResourceRepository;

    }

    public function get(
        ?int $productId = null,
        ?string $slug = null,
        ?int $languageId = null,
        bool $includePromotion = false,
        bool $includePoints = false,
        bool $includeStockStatus = false,
        bool $includeWeightType = false,
        bool $includeLengthType = false,
        bool $includeRating = false,
        bool $includeReviews = false
    ): array
    {
        return [];
    }

    public function getProductById(int $productId, int $languageId = 1): Product|null
    {
        $product = $this->model
            ->with([
                'content' => function ($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                },
                'digitalAssets',
                'categories',
                'manufacturer',
                'vendor'
            ])
            ->find($productId);
        $product->data->relatedProducts = $this->getProductRelated($productId);
        $product->data->relatedProjects = $this->getProjectRelated($productId);
        $product->data->relatedResources = $this->getProductResource($productId);
        $product->data->familyProducts = [];
        if($product->product_family_code){
            $product->data->familyProducts = $this->getFamilyProducts($productId, $product->product_family_code);
        }
        $product->data->categories = $this->getCategories($productId);
        $product->data->tags = $this->getProductTags($productId);
        $product->data->certificates = $this->getProductCertificates($productId);
        $product->data->resources = $this->getProductResources($productId);
        $product->data->images = $this->getProductImages($productId);
        $product->data->options = $this->getProductOptions($productId);
        $product->data->attributes = $this->getProductAttributes($productId);
        $product->data->digitalAssets = $this->getProductDigitalAssets($productId, $languageId);
        $variants = $this->getVariantsByProductId($productId);
        $product->data->metadata = $this->getProductMetadata($productId);
        $product->data->productVariants = $variants;
        return $product;
    }

    private function getProductCertificates(int $productId): array
    {
        $certificates = $this->productCertificate
            ->where('product_id', '=', $productId)
            ->findAll(false);

        $result = [];

        foreach ($certificates as $certificate) {

            $files = json_decode($certificate['certificate_file'] ?? '[]', true);

            if (!is_array($files)) {
                continue;
            }

            foreach ($files as $file) {

                $result[] = [
                    'project_id'             => null,
                    'post_id'                => null,
                    'product_certificate_id' => $certificate['product_certificate_id'] ?? null,
                    'product_id'             => $certificate['product_id'] ?? null,
                    'product_image_id'       => $file['product_image_id'] ?? null,
                    'image'                  => $file['objectURL'] ?? '',
                    'name'                   => $file['name'] ?? '',
                    'path'                   => $file['objectURL'] ?? '',
                    'description'            => $file['description'] ?? '',
                    'size'                   => $file['size'] ?? 0,
                    'type'                   => $file['type'] ?? '',
                    'objectURL'              => $file['objectURL'] ?? '',
                    'status'                 => $file['status'] ?? [
                        'name' => 'Uploaded',
                        'severity' => 'success'
                    ],
                    'sort_order'             => $certificate['sort_order'] ?? 0,
                    'created_at'             => $certificate['created_at'] ?? null,
                ];
            }
        }

        return $result;
    }

    private function getProductResources(int $productId): array
    {
        // SELECT design_resource_document.*, product_resource.product_id FROM `product_resource`
        // JOIN design_resource ON design_resource.design_resource_id = product_resource.design_resource_id
        // JOIN design_resource_document ON product_resource.design_resource_id = design_resource_document.design_resource_id
        // WHERE product_resource.product_id  = 321

        $resources = $this->productResource
        ->clearQuery()
        ->join(
            'design_resource',
            'design_resource.design_resource_id',
            '=',
            'product_resource.design_resource_id'
        )
        ->join(
            'design_resource_document',
            'design_resource_document.design_resource_id',
            '=',
            'product_resource.design_resource_id'
        )
        ->where('product_resource.product_id', '=', $productId)
        ->where('design_resource_document.design_resource_document_id', '!=', '')
        ->select([
            'design_resource_document.design_resource_document_id',
            'design_resource_document.design_resource_id',
            'design_resource_document.media_id',
            'design_resource_document.name',
            'design_resource_document.url',
            'design_resource_document.description',
            'design_resource_document.format',
            'design_resource_document.created_at',
            'design_resource_document.updated_at',
            'product_resource.product_id'])
        ->findAll(false);

        $result = [];

        foreach ($resources as $resource) {

            $result[] = [
                'project_id'                  => null,
                'post_id'                     => null,
                'product_id'                  => $resource['product_id'] ?? null,
                'design_resource_id'          => $resource['design_resource_id'] ?? null,
                'design_resource_document_id' => $resource['design_resource_document_id'] ?? null,
                'media_id'                    => $resource['media_id'] ?? null,

                'image'                       => $resource['url'] ?? '',
                'path'                        => $resource['url'] ?? '',
                'objectURL'                   => $resource['url'] ?? '',

                'name'                        => $resource['name'] ?? '',
                'description'                 => $resource['description'] ?? '',
                'type'                        => $resource['format'] ?? '',
                'format'                      => $resource['format'] ?? '',
                'size'                        => 0,

                'status' => [
                    'name'     => 'Uploaded',
                    'severity' => 'success',
                ],

                'created_at'                  => $resource['created_at'] ?? null,
                'updated_at'                  => $resource['updated_at'] ?? null,
            ];
        }

        return $result;
    }
    public function getBySlug(string $slug): ?Product
    {
        $query = $this->model
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_content.slug', '=', $slug)
            ->select([
                'product.*',
                'product_content.title as product_title',
                'product_content.meta_title as meta_title',
                'product_content.meta_description as meta_description',
                'product_content.meta_keywords as meta_keywords',
            ]);
        $result = $query->first();
        // return $this->prepareProduct($result);
        return $result;
    }

    public function getByProductCode(string $productCode): ?Product
    {
        $query = $this->model
            ->where('product_code', '=', $productCode);
        $product = $query->first();
        // $result = $this->prepareProduct((array) $product);
        return $product;
    }

    public function getProductDimension(string $productCode): ?array
    {
        $query = $this->model
            ->where('product_code', '=', $productCode);
        $product = $query->first();
        if(!$product) return []; 
        
        return (array) $product->data;
    }

    public function getProductByCodeWithDefaultConfiguration(string $productCode): ?Product
    {
        $query = $this->model
            ->where('product_code', '=', $productCode);
        $product = $query->first();
        if(!$product) return null; 
        $defaultVariant = $this->productVariant->where('product_id', '=', $product->product_id)->where('is_default', '=', 1)->first();
        if(!$defaultVariant) {
            $this->productVariant->clearQuery();
            $defaultVariant = $this->productVariant->where('product_id', '=', $product->product_id)->first();
        }
        if($defaultVariant) $defaultVariant = (array) $defaultVariant->data;
        $product->data->defaultVariant = $defaultVariant;
        $defaultItem = [];
        $defaultItem = $this->item->where('item.product_id', '=', $product->product_id)->where('is_default', '=', 1);
        if($defaultVariant) $defaultItem->where('item.product_variant_id', '=', $defaultVariant['product_variant_id']);

        $defaultItem = $defaultItem->first();
        if(!$defaultItem && $defaultVariant) {
            $this->item->clearQuery();
            $defaultItem = $this->item->where('item.product_id', '=', $product->product_id)
            ->where('item.product_variant_id', '=', $defaultVariant['product_variant_id'])
            ->first();
        }
        if(!$defaultItem) {
            $this->item->clearQuery();
            $defaultItem = $this->item
            ->where('item.product_id', '=', $product->product_id)
            ->where('is_default', '=', 1)
            ->first();
        }
        if(!$defaultItem) {
            $this->item->clearQuery();
            $defaultItem = $this->item
            ->where('item.product_id', '=', $product->product_id)
            ->whereNotNull('dimensions_image')
            ->first();
        }
        if(!$defaultItem) {
            $this->item->clearQuery();
            $defaultItem = $this->item
            ->where('item.product_id', '=', $product->product_id)
            ->first();
        }
        if($defaultItem) $defaultItem = (array) $defaultItem->data;
        $product->data->defaultItem = $defaultItem;
        return $product;
    }

    public function productList(): array
    {
        $this->model->clearQuery();
        $query = $this->getProductQuery();
        $query->limit(0);
        $results = $query->findAll(false);
        return $results;

        // $result = $this->model
        //     ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        //     ->select(['product.product_id', 'product_content.name', 'product.product_code'])
        //     ->limit(0)
        //     ->findAll(false);
        // return $result;
    }

    public function getAll(array $filters = []): array
    {
        return [];
    }

    public function getOptions(): array
    {
        $options = $this->option
            ->select([
                '`option`.option_id',
                'option_content.name',
                'JSON_OBJECT("type_id", `option`.type_id, "type", `option`.type) as type'
            ])
            ->join('option_content', 'option_content.option_id', '=', '`option`.option_id')
            ->findAll();

        return $options;
    }

    // Product Category
    public function getProductsByParentCategoryId(int $categoryId): array
    {
        $this->model->clearQuery();
        $productQuery = $this->getProductQuery();
        $productQuery->where('category_ti.parent_id', '=', $categoryId);
        $productQuery->orderBy('category_ti.sort_order', 'DESC');
        $productQuery->groupBy('category_pti.product_id');
        $productQuery->groupBy('category_pti.taxonomy_item_id');
        // Never used prepareProducts function here

 
        $products = $productQuery->findAll();
        return $products;
    }
    public function getProductsByCategoryId(int $categoryId): array
    {
        $this->model->clearQuery();
        $productQuery = $this->getProductQuery();
        $productQuery->where('category_ti.taxonomy_item_id', '=', $categoryId);
        $productQuery->orderBy('category_ti.sort_order', 'DESC');
        $productQuery->groupBy('category_pti.product_id');
        $productQuery->groupBy('category_pti.taxonomy_item_id');
        // var_dump($productQuery->getQuery());
        $products = $productQuery->findAll();

        // Never used prepareProducts function here
        return $products;
    }
    public function getProductsByShowroomSectionProductIds(array $productIds): array
    {
        $this->model->clearQuery();
        $productQuery = $this->getProductQuery();
        $productQuery->whereIn('product.product_id', $productIds);
        // var_dump($productQuery->getQuery());
        $products = $productQuery->findAll();
        $processedProducts = $this->prepareProducts($products);
        return $processedProducts;
    }

    public function getProductsByCategorySlug(string $categorySlug, array $params): ?array
    {
        // Get language and site parameters
        $languageId = $params['language_id'] ?? 1;

        $category = $this->taxonomyItemContent->where('slug', '=', $categorySlug)->first();
        if(!$category){
            throw new \Exception('Category not found');
        }
 
        $query = $this->getProductQuery($categorySlug);
        $query->where('product.status', '=', 1);
            $query->limit(0);
            $countQuery = clone $query;
            $totalCount = $countQuery->countAll();

            // item count
            // if(isset($params['item_count']) && $params['item_count'] > 0){
            //     $query->limit($params['item_count'] * 1);
            // }
            // pagination
            if (
                isset($params['per_page']) &&
                isset($params['current_page']) &&
                $params['current_page'] > 0
            ) {
                $offset = $params['offset'] ?? ($params['current_page'] - 1) * $params['per_page'];
                $offset = max(0, (int)$offset);
                $limit = (int)$params['per_page'];

                $query->offset($offset);
                $query->limit($limit);
            }

        $products = $query->findAll();
        $processedProducts = $this->prepareProducts($products);
        

        $results = [];
        $results['section_title'] = $category->name;
        $results['section_subtitle'] = 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.';
        $results['filters'] = [
            ['name' => 'Material', 'options' => ['Material 1', 'Material 2', 'Material 3']],
            ['name' => 'Features', 'options' => ['Feature 1', 'Feature 2', 'Feature 3']],
            ['name' => 'Weight', 'options' => ['Light', 'Medium', 'Heavy']],
            ['name' => 'Certifications', 'options' => ['AFRDI Certified', 'OBP Certified']],
        ];
        $results['active_tags'] = [
            'Tag Name Here',
            'Tag Name Here',
        ];
        $results['product_count'] = count($processedProducts);
        $results['total_count'] = $totalCount;
        $results['current_page'] = $params['current_page'] ?? 1;
        $results['per_page'] = $params['per_page'] ?? 40;
        $results['offset'] = $params['offset'] ?? 0;
        $results['load_more'] = $totalCount > ($results['per_page'] * $results['current_page']);
        $results['items'] = $processedProducts;

        return $results;
    }

    public function getCategories($product_id = false): array
    {
        $categories = $this->taxonomyItem
            ->select([
                'taxonomy_item.taxonomy_item_id as id',
                'taxonomy_item.taxonomy_item_id',
                'taxonomy_item.parent_id',
                'taxonomy_item.banner_way_points',
                'taxonomy_item.image',
                'taxonomy_item.slider_image',
                'taxonomy_item.label_name',
                'taxonomy_item_content.name',
                'taxonomy_item_content.slug',
                'taxonomy_item_content.products_link',
                'taxonomy_item_content.link',
                'taxonomy_item_content.content',
                'taxonomy_item_content.meta_title',
                'taxonomy_item_content.meta_description',
                'taxonomy_item_content.meta_keywords',
                'taxonomy_item.sort_order',
                'taxonomy_item.status',
            ])
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->join('taxonomy_content', 'taxonomy_content.taxonomy_id', '=', 'taxonomy.taxonomy_id')
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('taxonomy.type', '=', 'categories')
            ->where('taxonomy.site_id', '=', 1)
            ->where('taxonomy_content.language_id', '=', 1);
        $categories->orderBy('taxonomy_item.sort_order', 'ASC');
        if ($product_id) {
            $categories->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
                ->where('product_to_taxonomy_item.product_id', '=', $product_id)
                ->select(['product_to_taxonomy_item.sort_order as product_sort_order']);
            return $categories->findAll();
        }
        $categories = $categories->findAll();

        return $this->buildCategoryTree($categories);
    }

    // Product Creation / Update
    public function createProduct(ProductData $productData): Product
    {
        $productDataArray = $productData->toArray();
        $product = $this->model->create($productDataArray);
        $content = $productData->getProductContent();
        if ($content) {
            $content['product_id'] = $product->product_id;
            $this->productContent->insert([$content]);
            $product->content = $content;
        }
        $manufacturer = $productData->getManufacturer();
        if ($manufacturer) {
            // $manufacturer['product_id'] = $product->product_id;
            $this->manufacturer->insert([$manufacturer]);
            $product->manufacturer = $manufacturer;
        }
        $vendor = $productData->getVendor();
        if ($vendor) {
            // $vendor['product_id'] = $product->product_id;
            $this->vendor->insert([$vendor]);
            $product->vendor = $vendor;
        }
        $product_id = $product->data->product_id;
        $categories = $productData->getCategories($product_id);
        if (count($categories) > 0) {
            $this->productToTaxonomyItem->upsert($categories, ['product_id', 'taxonomy_item_id']);
        }
        $relatedProducts = $productData->getRelatedProducts($product_id);
        if (count($relatedProducts) > 0) {
            $this->productRelated->upsert($relatedProducts, ['product_id', 'product_related_id']);
        }
        $variantProducts = $productData->getVariantProducts($product_id);
        if (count($variantProducts) > 0) {
            $this->productVariant->upsert($variantProducts, ['product_id', 'product_variant_id']);
        }
        $digitalAssets = $productData->getDigitalAssets($product_id);
        if (count($digitalAssets) > 0) {
            $this->productToDigitalAsset->upsert($digitalAssets, ['product_id', 'digital_asset_id']);
        }
        // ========================= Abdullah (15-06-2026) =============================
        // Temporarily disabled to avoid the current error.
        // Review and re-enable after the related issue is resolved.

        // $attributes = $productData->getAttributes($product_id);
        // if (count($attributes) > 0) {
        //     $this->attribute->upsert($attributes, ['name', 'attribute_group_id']);
        //     $attributeNames = array_column($attributes, 'name');
        //     $attributeGroupId = $attributes[0]['attribute_group_id'];
        //     $attributeData = $this->attribute->whereIn('name', $attributeNames)->where('attribute_group_id', '=', $attributeGroupId)->select(['attribute_id', 'value'])->findAll();
        //     $productAttributes = [];
        //     foreach ($attributeData as $attribute) {
        //         $productAttributeData = [
        //             'product_id' => $product_id,
        //             'attribute_id' => $attribute['attribute_id'],
        //             'language_id' => 1,
        //             'value' => $attribute['value']
        //         ];
        //         $productAttributes[] = $productAttributeData;
        //     }
        //     $this->productAttribute->upsert($productAttributes, ['product_id', 'attribute_id']);
        // }
        // ========================= Abdullah comment (15-06-2026) =============================

        $options = $productData->getProductOptions($product_id);
        // remember one thing that type and value should be json format
        if (count($options) > 0) {
            // Extract type_id from type JSON for each option
            // foreach($options as &$option) {
            //     if(isset($option['type'])) {
            //         $typeData = json_decode($option['type'], true);
            //         if(is_array($typeData) && isset($typeData['type_id'])) {
            //             $option['type_id'] = $typeData['type_id'];
            //         }
            //     }
            // }
            $this->productOption->upsert($options, ['product_id', 'option_id']);
        }

        $promotions = $productData->getProductPromotions($product_id);
        if (count($promotions) > 0) {
            $this->productPromotion->upsert($promotions, ['product_id', 'user_group_id']);
        }
        $images = $productData->getProductImages($product_id);
        if (count($images) > 0) {
            $this->productImage->upsert($images, ['product_id', 'image']);
        }
        $metadata = $productData->getProductMetadata($product_id);
        if (count($metadata) > 0) {
            $this->productMetadata->upsert($metadata, ['product_id', 'namespace', 'key']);
        }

        return $product;
    }

    public function updateProduct(ProductData $productData): Product
    {
        $product = $this->model->find($productData->product_id);
        $familyCode = $product->product_family_code ?? $product->product_code;
   
        if (!$product) {
            throw new \Exception('Product not found');
        }
        $productDataArray = $productData->toArray();
        // if (isset($productDataArray['image']) && is_array($productDataArray['image'])) {
        //     $productDataArray['image'] = json_encode($productDataArray['image']);
        // }
        $imageFields = ['image', 'banner_image', 'main_image_one', 'main_image_two', 'feature_image_one', 'feature_image_two', 'feature_image_three']; // add this for dynamic all images
        // All image-related fields that need JSON encoding
        foreach ($imageFields as $field) {
            if (isset($productDataArray[$field]) && is_array($productDataArray[$field])) {
                $productDataArray[$field] = json_encode($productDataArray[$field]);
            }
        }
        $product = $product->update($productDataArray);
        $content = $productData->getProductContent();
        if ($content) {
            $content['product_id'] = $product->product_id;
            $this->productContent->upsert([$content], ['product_id', 'language_id']);
            $product->content = $content;
        }

        $product_id = $product->data->product_id;
        $options = $productData->getProductOptions($product_id);
        // remember one thing that type and value should be json format
        if (count($options) > 0) {
            // testing .. abdullah
            if (isset($options[0]) && is_array($options[0])) {
                foreach ($options as &$option) {
                    $option['type_id'] = 4;
                }
                unset($option);
            } else {
                // If it's a single associative array
                $options['type_id'] = 4;
                $options = [$options]; // wrap into array for upsert
            }
            $this->productOption->upsert($options, ['product_id', 'option_id']);
            $product->options = $options;
        }
        $categories = $productData->getCategories($product_id);
        if (count($categories) > 0) {
            $this->productToTaxonomyItem->deleteWhere(['product_id' => $product_id]);
            $this->productToTaxonomyItem->upsert($categories, ['product_id', 'taxonomy_item_id']);
            $product->categories = $categories;
        }
        $tags = $productData->getTags($product_id);
        if (count($tags) > 0) {
            $this->productToTaxonomyItem->upsert($tags, ['product_id', 'taxonomy_item_id']);
            $product->tags = $tags;
        }

        
        // Map strings to formatted database row arrays
        // $certificates = $productData->getCertificates($product_id, $productData->certificates);
        // if (count($certificates) > 0) {
        //     // Upsert expects an array of rows, matching unique keys to update duplicates
        //     $this->productCertificate->upsert(
        //         $certificates, 
        //         ['product_id', 'certificate_type']);
        //     $product->certificates = $certificates;
        // }



        $relatedProducts = $productData->getRelatedProducts($product_id);
        if (count($relatedProducts) > 0) {
            $this->productRelated->upsert($relatedProducts, ['product_id', 'product_related_id']);
        }
        $familyProducts = $productData->getFamilyProducts($product_id, $familyCode);
        if (count($familyProducts) > 0) {
            $this->model->clearQuery();
            $this->model->upsert($familyProducts, ['product_id', 'product_code']);
            $product->familyProducts = $familyProducts;
        }
        $relatedProjects = $productData->getProductRelatedProjects($product_id);
        if (count($relatedProjects) > 0) {
            $this->productRelatedProject->clearQuery();
            $this->productRelatedProject->upsert($relatedProjects, ['project_id', 'product_id']);
            $product->relatedProjects = $relatedProjects;
        }
        $relatedResources = $productData->getProductRelatedResources($product_id);
        if (count($relatedResources) > 0) {
            $this->productResource->clearQuery();
            $this->productResource->upsert($relatedResources, ['design_resource_id', 'product_id']);
            $product->relatedResources = $relatedResources;
        }
        $variantProducts = $productData->getVariantProducts($product_id);
        if (count($variantProducts) > 0) {
            // $this->productVariant->upsert($variantProducts, ['product_id', 'product_variant_id']);
        }
        $digitalAssets = $productData->getDigitalAssets($product_id);
        if (count($digitalAssets) > 0) {
            $this->productToDigitalAsset->upsert($digitalAssets, ['product_id', 'digital_asset_id']);
        }
        [$attributes, $attributeContents] = $productData->getAttributes($product_id);
        if (count($attributes) > 0) {
            // $this->attribute->upsert($attributes, ['name', 'attribute_group_id']);
            $this->attribute->upsert($attributes, ['attribute_code', 'attribute_group_id']);
            // $attributeNames = array_column($attributes, 'name');
            $attributeCodesIdMap = array_column($attributes, 'attribute_code', 'attribute_id'); // get attribute codes from the attributes array
            foreach ($attributeContents as $attributeContent) {
                $attributeContent['attribute_id'] = $attributeCodesIdMap[$attributeContent['attribute_id']];
            }
            $attributeCodes = array_column($attributes, 'attribute_code');
            $attributeGroupId = $attributes[0]['attribute_group_id'];
            // get attribute data by code and attribute group id // remove name from the query
            $attributeData = $this->attribute->whereIn('attribute_code', $attributeCodes)->where('attribute_group_id', '=', $attributeGroupId)->select(['attribute_id', 'attribute_code', 'value'])->findAll();
            $productAttributes = [];
            foreach ($attributeData as $attribute) {
                $productAttributeData = [
                    'product_id' => $product_id,
                    'attribute_id' => $attribute['attribute_id'],
                    'language_id' => 1,
                    'value' => $attribute['value']
                ];
                $productAttributes[] = $productAttributeData;
            }
            // upsert attribute content
            $this->attributeContent->upsert($attributeContents, ['attribute_id', 'language_id']);
            $this->productAttribute->upsert($productAttributes, ['product_id', 'attribute_id']);
            $product->attributes = $attributes;
        }
        $promotions = $productData->getProductPromotions($product_id);
        if (count($promotions) > 0) {
            $this->productPromotion->upsert($promotions, ['product_id', 'user_group_id']);
        }
        // $images = $productData->getProductImages($product_id);
        // if (count($images) > 0) {
        //     $this->productImage->upsert($images, ['product_id', 'image']);
        // }
        $metadata = $productData->getProductMetadata($product_id);
        if (count($metadata) > 0) {
            $this->productMetadata->upsert($metadata, ['product_id', 'namespace', 'key']);
        }

        return $product;
    }

    private function relatedProjects($product_id, $productRelatedProjects) : array
    {
         $this->productRelatedProject->upsert($productRelatedProjects, ['product_id', 'user_group_id']);
        return [];
    }

    public function insertProducts(array $data): bool
    {
        $products = $data['products'];
        $productContents = $data['productContents'];
        $this->db->beginTransaction();
        $this->model->insert($products);

        $skus = array_column($products, 'sku');
        $products = $this->model->whereIn('sku', $skus)->select(['product_id', 'sku'])->findAll();
        $productSkuMaps = [];
        foreach ($products as $product) {
            $productSkuMaps[$product['sku']] = $product['product_id'];
        }
        foreach ($productContents as &$productContent) {
            if (isset($productSkuMaps[$productContent['sku']])) {
                $productContent['product_id'] = $productSkuMaps[$productContent['sku']];
                unset($productContent['sku']);
            }
        }
        $this->productContent->insert($productContents);

        $this->db->commit();
        return true;
    }

    // Product Media
    public function productImage(array $images, int $productId): bool
    {
        try {
            $this->db->beginTransaction();

            // Delete existing images
            $stmt = $this->db->prepare("DELETE FROM product_image WHERE product_id = ?");
            $stmt->execute([$productId]);

            // Insert new images
            $stmt = $this->db->prepare("
                INSERT INTO product_image (image, product_id)
                VALUES (?, ?)
            ");

            foreach ($images as $image) {
                $stmt->execute([$image, $productId]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    public function insertProductImages(array $data,  int $product_id): array
    {
        // list($imageData, $imageLinks) = $this->prepareImageFormat($data, $product_id);
        $imageData = [];
        $imageLinks = [];
        foreach ($data as $image) {
            $img = [];
            $img['product_id'] = $product_id ?? null;
            $img['image_link'] = $image['image'];
            $img['media_id'] = $image['media_id'];
            $imageLinks[] = $image['image'];
            $img['image'] = json_encode([
                'name' => $image['name'],
                'objectURL' => $image['objectURL'],
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => $image['path'],
                'file' => [
                    'name' => $image['name'] ?? '',
                    'size' => $image['size'] ?? 0,
                    'type' => $image['type'] ?? '',
                    'error' => 0,
                    'tmp_name' => $image['path'] ?? '',
                    'full_path' => $image['name'] ?? '',
                ],
                'status' => $image['status']
            ]);
            $img['sort_order'] = 0;
            $img['status'] = json_encode($image['status']);
            $img['way_points'] = json_encode([]);
            $imageData[] = $img;
        }

        if (!empty($imageData)) {
            $this->db->beginTransaction();
    
            try {
                $this->productImage->insert($imageData);
                $this->db->commit();
            } catch (\Throwable $e) {
                $this->db->rollBack();
                throw $e;
            }
        }
        $uploadedImages = $this->productImage->whereIn('image_link', $imageLinks)->select(['image_link','product_image_id'])->findAll();
        $uploadedImages = array_column($uploadedImages, 'product_image_id', 'image_link');
        foreach ($data as $key => $image) {
            if (isset($uploadedImages[$image['image']])) {
                $data[$key]['product_image_id'] = $uploadedImages[$image['image']];
            }
        }
        return $data;
    }

    public function insertProductCertificates(array $certificates, int $product_id): array
    {
        $certificateData = [];
    
        foreach ($certificates as $certificate) {
    
            $certificateFile = json_encode([[
                'id' => null,
                'file' => $certificate['file'] ?? [
                    'name' => $certificate['name'] ?? '',
                    'size' => $certificate['size'] ?? 0,
                    'type' => $certificate['type'] ?? '',
                    'error' => 0,
                    'tmp_name' => $certificate['path'] ?? '',
                    'full_path' => $certificate['name'] ?? '',
                ],
                'name' => $certificate['name'] ?? '',
                'size' => $certificate['size'] ?? 0,
                'type' => $certificate['type'] ?? '',
                'image' => $certificate['image'] ?? '',
                'status' => $certificate['status'] ?? [
                    'name' => 'Uploaded',
                    'severity' => 'success'
                ],
                'media_id' => $certificate['media_id'] ?? null,
                'objectURL' => $certificate['objectURL'] ?? '',
                'created_at' => $certificate['created_at'] ?? '',
                'description' => $certificate['description'] ?? '',
                'product_image_id' => null
            ]]);

            $fileName = $certificate['name'] ?? '';
            $friendlyTitle = '';
            if (preg_match('/(AFRDI)_(Blue|Green)/i', $fileName, $matches)) {
                $friendlyTitle = strtoupper($matches[1]) . ' ' . ucfirst(strtolower($matches[2]));
            } else {
                // Remove extension
                $friendlyTitle = pathinfo($fileName, PATHINFO_FILENAME);
            
                // Replace separators with space
                $friendlyTitle = preg_replace('/[_\-\/&]+/', ' ', $friendlyTitle);
            
                // Remove extra spaces
                $friendlyTitle = preg_replace('/\s+/', ' ', trim($friendlyTitle));
            
                // Convert to title case
                $friendlyTitle = ucwords(strtolower($friendlyTitle));
            }
    
            $certificateData[] = [
                'product_id' => $product_id,
                'media_id' => $certificate['media_id'] ?? null,
                'logo' => $certificateFile,
                'certificate_file' => $certificateFile,
                'certificate_provider' => $certificate['certificate_provider'] ?? null,
                'certificate_type' => $certificate['type'] ?? null,
                'file_format' => $certificate['file_format'] ?? null,
                'title' => $friendlyTitle,
                'description' => $certificate['description'] ?? null,
                'sort_order' => $certificate['sort_order'] ?? 0,
                'created_at' => date('Y-m-d')
            ];
        }
    
        if (!empty($certificateData)) {
    
            $this->db->beginTransaction();
    
            try {
    
                $this->productCertificate->upsert(
                    $certificateData,
                    ['product_id', 'title']
                );
    
                $this->db->commit();
    
            } catch (\Throwable $e) {
    
                $this->db->rollBack();
                throw $e;
            }
        }
    
        return $certificateData;
    }

    public function insertProductResources(array $resources, int $product_id): array
    {
        $this->db->beginTransaction();

        try {

            $product = $this->productContent
                ->where('product_id', '=', $product_id)
                ->first();

            $productTitle = $product->data->title ?? '';

            /**
             * Create / Update Design Resource
             */
            $designResource = [[
                'title'         => $productTitle,
                'description'   => 'product resource',
                'resource_type' => 'models',
                'media_id'      => null,
            ]];
            
            $this->designResource->upsert(
                $designResource,
                ['title', 'resource_type']
            );

            $designResourceData = $this->designResource
                ->clearQuery()
                ->where('title', '=', $productTitle)
                ->where('resource_type', '=', 'models')
                ->first();

            $designResourceId = $designResourceData->data->design_resource_id;

            /**
             * Documents
             */
            $documents = [];
            $urls = [];

            foreach ($resources as $item) {
                $format = $this->detectResourceFormat(
                    $item['name'] ?? '',
                    $item['type'] ?? null
                );

                $documents[] = [
                    'design_resource_id' => $designResourceId,
                    'media_id'           => $item['media_id'] ?? null,
                    'name'               => $item['name'] ?? null,
                    'url'                => $item['objectURL'] ?? $item['url'] ?? null,
                    'description'        => $item['description'] ?? null,
                    'format'             => $format ?? null,
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];
                $urls[] = $item['objectURL'] ?? $item['url'] ?? null;
            }

            if (!empty($documents)) {

                $this->designResourceDocument->upsert(
                    $documents,
                    ['design_resource_id', 'url']
                );
            }

            /**
             * Product Resource Mapping
             */
            $productResource = [[
                'product_id'         => $product_id,
                'design_resource_id' => $designResourceId,
                'resource_type'      => 'models',
                'sort_order'         => 0,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]];

            $this->productResource->upsert(
                $productResource,
                ['product_id', 'design_resource_id']
            );

            $this->db->commit();
            $certificate =  $this->certificateData($product_id, $urls);

            return ['files' => $certificate];

        } catch (\Throwable $e) {

            $this->db->rollBack();

            throw $e;
        }
    }

    private function detectResourceFormat(string $fileName, ?string $fileType = null): ?string
    {
        $fileName = strtoupper($fileName);

        // Detect format from filename
        if (preg_match('/(?:^|[_\-. ])(SKP|GSM|DWG|RFA)(?:[_\-. ]|$)/i', $fileName, $matches)) {
            return strtoupper($matches[1]);
        }

        // Detect by MIME type
        return match ($fileType) {
            'application/x-zip-compressed',
            'application/zip' => 'ZIP',
            'application/pdf' => 'PDF',
            default => null,
        };
    }


    private function certificateData(int $productId, array $urls): array
    {
        $resources = $this->productResource
        ->clearQuery()
        ->join(
            'design_resource',
            'design_resource.design_resource_id',
            '=',
            'product_resource.design_resource_id'
        )
        ->join(
            'design_resource_document',
            'design_resource_document.design_resource_id',
            '=',
            'product_resource.design_resource_id'
        )
        ->where('product_resource.product_id', '=', $productId)
        ->whereIn('design_resource_document.url', $urls)
        ->select([
            'design_resource_document.design_resource_document_id',
            'design_resource_document.design_resource_id',
            'design_resource_document.media_id',
            'design_resource_document.name',
            'design_resource_document.url',
            'design_resource_document.description',
            'design_resource_document.format',
            'design_resource_document.created_at',
            'design_resource_document.updated_at',
            'product_resource.product_id'])
        ->findAll(false);

        $result = [];

        foreach ($resources as $resource) {

            $result[] = [
                'project_id'                  => null,
                'post_id'                     => null,
                'product_id'                  => $resource['product_id'] ?? null,
                'design_resource_id'          => $resource['design_resource_id'] ?? null,
                'design_resource_document_id' => $resource['design_resource_document_id'] ?? null,
                'media_id'                    => $resource['media_id'] ?? null,

                'image'                       => $resource['url'] ?? '',
                'path'                        => $resource['url'] ?? '',
                'objectURL'                   => $resource['url'] ?? '',

                'name'                        => $resource['name'] ?? '',
                'description'                 => $resource['description'] ?? '',
                'type'                        => $resource['format'] ?? '',
                'format'                      => $resource['format'] ?? '',
                'size'                        => 0,

                'status' => [
                    'name'     => 'Uploaded',
                    'severity' => 'success',
                ],

                'created_at'                  => $resource['created_at'] ?? null,
                'updated_at'                  => $resource['updated_at'] ?? null,
            ];
        }

        return $result;
    }

    // Merge insertProductTableImageFile and insertProductImages into one
    public function insertProductTableImageFile(array $data, string $property, int $product_id): bool
    {
        $product = $this->model->where('product_id', '=', $product_id)->first();
        if (!$product) {
            return false;
        }

        list($imageData) = $this->prepareImageFormat($data, $product_id);

        $this->db->beginTransaction();
        try {
            // Convert array to JSON before saving
            $product->update([$property => json_encode($imageData)]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteProductImage(int $product_image_id): bool
    {
        return $this->productImage->delete($product_image_id);
    }

    // Product Relations
    public function productRelated(array $relatedIds, int $productId): bool
    {
        try {
            $this->db->beginTransaction();

            // Delete existing related products
            $stmt = $this->db->prepare("DELETE FROM product_related WHERE product_id = ?");
            $stmt->execute([$productId]);

            // Insert new related products
            $stmt = $this->db->prepare("INSERT INTO product_related (product_related_id, product_id) VALUES (?, ?)");

            foreach ($relatedIds as $relatedId) {
                $stmt->execute([$relatedId, $productId]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getRelatedProducts(int $productId, int $limit = 10, string $slug = ''): array
    {

        $product = $this->getBySlug($slug);
        if(!$product){
            return [];
        }
        $productFamilyCode = $product->product_family_code;
        $productId = $product->product_id;
        if(!$productFamilyCode){
            return [];
        }

        $this->model->clearQuery();
        $query = $this->getProductQuery();
        $query->where('product.product_family_code', '=', $productFamilyCode);
        $query->whereNotIn('product.product_id', [$productId]);
        $query->limit($limit);
        // var_dump($query->getQuery());
        // exit;
        $result = $query->findAll();

        return $this->prepareProducts($result);
    }

    public function deleteRelatedProduct(int $product_id, int $related_product_id): bool
    {
        try {
            $this->db->beginTransaction();
            $relatedProduct = $this->productRelated->where('product_id', '=', $product_id)->where('product_related_id', '=', $related_product_id)->first();
            if (!$relatedProduct) {
                return false;
            }
            $relatedProduct->clearQuery();
            $relatedProduct->deleteWhere([
                'product_id' => $product_id,
                'product_related_id' => $related_product_id
            ]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete related product: " . $e->getMessage());
            return false;
        }
    }
    public function removeProductFromFamily(int $product_id, int $related_product_id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $product = $this->model->where('product_id', '=', $related_product_id)->first();
            if (!$product) {
                return false;
            }
            $product->update([
                'product_family_code' => null
            ]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete related product: " . $e->getMessage());
            return false;
        }
    }

    public function removeProductRelatedProject(int $product_id, int $project_id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $product = $this->productRelatedProject->deleteWhere(['product_id' => $product_id, 'project_id' => $project_id]);
            if (!$product) {
                return false;
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete related product: " . $e->getMessage());
            return false;
        }
    }

    public function getProductAlsoLike(string $productSlug, int $limit = 4): array
    {
        $product = $this->getBySlug($productSlug);
        if(!$product){
            return [];
        }
        $result = $this->productRelated
        ->where('product_related.product_id', '=', $product->product_id)
        ->select(['product_related.product_related_id'])
        ->limit($limit)
        ->findAll(false);
        $relatedProductIds = array_column($result, 'product_related_id');
        if(count($relatedProductIds) == 0){
            return [];
        }
        $this->model->clearQuery();
        $query = $this->getProductQuery();
        $query->whereIn('product.product_id', $relatedProductIds);
        $query->groupBy('product.product_id');
        $query->limit($limit);
        $products = $query->findAll();
        return $this->prepareProducts($products);
    }

    // Product Search
    public function relatedProductSearch(string $search): array
    {
        $this->model->clearQuery();
        $result = $this->model
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_content.name', 'LIKE', '%' . $search . '%')
            ->orWhere('product.product_code', 'LIKE', '%' . $search . '%')
            ->select(['product.product_id', 'product.product_code as name', 'product.product_code', 'product.image', 'product_content.tag_line'])
            ->orderBy('product.product_id', 'DESC')
            ->limit(50)
            ->findAll(false);

        $baseUrl = env('APP_URL');
        foreach ($result as &$product) {
            // $images = json_decode($product['image'], true);
            $images =isset($product['image']) && !empty($product['image']) ? json_decode($product['image'], true) : [];
            $product['image'] = isset($images[0]['objectURL']) && !empty($images[0]['objectURL']) ? $images[0]['objectURL'] : '';
            // not more 20 characters in the description
            $tagLine = isset($project['tag_line']) ? (string) $project['tag_line'] : '';
            $project['description'] = strlen($tagLine) > 20 ? substr($tagLine, 0, 20) . '...' : $tagLine;
        }

        return $result;
    }

    public function variantProductSearch(string $search): array
    {
        $this->model->clearQuery();
        $query = $this->getProductQuery();
        $query->with([
            'prices' => function ($query) {
                return $query->select(['price']);
            }
        ]);
        $query->where('product_content.name', 'LIKE', '%' . $search . '%')
            ->orderBy('product.product_id', 'DESC')
            ->limit(50);
        $result = $query->findAll(false);
        return $result;
    }

    public function digitalAssetSearch(string $search): array
    {
        $result = $this->digitalAsset
            ->join('digital_asset_content', 'digital_asset_content.digital_asset_id', '=', 'digital_asset.digital_asset_id')
            ->where('digital_asset_content.name', 'LIKE', '%' . $search . '%')
            ->select(['digital_asset.digital_asset_id', 'digital_asset_content.name'])
            ->findAll(false);
        return $result;
    }

    // waypoints product search hero component data
    public function getProductSearchForWaypoints(string $queryString): array
    {
        $this->model->clearQuery();
        $query = $this->getProductQuery();

        $query->where('product_content.name', 'LIKE', '%' . $queryString . '%')
            ->orWhere('product.product_code', 'LIKE', '%' . $queryString . '%')
            ->orderBy('product.product_id', 'DESC')
            ->limit(50);
        $products = $query->findAll(false);

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'label' => ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))),
                'href' => '/products/' . $product['category_slug'] . '/' . $product['product_slug'],
            ];
        }
        return $data;
    }

    // Product Taxonomy
    public function getTags(): array
    {
        return $this->getTaxonomyItems('tags');
    }

    public function getFinishes(): array
    {
        return $this->getTaxonomyItems('finishes');
    }

    private function getTaxonomyItems(string $type): array
    {
        return $this->taxonomyItem
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', $type)
            ->where('taxonomy.post_type', '=', 'product')
            ->where('taxonomy.site_id', '=', 1)
            ->where('taxonomy_item_content.language_id', '=', 1)
            ->select(['taxonomy_item.taxonomy_item_id', 'taxonomy_item_content.name'])
            ->findAll(false);
    }

    public function insertProductTaxonomies(array $data): bool
    {
        $this->db->beginTransaction();
        $this->taxonomyItem->insert($data['taxonomyItems']);
        $this->taxonomyItemContent->insert($data['taxonomyItemContents']);
        $this->productToTaxonomyItem->insert($data['productTaxonomies']);
        $this->taxonomyItem->insert($data['productSubCategoriesTaxonomyItems']);
        $this->taxonomyItemContent->insert($data['productSubCategoriesTaxonomyItemContents']);
        $this->db->commit();
        return true;
    }

    // Product Components (Frontend CMS Components)
    public function getProductHeroComponentData(array $param): array
    {
        $child_category_slug = isset($param['category']) ? $param['category'] : '';
        $results = $this->getProductComponentData($param);
        $results['parent_category'] = $child_category_slug;
        $results['child_category'] = $child_category_slug;

        $this->taxonomyItem->clearQuery();
        $childData = $this->taxonomyItem->where('taxonomy_item_code', '=', $child_category_slug)->select(['taxonomy_item_id', 'parent_id', 'name'])->first();
        $results['child_category_name'] = isset($childData?->data?->name) ? $childData?->data?->name : '';
        $parentId = $childData?->data?->parent_id??null;
        if(isset($childData->data->parent_id) && $childData->data->parent_id != 0){
            $this->taxonomyItem->clearQuery();
            $parentData = $this->taxonomyItem->where('taxonomy_item_id', '=', $parentId)->select(['taxonomy_item_code', 'name'])->first();
            $results['parent_category'] = isset($parentData->data->taxonomy_item_code) ? $parentData->data->taxonomy_item_code : '';
            $results['parent_category_name'] = isset($parentData->data->name) ? $parentData->data->name : '';
        }

        return $results;
    }
    public function getFeaturedProductSliderComponentData(array $params)
    {
        $project = $this->project->where('slug', '=', $params['slug'])->first();
        if(!$project){
            return [];
        }
        $project_id = $project->data->project_id;

        $this->productRelatedProject->clearQuery();
        $products = $this->productRelatedProject
                    ->where('product_related_project.project_id', '=', $project_id)
                    ->select(['product_related_project.*']);
        $products->orderBy('product_related_project.sort_order', 'ASC');
        $relatedProducts = $products->findAll(false);
        $productIds = array_column($relatedProducts, 'sort_order', 'product_id');

        // 124 =1
        // 7 = 2
        // 138 = 3
        // 202 = 4
        // 184 = 5
        // 4 = 6
        // 132 = 7
        // 199 = 8
        // 46 = 9
        // 141 = 10
        // 108 =11
        // 86 = 12
        // 10 = 13
        // 56 = 14
        // 13 =15
        // 169 = 16

        if(count($productIds) == 0){
            return [];
        }
        $this->model->clearQuery();
        $query = $this->getProductQuery();
        $query->groupBy('product.product_id');
        $query->where('product.status', '=', 1);
        $query->whereIn('product.product_id', array_keys($productIds));
        $products = $query->findAll();
        $results = $this->prepareProducts($products);
        
        foreach ($results as &$product) {
            $pid = isset($product['id']) ? $product['id'] : 0;

            if (isset($productIds[$pid])) {
                $product['sort_order'] = $productIds[$pid];
            } else {
                $product['sort_order'] = PHP_INT_MAX;
            }
        }
        unset($product);
        usort($results, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        $results['items'] = $results;
        return $results;
    }


    public function getProductFeaturedProjectsSliderComponentData(array $params)
    {
        $this->model->clearQuery();
        $product = $this->model->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->where('product_content.slug', '=', $params['slug'])->first();
        if(!$product){
            return [];
        }
        $product_id = $product->data->product_id;
        $this->model->clearQuery();

        $project = $this->productRelatedProject
                    ->join('project', 'project.project_id', '=', 'product_related_project.project_id')
                    ->where('product_related_project.product_id', '=', $product_id)
                    ->select(['product_related_project.sort_order', 'project.project_id', 'project.title', 'project.slug', 'project.image', 'project.preview_text', 'project.location']);

        $project->orderBy('project.project_id', 'DESC');
        $project->orderBy('product_related_project.sort_order', 'ASC');
        $projects = $project->findAll(false);
        
        foreach($projects as &$project){
            $project['image'] = json_decode($project['image'], true);
            $project['image'] = isset($project['image'][0]['objectURL']) ? $project['image'][0]['objectURL'] : '';
        }

        // usort($projects, function($a, $b) {
        //     return $a['sort_order'] <=> $b['sort_order'];
        // });
        return $projects;
    }
    public function getFeaturedProductMasonryComponentData(array $param)
    {
        $model = 'product';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id')
            ->join('taxonomy_item', 'taxonomy_item.taxonomy_item_id', '=', 'product_to_taxonomy_item.taxonomy_item_id')
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id');
            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }

            $fields = $param['fields'];
            $fields[] = 'product_content.tag_line';
            $fields[] = 'product_content.title';
            $fields[] = 'CONCAT("products/", taxonomy_item_content.slug, "/", product_content.slug) as href';
            $query->select($fields); // Add other needed columns
            $query->where('product.is_featured', '=', 1);
            $query->where('product.status', '=', 1);
            $query->orderBy('product.product_id', 'DESC');

            $products = $query->findAll();

            $result = [];
            foreach ($products as $key => $product) {
                $p = [];
                $p['heading'] = $product['title']?? ucwords(strtolower(str_replace(['_', '-'], ' ', $product['name'])));
                $p['product_id'] = $product['product_id'];
                $p['des'] = $product['description'];
                $p['tag_line'] = $product['tag_line'];
                $p['slug'] = $product['slug'];
                $p['link'] = $product['href'];
                if (in_array($key, [0, 3])) {
                    $p['class'] = 'th-masonry-grid-item grid-col-span-8';
                }
                if (in_array($key, [1, 2])) {
                    $p['class'] = 'th-masonry-grid-item grid-col-span-5';
                }

                if (isset($product['image'])) {
                    $image = json_decode($product['image'], true);
                    if (isset($image[0]['objectURL'])) {
                        $p['img'] = $image[0]['objectURL'];
                    } else {
                        $p['img'] = $image[0] ?? null;
                    }
                }

                $result[] = $p;
            }
            return $result;
        }
        return [];
    }

    public function getProductFeatureComponentData(array $params)
    {
        $results = $this->getProductComponentData($params);

        if (!count($results)) {
            return [
                'sectionTitle' => 'Features',
                'sectionSubtitle' => "Experience the ultimate blend of <span class='font-weight-700'>comfort</span> and <span class='font-weight-700'>functionality</span> with Archi, where <span class='font-weight-700'>cutting-edge design</span> meets adjustable precision for every workspace need",
                'sectionLinkText' => 'Order Online',
                'items' => []
            ];
        }
        $items = $this->prepareFeatureResult($results, 'feature_image', 3);

        return [
            'sectionTitle' => $results['feature_title'] ?? 'Features',
            'sectionSubtitle' => $results['feature_description'] ?? '',
            'sectionLinkText' => $results['feature_link_text'] ?? '',
            'items' => $items
        ];
    }

    public function getProductStoryMasonryComponentData(array $params)
    {
       $results = $this->getProductComponentData($params);

        if (!count($results)) {
            return [
                'items' => []
            ];
        }

        $items = $this->prepareFeatureResult($results, 'main_image', 2);
        return [
            'items' => $items
        ];
    }

    public function getProductSpecificationsComponentData($param = []): array
    {
        $results = $this->getProductComponentData($param);

        $specificationImageUrl = '';
        foreach ([
            'specifications_image',
            'feature_image_one',
            'image_thumb',
            'image',
            'main_image_one',
            'main_image_two',
        ] as $imageField) {
            $url = $this->getImageUrl($results[$imageField] ?? null);
            if ($url !== '') {
                $specificationImageUrl = $url;
                break;
            }
        }

        $specifications = $this->parseProductSpecifications($results['specifications'] ?? null);
        
        $items = [
            'id' => "specifications",
            'title' => $results["specifications_title"] ?? null,
            'specifications' => $specifications,
            'img' => $specificationImageUrl,
            'heading' => $results["specifications_title"] ?? null,
        ];

        return $items;
    }

    public function getProductRelatedProjectComponentData(string $slug): array
    {
        $project = $this->project->where('slug', '=', $slug)->first();
        if(!$project){
            return [];
        }
        $project_id = $project->data->project_id;

        $query = $this->productRelatedProject
        ->join('product', 'product.product_id', '=', 'product_related_project.product_id')
        ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->where('product_related_project.project_id', '=', $project_id)
        ->select(['product_related_project.*', 'product_content.name', 'product_content.title', 'product.image', 'product_content.slug', 'product.description', 'product_content.tag_line'])
        ->limit(4);
        $products = $query->findAll(false);

        $result = [];
        foreach ($products as $key => $product) {
            $p = [];
            $p['heading'] = ucfirst($product['title']?? ucwords(strtolower(str_replace(['_', '-'], ' ', $product['name']))));
            $p['product_id'] = $product['product_id'];
            $p['des'] = $product['description'];
            $p['tag_line'] = $product['tag_line'];
            $p['slug'] = $product['slug'];
            $p['link'] = '/products/seating/' . $product['slug'];
            if (in_array($key, [0, 3])) {
                $p['class'] = 'th-masonry-grid-item grid-col-span-8';
            }
            if (in_array($key, [1, 2])) {
                $p['class'] = 'th-masonry-grid-item grid-col-span-5';
            }

            if (isset($product['image'])) {
                $image = json_decode($product['image'], true);
                if (isset($image[0]['objectURL'])) {
                    $p['img'] = $image[0]['objectURL'];
                } else {
                    $p['img'] = $image[0] ?? null;
                }
            }

            $result[] = $p;
        }
        return $result;
    }

    public function getProductCallToActionComponentData(int $productId, array $fields, int $limit = 3)
    {
        return [];
    }

    public function getProductSustainabilityComponentData(int $productId, array $fields, int $limit = 1)
    {
        return [];
    }

    public function getCategorySeatingDetailsComponentData(array $params): array
    {
        return [];
    }

    // not use 
    public function getProductsByCategoryDetailsComponent(array $params): array
    {
        return []; 
    }
    public function getSliderProducts(array $param): array
    {
        $this->model->clearQuery();
        $query = $this->getProductQuery();
        $query->where('product.is_featured', '=', $param['is_featured'])->orderBy('product.product_id', 'DESC')->limit($param['item_count']);
        $results = $query->findAll(false);

        // return $this->prepareProducts($results);
        // $result = $this->model
        //     ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        //     ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id')
        //     ->join('taxonomy_item_content as category', 'category.taxonomy_item_id', '=', 'product_to_taxonomy_item.taxonomy_item_id')
        //     ->where('product.is_featured', '=', $param['is_featured'])
        //     ->orderBy('product.product_id', 'DESC')

        //     ->select($param['fields'])
        //     ->limit($param['item_count']);

        // $result = $result->findAll(false);

        return $results;
    }

    public function getProductInstagramSliderComponentData(string $slug): array
    {
        $product = $this->productContent->where('slug', '=', $slug)->first();
        if(!$product){
            return [];
        }
        $product_id = $product->data->product_id;

        $query = $this->productRelatedProject
        ->join('project', 'project.project_id', '=', 'product_related_project.project_id')
        ->where('product_related_project.product_id', '=', $product_id)
        ->select(['product_related_project.*', 'project.title', 'project.image', 'project.slug'])
        ->limit(4);
        $projects = $query->findAll(false);

        $result = [];
        foreach ($projects as $key => $project) {
            $p = [];
            $p['heading'] = ucfirst($project['title']?? '');
            $p['project_id'] = $project['project_id'];
            $p['slug'] = $project['slug'];
            // $p['link'] = '/projects/' . $project['slug'];
            $p['link'] = '/krostfurniture/' . $project['slug'];


            if (isset($project['image'])) {
                $image = json_decode($project['image'], true);
                if (isset($image[0]['objectURL'])) {
                    $p['img'] = $image[0]['objectURL'];
                } else {
                    $p['img'] = $image[0] ?? null;
                }
            }

            $result[] = $p;
        }
        return [
            'title' => $product->data->title,
            'items' => $result
        ];
    }

    // Product Tabs
    //product configurator - product details tabs data
    public function getProductVideoGallaryImagesData(array $params): array
    {
        $query = $this->productImage
        // ->join('media', 'media.media_id', '=', 'product_image.media_id')
        ->join('product_content', 'product_content.product_id', '=', 'product_image.product_id')
        ->where('product_image.product_id', '=', $params['product_id'])
        ->select(['product_image.product_id', 'product_image.image', 'product_image.image as file','product_image.image_link as path', 'product_image.type', 'product_content.name']);
        
        $results = $query->findAll(false);

        if(isset($results) && !empty($results)){
            $results = array_map(function($r, $i) {
                $data = new ResourceImageData($r, $i, '');
                return $data->toArray();
            }, $results, array_keys($results));
        }
        return ['items' => $results];
    }

    public function getProductDownloadsTabData(array $params): array
    {       
        $results = $this->designResourceRepository->getModelData($params['product_id'], $params['resource_types']);

        $FILE_FORMAT_IMAGES = [
            'GSM' => '/media/design-resource/icons/gsm.png',
            'DWG' => '/media/design-resource/icons/dwg.png',
            'MAX' => '/media/design-resource/icons/max.png',
            'SKP' => '/media/design-resource/icons/skp.png',
            'RFA' => '/media/design-resource/icons/rfa.png',
            'ZIP' => '/media/design-resource/icons/zip.png',
            'PDF' => '/media/design-resource/icons/pdf.png',
            'DOC' => '/media/design-resource/icons/doc.png',
            'DOCX' => '/media/design-resource/icons/docx.png',
            'XLS' => '/media/design-resource/icons/xls.png',
            'XLSX' => '/media/design-resource/icons/xlsx.png',
            'PPT' => '/media/design-resource/icons/ppt.png',
            'PPTX' => '/media/design-resource/icons/pptx.png',
            'JPG' => '/media/design-resource/icons/jpg.png',
        ];
        
        $results = array_filter($results, function ($result) {
            return stripos($result['name'] ?? '', 'AFRDI') === false;
        });

        // map results to array
        $results = array_map(function ($result) use ($FILE_FORMAT_IMAGES, $params) {
            $objectURL = null;
            if (in_array('models', $params['resource_types'])) {
                $format = $result['format'] ?? 'ZIP';
                $objectURL = $FILE_FORMAT_IMAGES[$format] ?? $FILE_FORMAT_IMAGES['ZIP'];
            }

            // format name: option 01            
            $friendlyName = $result['name'];
            if (!empty($result['name'])) {
                $pathInfo = pathinfo($result['name']);
                $filenameWithoutExt = $pathInfo['filename'];
                $cleanedName = trim(str_replace(['_', '-'], ' ', $filenameWithoutExt));
                $friendlyName = $cleanedName . " (" . $format . ")";
            }
            // option 01
            // format name: option 02
            // if (!empty($result['name'])) {
            //     // format maping by name
            //     $friendlyFormatNames = [
            //         'DWG'  => 'AutoCAD Drawing',
            //         'GSM'  => 'ArchiCAD Object',
            //         'MAX'  => '3ds Max Model',
            //         'RFA'  => 'Revit 3D Model',
            //         'SKP'  => 'SketchUp Model',
            //         'ZIP'  => 'CAD & 3D Assets',
            //         'PDF'  => 'User Guide / Specification',
            //     ];
            //     if (isset($friendlyFormatNames[$format])) {
            //         $friendlyName = $friendlyFormatNames[$format] . " (" . $format . ")";
            //     } else {
            //         $pathInfo = pathinfo($result['name']);
            //         $cleanedName = trim(str_replace(['_', '-'], ' ', $pathInfo['filename']));
            //         $friendlyName = ucwords(strtolower($cleanedName)) . " (" . $format . ")";
            //     }
            // }
            // end option 02

            $cleanUrl = $result['url'] ?? '';
            if (!empty($cleanUrl)) {
                $cleanUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $cleanUrl);
                $cleanUrl = str_replace(' ', '%20', $cleanUrl);
            }
            
            return [
                'name'      => $friendlyName,
                'raw_filename' => $result['name'],
                'format'    => $result['format'],
                'media_id'  => $result['media_id'],
                'url'       => $cleanUrl,
                'objectURL' => $objectURL,
            ];
        }, $results);
        return $results;
    }

    // public function getProductCertificationsTabDataBackup(array $params): array
    // {
    //     $query = $this->productCertificate
    //     ->join('product', 'product.product_id', '=', 'product_certificate.product_id')
    //     ->where('product_certificate.product_id', '=', $params['product_id'])
    //     ->select([
    //         'product_certificate.*',
    //         'product.product_id',     
    //     ]);

    //     $results = $query->findAll(false);  
    //     $data = [];
    //     foreach ($results as $result) {
    //         $item = [];
    //         // format loao
    //         $logo = json_decode($result['logo'], true);
    //         $logoUrl = $logo[0]['objectURL'] ?? '';
    //         $item['logo'] = $logoUrl;
    //         $certificateUrl = isset($result['certificate_file']) ? json_decode($result['certificate_file'], true) : [];
    //         $certificateUrl = isset($certificateUrl[0]['objectURL']) ? $certificateUrl[0]['objectURL'] : '';
    //         $item['certificateDownloadLink'] = $certificateUrl;

    //         $certificate = isset($result['certificate_file']) ? json_decode($result['certificate_file'], true) : [];
    //         $certificateTitle = $certificate[0]['file']['name']?? '';
    //         $item['title'] = $certificateTitle;
    //         $data[] = $item;
    //     }

    //     return $data;
    // }
    
    public function getProductCertificationsTabData(array $params): array
    {
        $query = $this->productCertificate
        ->join('product', 'product.product_id', '=', 'product_certificate.product_id')
        ->where('product_certificate.product_id', '=', $params['product_id'])
        ->select([
            'product_certificate.*',
            'product.product_id',     
        ]);

        $results = $query->findAll(false);  
        $data = [];
        
        foreach ($results as $result) {
            $item = [];
            
            // format certificate link
            $certificateUrlData = isset($result['certificate_file']) ? json_decode($result['certificate_file'], true) : [];
            $certificateUrl = isset($certificateUrlData[0]['objectURL']) ? $certificateUrlData[0]['objectURL'] : '';
            $certificateTitle = $certificateUrlData[0]['file']['name'] ?? '';
            
            // clean url
            if (!empty($certificateUrl)) {
                $certificateUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $certificateUrl);
                $certificateUrl = str_replace(' ', '%20', $certificateUrl);
            }
            $item['certificateDownloadLink'] = $certificateUrl;

            // format title
            $friendlyTitle = 'Product Certificate';
            if (!empty($certificateTitle)) {
                $pathInfo = pathinfo($certificateTitle);
                $filenameWithoutExt = $pathInfo['filename'];

                $cleanedTitle = str_replace(['_', '-'], ' ', $filenameWithoutExt);
                $friendlyTitle = ucwords(strtolower(trim($cleanedTitle)));
                
                // AFRDI
                $friendlyTitle = str_ireplace('Afrdi', 'AFRDI', $friendlyTitle);

                // add Certificate 
                if (stripos($friendlyTitle, 'certificate') === false) {
                    $friendlyTitle .= ' Certificate';
                }
            }
            $item['title'] = $friendlyTitle;

            // logo condition
            $finalLogoUrl = '';
            
            if (stripos($friendlyTitle, 'blue') !== false) {
                // blue logo
                $finalLogoUrl = '/media/Certificates/blue-afrdi-logo.png';
            } elseif (stripos($friendlyTitle, 'green') !== false) {
                // green logo
                $finalLogoUrl = '/media/Certificates/green-afrdi-logo.png';
            } else {
                // 'blue or green both not but file format pdf
                $pathInfo = pathinfo($certificateTitle);
                $extension = strtoupper($pathInfo['extension'] ?? '');
                
                if ($extension === 'PDF') {
                    $finalLogoUrl = '/media/design-resource/icons/pdf.png';
                } else {
                    // other file png/jpg/webp
                    $logo = json_decode($result['logo'], true);
                    $finalLogoUrl = $logo[0]['objectURL'] ?? '';
                }
            }

            // final logo
            if (!empty($finalLogoUrl)) {
                $finalLogoUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $finalLogoUrl);
                $finalLogoUrl = str_replace(' ', '%20', $finalLogoUrl);
            }
            $item['logo'] = $finalLogoUrl;
            
            $data[] = $item;
        }

        return $data;
    }
    // Product Import
    public function importProducts(string $csv_file): array
    {
        // Create a CSV reader from the file path
        $reader = Reader::createFromPath($csv_file, 'r');
        // Use the first row as the header keys for each record
        $reader->setHeaderOffset(0);
        // Fetch the headers from the CSV file
        $headers = $reader->getHeader();
        // Validate header presence (must exist for proper mapping)
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        // Retrieve all records (each row mapped by header)
        $records = $reader->getRecords();

        // Initialize result containers
        $valid = [];                 // Successfully validated products
        $validProducts = [];          // Successfully validated products
        $validProductContents = [];   // Product content data (e.g., descriptions)
        $invalid = [];                // Invalid records with validation errors
        $updatedProducts = [];                // Records that were detected as duplicates
        $updatedProductContents = [];         // Product content data (e.g., descriptions) that were detected as duplicates
        $duplicated = [];             // Records that were detected as duplicates
        $processed = [];              // Track processed unique identifiers
        $productToRelationships = [];  // Holds category-to-product mapping
        $tagRelationships = [];       // Holds tag-to-product mapping
        $mediaData = [];                  // Holds media data
        // Map all categories for validation and ID resolution
        // Example result:
        // ['Workstations' => 1, 'Fixed Height Workstations' => 2, 'Height Adjustable Workstations' => 3]
        $taxonomyNameToIdMapByCategory = $this->getCategoriesForValidation();
        // Map all tags for validation and ID resolution
        $taxonomyNameToIdMapByTags = $this->getTagsForValidation();
        // Get default field values to merge with incoming records
        $defaultFields = $this->getDefaultFields($headers);

        // Create lookup maps for manufacturer and vendor by code
        $manufacturerIdByCode = $this->getIdMap($this->manufacturer, 'manufacturer_code', 'manufacturer_id');
        $vendorIdByCode = $this->getIdMap($this->vendor, 'vendor_code', 'vendor_id');

        $existingData = [];
        $importingProductCodes = array_column(iterator_to_array($records), 'product_code');
        $products = $this->model->select(['product_id', 'product_code', 'sku'])
            ->whereIn('product_code', $importingProductCodes)
            ->limit(0)->findAll(false);

        $productCodeToIdMapByProduct = array_column($products, 'product_id', 'product_code');           // Records that were detected as duplicates

        // Process each record (row) from the CSV
        foreach ($records as $offset => $record) {
            try {
                if ((!isset($record['product_code']) || empty($record['product_code']))
                && (!isset($record['product_id']) || empty($record['product_id']))) {
                    $invalid[] = [
                        'row' => $offset + 1, // +2 because CSV row count starts at 1 and includes header
                        'data' => $record,
                        'errors' => 'Either product_code or product_id is required'
                    ];
                    continue;
                }
                if(isset($record['product_code']) && !empty($record['product_code'])){
                    $record['product_id'] = $productCodeToIdMapByProduct[$record['product_code']] ?? null;
                }
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);

                // Resolve manufacturer_id based on manufacturer_code
                $record['manufacturer_id'] = isset($record['manufacturer_code']) && $record['manufacturer_code'] ? $manufacturerIdByCode[$record['manufacturer_code']] ?? null : null;

                // Resolve vendor_id based on vendor_code
                $record['vendor_id'] = isset($record['vendor_code']) && $record['vendor_code'] ? $vendorIdByCode[$record['vendor_code']] ?? null : null;

                // Split category string into an array (e.g. "Workstations,Fixed Height Workstations,Height Adjustable Workstations" → ['Workstations','Fixed Height Workstations','Height Adjustable Workstations'])
                $record['category'] = isset($record['category']) ? explode(',', $record['category']) : [];
                $record['tags'] = isset($record['tags']) ? explode(',', $record['tags']) : [];

                // Validate product data using a dedicated validator class
                $validator = new ProductDataValidation($record, $taxonomyNameToIdMapByCategory, $taxonomyNameToIdMapByTags, $productCodeToIdMapByProduct);
                $validated = $validator->validate();

                // If validation fails, store record and error info in $invalid
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 1, // +2 because CSV row count starts at 1 and includes header
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }
                // Get a unique identifier for this product (e.g., product_code or name or SKU)
                $unique = $validator->getUniqueIdentifier();

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $duplicated[] = [
                        'row' => $offset + 1,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                // Mark this unique product as processed
                $processed[] = $unique;
                // Extract data directly from validated stdClass objects
                $product = (array) $validated->product;
                $content = (array) $validated->product_content;
                // Store category relationships for later processing
                $validatedData = $validated->toArray();
                if(isset($validated->media) && !empty($validated->media)){
                    $mediaData[] = (array) $validated->media;
                }
                if (!empty($validatedData['categories_data']) || !empty($validatedData['tags_data'])) {
                    $productToRelationships[] = [
                        'product_code' => $product['product_code'] ?? null,
                        'product_id' => $product['product_id'] ?? null,
                        'categories' => $validatedData['categories'],
                        'categories_data' => $validatedData['categories_data'] ?? [],
                        'tags_data' => $validatedData['tags_data'] ?? [],
                    ];
                }
                if ($validated->isExistingData) {
                    $updatedProducts[] = $product;
                    $updatedProductContents[] = $content;
                } else {
                    // Extract data directly from validated stdClass objects
                    $valid[] = $product;
                    if (count($product) > 0) $validProducts[$product['product_code']] = $product;
                    if (count($content) > 0) $validProductContents[$product['product_code']] = $content;
                }
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 1,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        // Initialize a counter for inserted products
        $insertedCount = 0;
        // Insert all validated products and their related content into the database
        // This custom method likely handles batch inserts for performance
        // and returns information such as inserted IDs and counts
        $mediaIdsMap = $this->insertMedia($mediaData);
        $insertResult = $this->insertProductsAndContents($validProducts, $validProductContents, $mediaIdsMap);
        $updatedResult = $this->insertProductsAndContents($updatedProducts, $updatedProductContents, $mediaIdsMap);
        $productToResult = [
            'valid_categories' => [],
            'valid_tags' => [], 
        ];
        // Process product-to-category relationships after product insertion.
        // This step ensures that each imported product is linked to its correct categories.
        if (!empty($productToRelationships)) {
            // i will return ['category_summary' => [], 'tag_summary' => []]
            $productToResult = $this->processBatchProductToRelationships(
                // Category relationship data collected during validation.
                // Each element includes product_code, product_id, categories, and categories_data.
                // Example:
                // [
                //   'product_code' => 'PRD-001',
                //   'product_id'   => 123,
                //   'categories_data' => [['product_code' => 'PRD-001', 'catgoriy_id' => 19], ['product_code' => 'PRD-002', 'catgoriy_id' => 20]]
                // ]
                $productToRelationships,

                // Product IDs returned from the insertProductsAndContents() method.
                // Used to map category relationships to the actual inserted product IDs.
                // Example: [ 'PRD-001' => 123, 'PRD-002' => 124, ... ]
                $insertResult['productIds'] ?? [],

                // Taxonomy name-to-ID mapping, built earlier in getCategoriesForValidation().
                // Used to translate category names into taxonomy_item_id values.
                // Example: ['Workstations' => 1, 'Height Adjustable' => 3, ...]
                $taxonomyNameToIdMapByCategory ?? []
            );
        }
        // process product to tags

        // Safely calculate counts
        $validCategories = $productToResult['valid_categories'] ?? [];
        $validTags = $productToResult['valid_tags'] ?? [];

        // Build a detailed summary report to return
        return [
            // Indicate that the import completed successfully
            'success' => true,
            // Count the total number of records processed from the CSV file
            'total_records' => iterator_count($records),
            // Count of successfully validated product records
            'valid_records' => count($valid),
            // Count of records that failed validation
            'invalid_records' => count($invalid), // count($invalid),
            // Count of duplicate or already-processed records
            'updated_records' => count($updatedProducts),
            // Records that were skipped or detected as duplicates
            'updated_data' => $updatedProducts,
            // Product insertion results and valid product data
            'valid_data' => $valid,
            'duplicated_data' => $duplicated,
            'duplicated_records' => count($duplicated),
            'products' => [
                // Total number of product rows inserted into the database
                'inserted_count' => $insertResult['inserted_count'],
                // List of validated product arrays that were ready for insertion
                'valid_data' => $valid,
                'updated_count' => $updatedResult['inserted_count'],
                'updated_data' => $updatedProducts,
            ],
            // Product content insertion results and valid content data
            'product_contents' => [
                // Number of related content rows inserted (e.g., descriptions, translations)
                'inserted_count' => $insertResult['inserted_content_count'],
                // List of validated product content arrays
                'valid_data' => $validProductContents,
                'updated_count' => $updatedResult['inserted_content_count'],
                'updated_data' => $updatedProductContents
            ],
            // Category relationship statistics
            'categories' => [
                'valid_data' => $validCategories, // $productToResult['catgories_summary]
                // Total category links (product-category mappings)
                'total_relationships' => count($validCategories), // $productToResult['catgories_summary]
                // Status message to indicate processing result
                'processed_relationships' => !empty($validCategories)
                    ? 'Processed successfully'
                    : 'No category relationships to process',
                'error' => $productToResult['error']['message'] ?? null
            ],
            // Tags relationship statistics
            'tags' => [
                'valid_data' => $validTags,
                'total_relationships' => count($validTags),
                'processed_relationships' => !empty($validTags)
                    ? 'Processed successfully'
                    : 'No tag relationships to process'
            ],
            // All invalid (failed) record details, including errors and data
            'invalid_data' => $invalid, // $invalid,,
            // Final import summary and performance metrics
            'summary' => [
                // Success percentage (valid ÷ total records)
                'success_rate' => count($valid) > 0 ? round((count($valid) / iterator_count($records)) * 100, 2) . '%' : '0%',
                // Total number of products successfully processed
                'products_processed' => count($valid),
                // Number of product content records created
                'content_records_created' => $insertResult['inserted_content_count'],
                // Total number of product–category relationships created
                'category_relationships' => count($productToResult),
                // Total number of failed records
                'errors' => count($invalid)
            ]
        ];
    }

    public function importProductsImages(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validImages = [];
        $frontendValidData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultProductImageFields($headers);

        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }

        $config = app('config');
        $imageServer = $config['APP_URL'];

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);

                if (empty($record['product_code']) || empty($record['image_link'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Both product_code and image_link are required']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }

                $imageLink = $record['image_link'];
                $unique = $productId . '-' . $imageLink;

                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                // Convert image_link to image JSON format
                $imagePath = str_contains($imageLink, '/media/Products/gallery/') ? $imageLink : "/media/Products/gallery/{$imageLink}";
                $imageJson = json_encode([
                    [
                        'name' => basename($imageLink),
                        'objectURL' => $imageServer . $imagePath,
                        'size' => 0,
                        'type' => 'image/jpeg',
                        'path' => ROOT_DIR . PUBLIC_PATH . $imagePath,
                        'status' => ['name' => 'Uploaded', 'severity' => 'success']
                    ]
                ]);

                $sortOrder = isset($record['sort_order']) ? (int)$record['sort_order'] : 0;
                $status = isset($record['status']) ? $record['status'] : json_encode(['active' => true]);
                $wayPoints = isset($record['way_points']) ? $record['way_points'] : json_encode([]);

                // Ensure status and way_points are JSON
                if (!$this->isValidJson($status)) {
                    $status = json_encode(['active' => true]);
                }
                if (!$this->isValidJson($wayPoints)) {
                    $wayPoints = json_encode([]);
                }

                $validImages[] = [
                    'product_id' => $productId,
                    'image' => $imageJson,
                    'image_link' => $imageLink,
                    'sort_order' => $sortOrder,
                    'status' => $status,
                    'way_points' => $wayPoints
                ];
                $validProductCode[] = ['product_code' => $record['product_code']];
                // only show frontend
                $frontendValidData[] = [
                    'product_id' => $productId,
                    'product_code' => $record['product_code'],
                    'image' => $imageJson,
                    'image_link' => $imageLink,
                    'sort_order' => $sortOrder,
                    'status' => $status,
                    'way_points' => $wayPoints
                ];

                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validImages)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productImage->upsert($validImages, ['product_id', 'image_link']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert product images: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validImages),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $frontendValidData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importRelatedProducts(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validRelations = [];
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultRelatedProductFields($headers);

        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);

                if (empty($record['product_code']) || empty($record['related_product_code'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Both product_code and related_product_code are required']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;
                $relatedProductId = $productCodeMap[$record['related_product_code']] ?? null;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }

                if (!$relatedProductId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Related product not found: {$record['related_product_code']}"]
                    ];
                    continue;
                }

                $unique = $productId . '-' . $relatedProductId;
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                $validRelations[] = [
                    'product_id' => $productId,
                    'product_related_id' => $relatedProductId
                ];
                $validData[] = [
                    'product_code' => $record['product_code'],
                    'related_product_code' => $record['related_product_code'],
                ];
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validRelations)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productRelated->upsert($validRelations, ['product_id', 'product_related_id']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert related products: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validRelations),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $validData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importProductsSortByCategory(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validRelations = [];
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->limit(0)->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }

        $allCategories = $this->taxonomyItem->select(['taxonomy_item_id', 'name'])->limit(0)->findAll(false);
        $categoryCodeMap = [];
        foreach ($allCategories as $category) {
            $categoryCodeMap[$category['name']] = (int)$category['taxonomy_item_id'];
        }

        foreach ($records as $offset => $record) {
            try {
                if (empty($record['product_code']) || empty($record['category']) || empty($record['sort_order'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['All fields (product_code, category and sort_order) are required']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;
                $categoryId = $categoryCodeMap[$record['category']] ?? null;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }

                if (!$categoryId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Category not found: {$record['category']}"]
                    ];
                    continue;
                }

                $unique = $productId . '-' . $categoryId;
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                $validRelations[] = [
                    'product_id' => $productId,
                    'taxonomy_item_id' => $categoryId,
                    'sort_order' => $record['sort_order']
                ];
                $validData[] = [
                    'product_code' => $record['product_code'],
                    'category' => $record['category'],
                    'sort_order' => $record['sort_order']
                ];
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validRelations)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productToTaxonomyItem->upsert($validRelations, ['product_id', 'taxonomy_item_id']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert products sort by category: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validRelations),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $validData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importProductsDigitalAssets(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validRelations = [];
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultDigitalAssetFields($headers);

        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }

        // Pre-fetch all digital assets for validation
        $allAssets = $this->digitalAsset->select(['digital_asset_id', 'digital_asset_code'])->findAll();
        $digitalAssetCodeMap = [];
        foreach ($allAssets as $asset) {
            $digitalAssetCodeMap[$asset['digital_asset_code']] = (int)$asset['digital_asset_id'];
        }
        $assetIds = array_column($allAssets, 'digital_asset_id');

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);

                if (empty($record['product_code']) || empty($record['digital_asset_code'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Both product_code and digital_asset_id are required']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;
                $digitalAssetId = $digitalAssetCodeMap[$record['digital_asset_code']] ?? null;
                // $digitalAssetId = (int)$record['digital_asset_id'];

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }

                if (!in_array($digitalAssetId, $assetIds)) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Digital asset not found: {$digitalAssetId}"]
                    ];
                    continue;
                }

                $unique = $productId . '-' . $digitalAssetId;
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                $validRelations[] = [
                    'product_id' => $productId,
                    'digital_asset_id' => $digitalAssetId
                ];
                $validData[] = [
                    'product_code' => $record['product_code'],
                    'digital_asset_code' => $record['digital_asset_code']
                ];
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validRelations)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productToDigitalAsset->upsert($validRelations, ['product_id', 'digital_asset_id']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert product digital assets: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validRelations),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $validData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    // product to attribute table 
    public function importProductsAttributes(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validAttributes = [];
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        $defaultFields = $this->getDefaultProductAttributeFields($headers);

        /** -----------------------------
         * Prefetch product, language, and attribute mappings
         * ----------------------------- */

        // Products
        $allProducts = $this->model->select(['product_id', 'product_code'])->findAll();
        $productCodeMap = array_column($allProducts, 'product_id', 'product_code');

        // Languages
        $allLanguages = $this->language->select(['language_id', 'code'])->findAll();
        $languageCodeMap = array_column($allLanguages, 'language_id', 'code');

        // Attributes (from attribute_content)
        $allAttributes = $this->attributeContent
            ->select([
                'attribute_content.attribute_id',
                'attribute_content.name',
                'attribute_content.language_id',
            ])
            ->join('attribute', 'attribute_content.attribute_id', '=', 'attribute.attribute_id')
            ->findAll(false);

        $attributeCodeMap = [];
        foreach ($allAttributes as $attri) {
            $key = trim($attri['language_id'] . '|' . strtolower($attri['name']));
            $attributeCodeMap[$key] = (int)$attri['attribute_id'];
        }

        $attributeIds = array_column($allAttributes, 'attribute_id');

        // Existing product attributes (for detecting updates)
        $existingRecords = $this->productAttribute
            ->select(['product_id', 'attribute_id', 'language_id'])
            ->findAll(false);

        $existingKeys = [];
        foreach ($existingRecords as $row) {
            $existingKeys[$row['product_id'] . '-' . $row['attribute_id'] . '-' . $row['language_id']] = true;
        }

        /** -----------------------------
         * Process CSV Records
         * ----------------------------- */
        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);

                if (
                    empty($record['product_code']) ||
                    empty($record['language_code']) ||
                    empty($record['value'])
                ) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['All fields (product_code, language_code, value) are required'],
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;
                $languageId = $languageCodeMap[$record['language_code']] ?? 1;

                // "value" is represents the attribute name from attribute_content
                $attributeName = strtolower(trim($record['value']));
                $key = $languageId . '|' . $attributeName;
                $attributeId = $attributeCodeMap[$key] ?? null;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"],
                    ];
                    continue;
                }

                if (!$attributeId || !in_array($attributeId, $attributeIds, true)) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Attribute not found for value '{$record['value']}' ({$record['language_code']})"],
                    ];
                    continue;
                }

                $unique = $productId . '-' . $attributeId . '-' . $languageId;

                if (in_array($unique, $processed, true)) {
                    continue;
                }

                if (isset($existingKeys[$unique])) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                $validAttributes[] = [
                    'product_id'   => $productId,
                    'attribute_id' => $attributeId,
                    'language_id'  => $languageId,
                    'value'        => $record['value'],
                ];

                $validData[] = [
                    'product_code'  => $record['product_code'],
                    'language_code' => $record['language_code'],
                    'value'         => $record['value'],
                ];

                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()],
                ];
            }
        }

        /** -----------------------------
         * Insert / Update
         * ----------------------------- */
        $insertedCount = 0;

        if (!empty($validAttributes)) {
            try {
                $this->db->beginTransaction();

                // upsert
                $insertedCount = $this->productAttribute->upsert(
                    $validAttributes,
                    ['product_id', 'attribute_id', 'language_id']
                );

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert product attributes: " . $e->getMessage());
            }
        }

        /** -----------------------------
         * Final Report
         * ----------------------------- */
        return [
            'success'           => true,
            'valid_records'     => count($validAttributes),
            'inserted_count'    => $insertedCount,
            'updated_records'   => count($updated),
            'invalid_records'   => count($invalid),
            'valid_data'        => $validData,
            'updated_data'      => $updated,
            'invalid_data'      => $invalid,
        ];
    }

    public function importProductsVariants(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validVariants = [];
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultProductVariantFields($headers);

        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);

                if (empty($record['product_code']) || empty($record['variant_product_code'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Both product_code and variant_product_code are required']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;
                $variantProductId = $productCodeMap[$record['variant_product_code']] ?? null;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }

                if (!$variantProductId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Variant product not found: {$record['variant_product_code']}"]
                    ];
                    continue;
                }

                $unique = $productId . '-' . $variantProductId;
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                $validVariants[] = [
                    'product_id' => $productId,
                    'product_variant_id' => $variantProductId
                ];
                $validData[] = [
                    'product_code' => $record['product_code'],
                    'variant_product_code' => $record['variant_product_code']
                ];
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validVariants)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productVariant->upsert($validVariants, ['product_id', 'product_variant_id']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert product variants: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validVariants),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $validData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importProductsOptions(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validOptions = [];
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultProductOptionFields($headers);

        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }
        $p = $productCodeMap;
        // Pre-fetch all type for mapping
        $allTypes = $this->type->select(['type_id', 'type'])->findAll();
        $typeCodeMap = [];
        foreach ($allTypes as $type) {
            $typeCodeMap[$type['type']] = (int)$type['type_id'];
        }
        $t = $typeCodeMap;
        // Pre-fetch all options for validation
        $allOptions = $this->option->select(['option_id', 'option_code'])->findAll();
        $optionCodeMap = [];
        foreach ($allOptions as $option) {
            $optionCodeMap[$option['option_code']] = (int)$option['option_id'];
        }

        $optionIds = array_column($allOptions, 'option_id');

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);

                if (
                    empty($record['product_code']) || empty($record['option_code']) ||
                    empty($record['type']) || empty($record['name'])
                ) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Required fields: product_code, option_code, type, name']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;
                $optionId = $optionCodeMap[$record['option_code']];
                $typeId = $typeCodeMap[$record['type']] ?? null;
                $name = $record['name'];
                $value = json_encode($record['value']) ?? json_encode([]);
                $metaDescription = $record['meta_description'] ?? null;
                $required = isset($record['required']) ? (int)$record['required'] : 0;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }

                if (!in_array($optionId, $optionIds)) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Option not found: {$optionId}"]
                    ];
                    continue;
                }

                $unique = $productId . '-' . $optionId;
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                $validOptions[] = [
                    'product_id' => $productId,
                    'option_id' => $optionId,
                    'type_id' => $typeId,
                    'name' => $name,
                    'value' => $value,
                    'meta_description' => $metaDescription,
                    'required' => $required
                ];
                $validData[] = [
                    'product_code' => $record['product_code'],
                    'option_code' =>  $record['option_code'], // $record['option_code'],
                    'type' => $record['type'],
                    'name' => $name,
                    'value' => $value,
                    'meta_description' => $metaDescription,
                    'required' => $required
                ];
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validOptions)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productOption->upsert($validOptions, ['product_id', 'option_id']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert product options: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validOptions),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $validData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importProductsTags(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();
        $records = iterator_to_array($records);

        $validTags = [];
        $validData = [];
        $newTaxonomyItemsData = [];
        $newTaxonomyItemsContentData = [];
        $newTagsProductsMap = [];
        $deleteTagsProductsMap = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        $importingProductCodes = array_column($records, 'product_code');

        // Pre-fetch all products for mapping
        $allProducts = $this->model->select(['product_id', 'product_code'])->whereIn('product_code', $importingProductCodes)->limit(0)->findAll();
        $productCodeMap = [];
        foreach ($allProducts as $product) {
            $productCodeMap[$product['product_code']] = (int)$product['product_id'];
        }
        $taxonomyItems = $this->taxonomyItem->where('taxonomy_id', '=', 2)->select(['taxonomy_item_id', 'name'])->findAll();
        $taxonomyCodeMap = [];
        foreach ($taxonomyItems as $item) {
            $taxonomyCodeMap[$item['name']] = (int)$item['taxonomy_item_id'];
        }

        foreach ($records as $offset => $record) {
            try {
                if (empty($record['product_code'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Product code is required']
                    ];
                    continue;
                }

                if (empty($record['tags'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['Tags are required']
                    ];
                    continue;
                }

                $productId = $productCodeMap[$record['product_code']] ?? null;

                if (!$productId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Product not found: {$record['product_code']}"]
                    ];
                    continue;
                }
                // delete tags
                $deleteTags = isset($record['delete_tags']) && !empty($record['delete_tags']) ? explode(',', $record['delete_tags']) : [];
                foreach ($deleteTags as $deleteTag) {
                    $deleteTag = trim($deleteTag);
                    $deleteTaxonomyItemId = $taxonomyCodeMap[$deleteTag] ?? null;
                    if ($deleteTaxonomyItemId) {
                        $deleteTagsProductsMap[] = "'" . $deleteTaxonomyItemId . '-' . $productId . "'";
                        // $deleteTagsProductsMap['product_id'][] = $productId;
                    }
                }

                $tags = explode(',', $record['tags']);
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    $taxonomyItemId = $taxonomyCodeMap[$tag] ?? null;
                    if (!$taxonomyItemId) {
                        $newTagsProductsMap[$tag][] = $productId;
                        //prepare data to create taxonomy item
                        $newTaxonomyItemsData[$tag] = [
                            'taxonomy_id' => 2,
                            'name' => $tag,
                            'taxonomy_item_code' => $this->validateSlug($tag, 'slug', ['name', 'slug']),
                            'sort_order' => 0,
                            'status' => 1
                        ];
                        $newTaxonomyItemsContentData[$tag] = [
                            'language_id' => 1,
                            'name' => $tag,
                            'slug' => $this->validateSlug($tag, 'slug', ['name', 'slug']),
                            'content' => 'product'
                        ];
                        continue;
                    }
                    $validTags[] = [
                        'product_id' => $productId,
                        'taxonomy_item_id' => $taxonomyItemId
                    ];
                    $validData[] = [
                        'product_code' => $record['product_code'],
                        'taxonomy_item_code' => $tag
                    ];
                    $unique = $productId . '-' . $taxonomyItemId;
                    $processed[] = $unique;
                    if (in_array($unique, $processed, true)) {
                        $updated[] = ['row' => $offset + 2, 'data' => $record];
                        continue;
                    }
                }
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        if (!empty($newTaxonomyItemsData)) {
            $newTaxonomyItemsDataForInsert = array_values($newTaxonomyItemsData);
            //First Insert the parrent taxonomy item data
            $this->taxonomyItem->insert($newTaxonomyItemsDataForInsert);
            $taxonomyItemsKeys = array_keys($newTaxonomyItemsData);
            //Now I need to get newly created taxonomy ids 
            $newlyCreatedTaxonomies = $this->taxonomyItem->select(['taxonomy_item_id', 'name'])
                ->whereIn('name', array_keys($newTaxonomyItemsContentData))->findAll(false);

            /**
             [
                ['name' => 'tag1', 'taxonomy_item_id' => 1],
                ['name' => 'tag2', 'taxonomy_item_id' => 2],
                ['name' => 'tag3', 'taxonomy_item_id' => 3],
             ]
             */

            $newlyCreatedTaxonomyMap = [];
            foreach ($newlyCreatedTaxonomies as $taxonomy) {
                $newlyCreatedTaxonomyMap[$taxonomy['name']] = $taxonomy['taxonomy_item_id'];
            }
            /**
             [
                'tag' => 1,
                'tag2' => 2,
                'tag3' => 3,
             ]
             */

            foreach ($newTaxonomyItemsContentData as $tag => $content) {
                $taxonomyItemId = $newlyCreatedTaxonomyMap[$tag] ?? null;
                if (!$taxonomyItemId) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Taxonomy item not found: {$tag}"]
                    ];
                    continue;
                }
                $newTaxonomyItemsContentData[$tag]['taxonomy_item_id'] = $taxonomyItemId;
                $productIds = $newTagsProductsMap[$tag] ?? [];

                foreach ($productIds as $productId) {
                    $validTags[] = [
                        'product_id' => $productId,
                        'taxonomy_item_id' => $taxonomyItemId
                    ];
                    $validData[] = [
                        'product_code' => $record['product_code'],
                        'taxonomy_item_code' => $tag
                    ];
                }
            }
            $newTaxonomyItemsContentDataForInsert = array_values($newTaxonomyItemsContentData);
            $this->taxonomyItemContent->insert($newTaxonomyItemsContentDataForInsert);
        }

        // delete tags products map
        if (count($deleteTagsProductsMap) > 0) {
            // DELETE FROM `product_to_taxonomy_item`
            // WHERE CONCAT(taxonomy_item_id, '-', product_id) IN 
            // ('125-1', '128-8', '126-16', '126-19', '125-57', '126-124');
            $this->productToTaxonomyItem->deleteWhereIn($deleteTagsProductsMap, 'concat(taxonomy_item_id, "-", product_id)');
            // $this->productToTaxonomyItem->deleteWhere(['taxonomy_item_id' => $deleteTagsProductsMap['taxonomy_item_id']]);
        }


        $insertedCount = 0;
        if (!empty($validTags)) {
            try {
                $this->db->beginTransaction();
                $insertedCount = $this->productToTaxonomyItem->upsert($validTags, ['product_id', 'taxonomy_item_id']);
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert product variants: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validTags),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $validData,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importManufacturerVendors(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $records = $reader->getRecords();
        $validData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultManufacturerVendorFields($headers);

        // Pre-fetch all manufacturer codes for mapping and validation
        $allManufacturers = $this->manufacturer->select(['manufacturer_code'])->findAll();
        $manufacturerCodeMap = [];
        foreach ($allManufacturers as $manuf) {
            $manufacturerCodeMap[$manuf['manufacturer_code']] = true;
        }

        // Pre-fetch all vendor codes for mapping and validation
        $allVendors = $this->vendor->select(['vendor_code'])->findAll();
        $vendorCodeMap = [];
        foreach ($allVendors as $vendor) {
            $vendorCodeMap[$vendor['vendor_code']] = true;
        }

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);
                $isManufacturer = !empty($record['manufacturer_code']);
                $codeKey = $isManufacturer ? 'manufacturer_code' : 'vendor_code';
                $codeValue = $record[$codeKey] ?? null;

                if (empty($codeValue)) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ["Missing both manufacturer_code and vendor_code"]
                    ];
                    continue;
                }

                $existingCodes = $isManufacturer ? $manufacturerCodeMap : $vendorCodeMap;
                if (!$this->validateCodeUniqueness($codeValue, $existingCodes, $codeKey)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'message' => "Code {$codeValue} already exists in the database."
                    ];
                    continue;
                }

                // Prevent duplicate unique
                if (in_array($codeValue, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'message' => "Duplicate code {$codeValue} in the current import."
                    ];
                    continue;
                }

                // Slugify name
                $slug = $this->validateSlug($record['name'], 'slug', ['name', 'slug']);
                $imagePath = str_contains($record['image'], '/media/Products/')
                    ? $record['image']
                    : "/media/Products/{$record['image']}";

                $entry = [
                    $codeKey     => $codeValue,
                    'name'       => $record['name'],
                    'slug'       => $slug,
                    'image'      => $imagePath,
                    'sort_order' => $record['sort_order'] ?? 0,
                ];

                $validData[$codeKey][] = $entry;
                $processed[] = $codeValue;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if (!empty($validData)) {
            try {
                $this->db->beginTransaction();
                if (!empty($validData['manufacturer_code'])) {
                    $insertedCount += $this->manufacturer->upsert(
                        $validData['manufacturer_code'],
                        ['manufacturer_code', 'name']
                    );
                }
                if (!empty($validData['vendor_code'])) {
                    $insertedCount += $this->vendor->upsert(
                        $validData['vendor_code'],
                        ['vendor_code', 'name']
                    );
                }
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert manufacturer/vendor: " . $e->getMessage());
            }
        }

        $formStatus = $this->determineFormStatus($validData, $updated, $invalid);
        return [
            'success'          => true,
            'valid_records'    => count($processed),
            'invalid_records'  => count($invalid),
            'updated_records'  => count($updated),
            'inserted_count'   => $insertedCount,
            'valid_data'       => array_merge(
                $validData['manufacturer_code'] ?? [],
                $validData['vendor_code'] ?? []
            ),
            'invalid_data'     => $invalid,
            'updated_data'     => $updated,
            'form_status'      => $formStatus,
        ];
    }

    public function importProductCertificates(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultProductCertificatesFields($headers);
        $requiredFields = [
            'product_id', // product
            'logo', // logo
            'certificate_file', // certificate
            'certificate_type', // certificate type
            'file_format', // file format
        ];
        $records = $reader->getRecords();

        $validData = [];
        $showData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingData = [];
        $showExistingData = [];
        // fetch all product
        $allProducts = $this->model->select(['product_id', 'product_code'])->limit(0)->findAll();
        $productCodeMap = array_column($allProducts, 'product_id', 'product_code');

        // exsiting product certificates
        $existingProductCertificates = $this->productCertificate->select(['product_certificate_id', 'product_id', 'certificate_type'])->limit(0)->findAll(false);
        $existingProductCertificatesMap = [];
        foreach ($existingProductCertificates as $certificate) {
            $existingProductCertificatesMap[$certificate['product_id'] . '-' . $certificate['certificate_type']] = $certificate['product_certificate_id'];
        }

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ProductCertificateDataValidation($record, $requiredFields, array_keys($defaultFields), $existingProductCertificatesMap, $productCodeMap);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->productCertificate;
                    $showExistingData[] = $record;
                } else {
                    $validData[] = (array) $validated->productCertificate;
                    $showData[] = $record;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        try {
            $this->db->beginTransaction();
            if (count($validData) > 0) {
                $this->productCertificate->insert($validData);
            }

            if (count($existingData) > 0) {
                $this->productCertificate->upsert($existingData, ['product_certificate_id']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product certificates: " . $e->getMessage());
        }

        return [
            'success'          => true,
            'valid_records'    => count($showData),
            'invalid_records'  => count($invalid),
            'updated_records'  => count($existingData),
            'inserted_count'   => count($validData),
            'valid_data'       => $showData,
            'invalid_data'     => $invalid,
            'updated_data'     => $showExistingData,
            'existing_data'    => $existingData,
        ];
    }

    public function importProductRelatedProjects(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultProductRelatedProjectsFields($headers);
        $requiredFields = [
            'product_id', // product
            'project_id', // project
            'sort_order', // sort order
        ];
        $records = $reader->getRecords();

        $validData = [];
        $showData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingData = [];
        $showExistingData = [];
        // fetch all product
        $allProducts = $this->model->select(['product_id', 'product_code'])->limit(0)->findAll();
        $productCodeMap = array_column($allProducts, 'product_id', 'product_code');

        // fetch all project
        $allProjects = $this->project->select(['project_id', 'slug'])->limit(0)->findAll();
        $projectSlugMap = array_column($allProjects, 'project_id', 'slug');

        // exsiting product certificates
        $existingProductRelatedProjects = $this->productRelatedProject->select(['project_id', 'product_id'])->limit(0)->findAll(false);
        $existingProductRelatedProjectsMap = [];
        foreach ($existingProductRelatedProjects as $project) {
            $existingProductRelatedProjectsMap[$project['product_id'] . '-' . $project['project_id']] = true;
        }

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ProductRelatedProjectDataValidation($record, $requiredFields, array_keys($defaultFields), $existingProductRelatedProjectsMap, $projectSlugMap, $productCodeMap);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->productRelatedProject;
                    $showExistingData[] = $record;
                } else {
                    $validData[] = (array) $validated->productRelatedProject;
                    $showData[] = $record;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        try {
            $this->db->beginTransaction();
            if (count($validData) > 0) {
                $this->productRelatedProject->upsert($validData, ['product_id', 'project_id']);
            }

            if (count($existingData) > 0) {
                $this->productRelatedProject->upsert($existingData, ['product_id', 'project_id']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product certificates: " . $e->getMessage());
        }

        return [
            'success'          => true,
            'valid_records'    => count($showData),
            'invalid_records'  => count($invalid),
            'updated_records'  => count($existingData),
            'inserted_count'   => count($validData),
            'valid_data'       => $showData,
            'invalid_data'     => $invalid,
            'updated_data'     => $showExistingData,
            'existing_data'    => $existingData,
        ];
    }

    // Product Import end
    // Waypoints / Category Tools
    public function updateWayPoints(array $data): array
    {
        $product_id = $data['product_id'];
        $way_points = $data['way_points'];
        $product = $this->model->where('product_id', '=', $product_id)->first();
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        $data = $product->update(['banner_way_points' => json_encode($way_points)]);
        return [
            'success' => true,
            'message' => 'Way points updated successfully'
        ];
    }

    public function updateCategoryBannerWayPoints(array $data): array
    {
        $model_id = $data['model_id'];
        $model_type = $data['model_type'];
        $way_points = $data['way_points'];

        $model_type = $model_type;
        $query = null;
        if($model_type == 'category') {
           $query = $this->taxonomyItem->where('taxonomy_item_id', '=', $model_id)->first();
        }

        if (!$query) {
            return [
                'success' => false,
                'message' => 'Component not found'
            ];
        }
        $updatedData = $query->update(['banner_way_points' => json_encode($way_points)]);
        return [
            'success' => true,
            'message' => 'Way points updated successfully',
            'data' => $data
        ]; 
    }

    public function updateCategoryOrder(array $data): array
    {
        return [];
    }

    // Product Preparation
    public function prepareProducts(array $products, $groupByCategories = false): array
    {
        $processedProducts = [];
        $categoryProducts = ['sections' => []];

        foreach ($products as $product) {
            if(!isset($product['id'])){
                continue;
            }
            $imageData = json_decode($product['image'] ?? '{}', true);
            $imageUrl = $imageData[0]['objectURL'] ?? '/img/products/default-product.png';
            $baseLink = env('APP_URL');
            $finishImage = json_decode($product['finish_image'] ?? '[]', true);
            $finishImageUrl = '';
            if (count($finishImage) > 0) {
                $finishImageUrl = $finishImage[0]['objectURL'] ?? '';
            }
          
            // if(!isset($processedProducts[$product['id']])){
                $tags = $product['certificate_titles']? explode(',', $product['certificate_titles']) : [];
                if($product['tag_names']){
                    $tags = array_merge($tags, explode(',', $product['tag_names']));
                }
                $finishes = $product['finishes'] ? json_decode($product['finishes'], true) : [];
                // Remove nulls from finishes
                if (is_array($finishes)) {
                    //IN futrue if need finish image or color need to process here
                    $finishes = array_values(array_filter($finishes, fn($item) => $item !== null));
                }
                $productData = [
                    'id' => $product['id'],
                    'model' => 'product',
                    'name' => isset($product['title']) && $product['title'] != null && empty($product['title']) ? ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))) : $product['title'],
                    'title' => isset($product['title']) && $product['title'] != null && empty($product['title']) ? ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))) : $product['title'],
                    'image' => $imageUrl,
                    'description' => $product['product_tag_line'] ?? $product['product_description'] ?? 'A comfortable and versatile seating option for various environments.',
                    'meta_description' => $product['product_meta_description'] ?? '',
                    'meta_keywords' => $product['product_meta_keywords'] ?? '',
                    'slug' => $product['slug'],
                    'category_slug' => $product['category_slug'],
                    'href' => isset($product['category_slug']) && isset($product['slug']) ?  $baseLink.'/products/'.$product['category_slug'].'/'.$product['slug'] : '',
                    'link' => isset($product['category_slug']) && isset($product['slug']) ?  $baseLink.'/products/'.$product['category_slug'].'/'.$product['slug'] : '',
                    'url' => isset($product['category_slug']) && isset($product['slug']) ?  $baseLink.'/products/'.$product['category_slug'].'/'.$product['slug'] : '',
                    'product_url' => isset($product['category_slug']) && isset($product['slug']) ?  '/products/'.$product['category_slug'].'/'.$product['slug'] : '',
                    'tags' => $tags,
                    'finishes' => $finishes,
                    'sort_order' => $product['product_sort_order'] ?? 0,
                ];
                $processedProducts[] = $productData;
            // }
            if($groupByCategories){
                if (!isset($categoryProducts['sections'][$product['category_slug']])) {
                    $categoryProducts['sections'][$product['category_slug']] = [
                        'title'         => $product['category_name'] ?? '',
                        'sort_order'    => $product['category_sort_order'] ?? 0,
                        'subtitle'      => $product['category_content'] ?? '',
                        'link'          => $product['products_link'] ?? '',
                        'category_link' => $product['category_link'] ?? '',
                        'items'         => [$productData]
                    ];
                }else{
                    $categoryProducts['sections'][$product['category_slug']]['items'][] = $productData;
                }
            }
        }
        if($groupByCategories){
            foreach ($categoryProducts['sections'] as &$section) {
                usort($section['items'], function($c, $d) {
                    return $c['sort_order'] <=> $d['sort_order'];
                });
            }
            unset($section);
            uasort($categoryProducts['sections'], function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            return $categoryProducts;
        }else{
            usort($processedProducts, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            return $processedProducts;
        }
    }


    public function getProductImages($productId): array
    {
        return $this->productImage->where('product_id', '=', $productId)->findAll();
    }

    public function getProductMetadata($productId): array
    {
        $metadata = ProductData::getDefaultMetadata();
        if (!isset($productId)) {
            return $metadata;
        }

        $rows = $this->productMetadata
            ->select(['namespace', 'key', 'value'])
            ->where('product_id', '=', $productId)
            ->findAll(false);

        foreach ($rows as $row) {
            if (!isset($row['namespace'], $row['key'])) {
                continue;
            }
            if (!isset($metadata[$row['namespace']])) {
                $metadata[$row['namespace']] = [];
            }
            $metadata[$row['namespace']][$row['key']] = $row['value'] ?? '';
        }

        return $metadata;
    }

    public function getProductAttributes(int $productId, int $languageId = 1): array
    {
        $this->productAttribute->joins = [];

        $productAttributes = $this->productAttribute
            ->join('attribute', 'attribute.attribute_id', '=', 'product_attribute.attribute_id')
            ->join('attribute_content', 'attribute_content.attribute_id', '=', 'attribute.attribute_id')
            ->join('attribute_group', 'attribute_group.attribute_group_id', '=', 'attribute.attribute_group_id')
            ->join('attribute_group_content', 'attribute_group_content.attribute_group_id', '=', 'attribute_group.attribute_group_id')
            ->where('product_attribute.product_id', '=', $productId)
            ->where('attribute_content.language_id', '=', $languageId)
            ->where('attribute_group_content.language_id', '=', $languageId)
            ->select([
                'attribute.attribute_id',
                'attribute.sort_order',
                'attribute_content.name',
                'product_attribute.value',
                'attribute_group.attribute_group_id',
                'attribute_group_content.name AS description'
            ])
            ->findAll(false);

        return $productAttributes;
    }

    public function getProductVariants(int $productId): array
    {
        $this->productVariant->joins = [];

        $variants = $this->productVariant
            ->join('product', 'product.product_id', '=', 'product_variant.product_variant_id')
            ->where('product_variant.product_id', '=', $productId)
            ->select([
                'product_variant.product_variant_id',
                'product.product_id',
                'product.sku',
                'product.stock_quantity AS stock',
                'product.price AS variant_price',
                'product.product_code',
                'product.description AS variant',
                'product.price AS product_price'
            ])
            ->findAll(false);

        return array_map(function ($v) {
            return [
                'product_variant_id' => $v['product_variant_id'] ?? 0,
                'variant'           => $v['product_code'] ?? $v['sku'] ?? 'Unnamed',
                'variant_price'     => $v['variant_price'] ?? 0,
                'product_price'     => $v['product_price'] ?? 0,
                'stock'             => $v['stock'] ?? 0,
                'sku'               => $v['sku'] ?? 'N/A',
                'weight'            => $v['weight'] ?? 0,
                'barcode'           => $v['barcode'] ?? 'N/A',
                'product_id'        => $v['product_id'] ?? 0,
                'product_code'      => $v['product_code'] ?? 'N/A'
            ];
        }, $variants);
    }

    public function getVariantsByProductId(int $product_id): array
    {
        $this->productVariant->clearQuery();
        $variants = $this->productVariant
            // ->whereIn('product_variant_id', [761,762,763,764,765,766,767, 768,769,770,771]);
            ->where('product_id', '=', $product_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->orderBy('is_default', 'DESC')
            ->limit(0);
        $variants = $variants->findAll(false);

        $variantIds = array_column($variants, 'product_variant_id');

        $productOptionGroups = $this->productOptionGroup
            ->whereIn('product_variant_id', $variantIds)
            ->where('product_id', '=', $product_id)
            ->limit(0)
            ->findAll(false);

        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');
        $productOptions = $this->productOption
            ->join('`type`', 'product_option.type_id', '=', '`type`.type_id')
            ->select(['product_option.*', '`type`.type'])
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->where('product_id', '=', $product_id)
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $productOptionGroup) {
            $productOptionGroup['productOptions'] = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];
            $formattedProductOptionGroups[$productOptionGroup['product_variant_id']][] = $productOptionGroup;
        }
        foreach ($variants as &$variant) {
            if(isset($variant['image']) && !empty($variant['image'])){
                $variant['image'] = json_decode($variant['image'], true);
            }
            $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];
        }

        return $variants;
    }

    public function getProductDigitalAssets(int $productId, int $languageId = 1): array
    {
        $this->digitalAsset->joins = [];

        $digitalAssets = $this->digitalAsset
            ->join('digital_asset_content', 'digital_asset_content.digital_asset_id', '=', 'digital_asset.digital_asset_id')
            ->join('product_to_digital_asset', 'product_to_digital_asset.digital_asset_id', '=', 'digital_asset.digital_asset_id')
            ->where('product_to_digital_asset.product_id', '=', $productId)
            ->where('digital_asset_content.language_id', '=', $languageId)
            ->select([
                'digital_asset.digital_asset_id',
                'digital_asset_content.name'
            ])
            ->findAll(false);

        return $digitalAssets;
    }

    public function getProductOptions(int $productId): array
    {
        $this->productOption->joins = [];

        // $productOptions = $this->productOption
        //     ->join('option', 'option.option_id', '=', 'product_option.product_option_id')
        //     ->where('product_option.product_id', '=', $productId)
        //     ->select([
        //         'product_option.product_option_id',
        //         'product_option.product_id',
        //         'product_option.option_id',
        //         'product_option.name',
        //         'product_option.value',
        //         'option.type_id',
        //         'product_option.meta_description as metadata',
        //         'JSON_OBJECT("type_id", `option`.type_id, "type", `option`.type) as type'
        //     ])
        //     ->findAll(false);
        // return array_map(function ($option) {
        //     $option['type'] = json_decode($option['type']);
        //     // if (!isset($option['type']['type_id']) || $option['type']['type_id'] === null) {
        //     //     $option['type']['type_id'] = 4;
        //     // }
        //     return $option;
        // }, $productOptions);
        return [];
    }

    public function getFamilyProducts_OLD(int $productId, string $slug): array
    {
        $this->model->clearQuery();
        $familyProducts = $this->model
        ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->where('product_family_code', '=', $slug)
        ->where('product.product_id', '!=', $productId)
        ->select([
            'product.product_id',
            'product.product_code',
            'product.image',
            'product_content.name',
            'product.description',
        ])
        ->findAll(false); // return as array
        $baseUrl = env('APP_URL');
        foreach ($familyProducts as &$product) {
            $images = json_decode($product['image'], true);
            $product['image'] = $images[0]['image'] ?? null ? $baseUrl . $images[0]['image'] : null;
            // unset($product['image']); // optional
            $product['description'] = strlen($product['description']) > 20 ? substr($product['description'], 0, 20) . '...' : $product['description'];
        }

        return $familyProducts;
    }

    public function getFamilyProducts(int $productId, $slug): array
    {
        if (is_array($slug)) {
            $slug = $slug[0] ?? '';
        }

        if (empty($slug)) {
            return [];
        }

        $this->model->clearQuery();

        $familyProducts = $this->model
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_family_code', '=', (string)$slug)
            ->where('product.product_id', '!=', $productId)
            ->select([
                'product.product_id',
                'product.product_code',
                'product.image',
                'product_content.name',
                'product.description',
            ])
            ->findAll(false);

        $baseUrl = rtrim(env('APP_URL'), '/');

        foreach ($familyProducts as &$product) {
            $images = json_decode($product['image'] ?? '[]', true);

            // $product['image'] = isset($images[0]['image'])
            //     ? $baseUrl . $images[0]['image']
            //     : null;

            $image = isset($images[0]['objectURL']) ? $baseUrl . $images[0]['objectURL'] :  null;
            $product['image'] = $image;

            $product['description'] = strlen($product['description'] ?? '') > 20
                ? substr($product['description'], 0, 20) . '...'
                : ($product['description'] ?? '');
        }

        return $familyProducts;
    }

    public function getProductRelated(int $productId): array
    {
        $this->productRelated->joins = [];

        $relatedProducts = $this->productRelated
            ->join('product', 'product_related.product_related_id', '=', 'product.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_related.product_id', '=', $productId)
            ->select([
                'product.product_id',
                'product.product_code',
                'product.image',
                'product_content.name',
                'product.description',
            ])
            ->findAll(false); // return as array

        $baseUrl = env('APP_URL');
        foreach ($relatedProducts as &$product) {

            $images = !empty($product['image'])
                ? json_decode($product['image'], true)
                : [];
        
            $product['image'] = !empty($images[0]['image'])
                ? $baseUrl . $images[0]['image']
                : null;
        
            $description = (string)($product['description'] ?? '');

            $product['description'] = mb_strlen($description) > 20
                ? mb_substr($description, 0, 20) . '...'
                : $description;
        }

        return $relatedProducts;
    }

    public function getProjectRelated(int $productId): array
    {
        $this->productRelatedProject->joins = [];

        $relatedProducts = $this->productRelatedProject
            ->join('project', 'product_related_project.project_id', '=', 'project.project_id')
            ->where('product_related_project.product_id', '=', $productId)
            ->select([
                'project.project_id',
                'project.image',
                'project.name',
            ])
            ->orderBy('product_related_project.sort_order', 'ASC')
            ->findAll(false); // return as array
        $baseUrl = env('APP_URL');
        foreach ($relatedProducts as &$product) {
            $images = json_decode($product['image'] ?? '', true);
            $product['image'] = isset($images[0]['image']) ? $baseUrl . $images[0]['image'] : null;
        }

        return $relatedProducts;
    }
    public function getProductResource(int $productId): array
    {
        $this->productResource->joins = [];
    
        $relatedResources = $this->productResource
            ->join('design_resource', 'design_resource.design_resource_id', '=', 'product_resource.design_resource_id')
            ->where('product_resource.product_id', '=', $productId)
            ->select([
                'product_resource.design_resource_id',
                'design_resource.title as name',
                'design_resource.resource_type as type',
            ])
            ->findAll(false); // return as array
        // $baseUrl = env('APP_URL');
        // foreach ($relatedResources as &$product) {
        //     $images = json_decode($product['image'], true);
        //     $product['image'] = $images[0]['image'] ?? null ? $baseUrl . $images[0]['image'] : null;
        // }

        return $relatedResources;
    }

    public function getProductTags($productId): array
    {
        $this->taxonomyItem->joins = [];
        $tags = $this->taxonomyItem
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('product_to_taxonomy_item.product_id', '=', $productId)
            ->where('taxonomy.type', '=', 'tags')
            ->where('taxonomy.post_type', '=', 'product')
            ->where('taxonomy.site_id', '=', 1)
            ->where('taxonomy_item_content.language_id', '=', 1)
            ->select(['taxonomy_item.taxonomy_item_id', 'taxonomy_item_content.name'])
            ->findAll();
        return $tags;
    }


    public function prepareProduct(?array $product): Product|array
    {
        if(!$product) return [];

        $productData = [
            'product_id' => $product['product_id'] ?? 0,
            'id' => $product['product_id'] ?? 0,
            'name' => $product['name'] ?? '',
            'title' => $product['title'] ?? '',
            'image' => $this->getImageUrl($product['image']),

            'description' => $product['description'] ?? '',
            'specifications' => $product['specifications'] ?? '',
            'warranty_period' => $product['warranty_period'] ?? '',

            'product_code' => $product['product_code'] ?? '',
            'product_family_code' => $product['product_family_code'] ?? '',
            'factory_code' => $product['factory_code'] ?? '',

            'sku' => $product['sku'] ?? '',
            'isbn' => $product['isbn'] ?? '',
            'barcode' => $product['barcode'] ?? '',

            'track_stock' => $product['track_stock'] ?? 0,
            'stock_quantity' => $product['stock_quantity'] ?? 0,
            'stock_status_id' => $product['stock_status_id'] ?? 0,

            'lead_days' => $product['lead_days'] ?? 0,
            'melbourne_lead_days' => $product['melbourne_lead_days'] ?? 0,
            'safety_stock' => $product['safety_stock'] ?? 0,
            'qty_alert' => $product['qty_alert'] ?? 0,

            'material' => $product['material'] ?? '',
            'weight' => $product['weight'] ?? 0,
            'length' => $product['length'] ?? 0,
            'width' => $product['width'] ?? 0,
            'height' => $product['height'] ?? 0,
            'depth' => $product['depth'] ?? 0,

            'price' => $product['price'] ?? 0,
            'old_price' => $product['old_price'] ?? 0,

            'min_order_quantity' => $product['min_order_quantity'] ?? 1,
            'out_of_stock_status' => $product['out_of_stock_status'] ?? '',

            'carton_qm' => $product['carton_qm'] ?? 0,
            'size' => $product['size'] ?? null,

            'carton_width' => $product['carton_width'] ?? 0,
            'carton_depth' => $product['carton_depth'] ?? 0,
            'carton_height' => $product['carton_height'] ?? 0,

            'specifications_image' => $this->getImageUrl($product['specifications_image']),
            'banner_image' => $this->getImageUrl($product['banner_image']),
            'video_link' => $product['video_link'] ?? '',
    
            'image_thumb' => $this->getImageUrl($product['image_thumb']),
    
            'main_image_one' => $this->getImageUrl($product['main_image_one']),
            'main_image_one_title' => $product['main_image_one_title'] ?? '',
            'main_image_one_description' => $product['main_image_one_description'] ?? '',
    
            'main_image_two' => $this->getImageUrl($product['main_image_two']),
            'main_image_two_title' => $product['main_image_two_title'] ?? '',
            'main_image_two_description' => $product['main_image_two_description'] ?? '',
    
            'feature_description' => $product['feature_description'] ?? '',
    
            'feature_image_one' => $this->getImageUrl($product['feature_image_one']),
            'feature_image_one_title' => $product['feature_image_one_title'] ?? '',
            'feature_image_one_description' => $product['feature_image_one_description'] ?? '',
    
            'feature_image_two' => $this->getImageUrl($product['feature_image_two']),
            'feature_image_two_title' => $product['feature_image_two_title'] ?? '',
            'feature_image_two_description' => $product['feature_image_two_description'] ?? '',
    
            'feature_image_three' => $this->getImageUrl($product['feature_image_three']),
            'feature_image_three_title' => $product['feature_image_three_title'] ?? '',
            'feature_image_three_description' => $product['feature_image_three_description'] ?? '',
        ];

        return $productData;
    }

    /** 
     * -------------------------------------------------------------------------------
     * all private methods started from here
     * -------------------------------------------------------------------------------
     */

     private function getProductQuery(string|null $categorySlug = null){
        $categorySlugCondition = "category_pti.taxonomy_item_id";
        if($categorySlug){
            $categorySlugCondition .= " AND category_tic.slug = '{$categorySlug}'";
        }
        //Aliasses 
        // product_to_taxonomy_item -> category_pti
        // taxonomy_item -> category_ti
        // taxonomy_item_content -> category_tic
        // product_certificate -> product_certificate
        // product_to_taxonomy_item -> tag_pti
        // taxonomy_item -> tag_ti
        // taxonomy_item_content -> tag_tic - tags
        // product_resource -> product_resource
        // design_resource -> design_resource

        $query = $this->model
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            //For Category filter
            ->join('`product_to_taxonomy_item` as category_pti', 'category_pti.product_id', '=', 'product.product_id', "INNER")
            ->join('`taxonomy_item` as category_ti', 'category_ti.taxonomy_item_id', '=', 'category_pti.taxonomy_item_id AND category_ti.taxonomy_id = 1', "INNER")
            ->join('`taxonomy_item_content` as category_tic', 'category_tic.taxonomy_item_id', '=', $categorySlugCondition, "INNER")
            //For Certificates
            ->join('product_certificate', 'product_certificate.product_id', '=', 'product.product_id')
            //For Tags 
            ->join('`product_to_taxonomy_item` as tag_pti', 'tag_pti.product_id', '=', 'product.product_id')
            ->join('`taxonomy_item` as tag_ti', 'tag_ti.taxonomy_item_id', '=', 'tag_pti.taxonomy_item_id AND tag_ti.taxonomy_id = 2')
            ->join('`taxonomy_item_content` as tag_tic', 'tag_tic.taxonomy_item_id', '=', 'tag_ti.taxonomy_item_id')
            //For finishes
            ->join('`product_resource`', 'product_resource.product_id', '=', 'product.product_id and product_resource.resource_type = "finishes"')
            ->join('`design_resource`', 'design_resource.design_resource_id', '=', 'product_resource.design_resource_id')

            ->where('product.status', '=', 1)
            // ->groupBy('product.product_id')
            ->select([
                "product.product_id as id",
                "product.product_id as product_id",
                "product.model as model",
                "product.sku as sku",
                "product.barcode as barcode",
                "product.track_stock as track_stock",
                "product.stock_quantity as stock_quantity",
                "product.weight as weight",
                "product_content.name",
                "product_content.title",
                "product_content.slug",
                "product_content.slug as product_slug",
                "product.image_thumb as image",
                "product.description as product_description",
                "product_content.tag_line as product_tag_line",
                "product_content.meta_description as product_meta_description",
                "product_content.meta_keywords as product_meta_keywords",
                "category_pti.sort_order as product_sort_order",
                 // Catagory
                "category_tic.slug as category_slug",
                "category_tic.name as category_name",
                "category_ti.sort_order as category_sort_order",
                "category_tic.name as category",
                "category_tic.link as category_link",
                "category_tic.products_link",
                "category_tic.content as category_content",
                // Tags
                "GROUP_CONCAT(DISTINCT tag_tic.taxonomy_item_id ORDER BY tag_tic.name) as tag_ids",
                "GROUP_CONCAT(DISTINCT tag_tic.name ORDER BY tag_tic.name SEPARATOR ', ') as tag_names",
                // Certificates
                "GROUP_CONCAT(DISTINCT product_certificate.product_certificate_id ORDER BY product_certificate.product_certificate_id SEPARATOR ', ') as certificate_ids",
                "GROUP_CONCAT(DISTINCT product_certificate.title ORDER BY product_certificate.product_certificate_id SEPARATOR ', ') as certificate_titles",
                // Finishes
                "COALESCE(JSON_ARRAYAGG(
                    IF(design_resource.design_resource_id IS NOT NULL,
                        JSON_OBJECT('finish_name', design_resource.title, 'finish_image', design_resource.img, 'finish_color', design_resource.hex_value),
                        NULL)
                ), JSON_ARRAY()) as finishes",
            ]);
            return $query;
    }

    /**
     * Split specifications into list items. When multiple &lt;p&gt; tags are present,
     * extracts their text via regex; otherwise splits on PHP_EOL.
     *
     * @return list<string>
     */
    private function parseProductSpecifications(?string $specifications): array
    {
        $specifications = (string) ($specifications ?? '');
        if ($specifications === '') {
            return [];
        }

        if (preg_match_all('/<p(?:\s[^>]*)?>(.*?)<\/p>/is', $specifications, $matches) && count($matches[1]) > 1) {
            $items = [];
            foreach ($matches[1] as $innerHtml) {
                $text = trim(html_entity_decode(strip_tags($innerHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if ($text !== '') {
                    $items[] = $text;
                }
            }
            if ($items !== []) {
                return $items;
            }
        }

        return explode(PHP_EOL, $specifications);
    }

    private function getImageUrl($image): ?string
    {
        $imageData = json_decode($image ?? '[]', true);
        if(isset($imageData[0]['objectURL'])){
            return $imageData[0]['objectURL'];
        }
        return '';
    }

    private function prepareImageFormat(array $data, ?int $parent_id = null): array
    {
        $imageData = [];
        // $config = app('config');
        // $imageServer = $config['APP_URL'];
        $imageLinks = [];
        foreach ($data as $image) {
            $img = [];
            $img['product_id'] = $parent_id ?? null;
            $img['image_link'] = $image['image'];
            $imageLinks[] = $image['image'];
            $img['file'] = [
                'name' => $image['name'] ?? '',
                'size' => $image['size'] ?? 0,
                'type' => $image['type'] ?? '',
                'error' => 0,
                'tmp_name' => $image['path'] ?? '',
                'full_path' => $image['name'] ?? '',
            ];
            $img['name'] = $image['name'];
            $img['size'] = $image['size'];
            $img['type'] = $image['type'];
            $img['image'] = $image['image'];
            $img['objectURL'] = $image['objectURL'];
            $img['image'] = [
                'name' => $image['name'],
                'objectURL' => $image['objectURL'],
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => $image['path'],
                'status' => $image['status']
            ];
            $img['sort_order'] = 0;
            $img['status'] = $image['status'];
            $img['way_points'] = json_encode([]);
            $imageData[] = $img;
        }
        return [$imageData, $imageLinks];
    }
    
    /**
     * Recursively builds a hierarchical tree structure from flat category array
     * 
     * @param array $categories Flat array of categories
     * @param mixed $parentId Parent ID to start building from (null for root)
     * @param string $keyPrefix Prefix for generating hierarchical keys
     * @return array Hierarchical tree structure
     */
    private function buildCategoryTree(array $categories, $parentId = null, string $keyPrefix = ''): array
    {
        $branch = [];
        $counter = 1;

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $currentKey = $keyPrefix ? $keyPrefix . '-' . $counter : (string)$counter;

                $image = null;
                if (!empty($category['image']) && is_string($category['image'])) {
                    $image = json_decode($category['image'], true);
                }
                $slider_image = null;
                if (!empty($category['slider_image']) && is_string($category['slider_image'])) {
                    $slider_image = json_decode($category['slider_image'], true);
                }
                $banner_way_points = isset($category['banner_way_points']) ? json_decode($category['banner_way_points'], true) : [];
              

                $node = [
                    'key' => $currentKey,
                    'label' => $category['name'],
                    'label_name' => $category['label_name'],
                    'id' => $category['taxonomy_item_id'],
                    'parent_id' => $category['parent_id'],
                    'banner_way_points' => $banner_way_points,
                    'products_link' => $category['products_link'],
                    'link' => $category['link'],
                    'slug' => $category['slug'],
                    'content' => $category['content'],
                    'meta_title' => $category['meta_title'],
                    'meta_description' => $category['meta_description'],
                    'meta_keywords' => $category['meta_keywords'],
                    'image' => $image,
                    'slider_image' => $slider_image,
                    'sort_order' => $category['sort_order'],
                    'status' => $category['status'],

                ];

                // Recursively get children
                $children = $this->buildCategoryTree($categories, $category['taxonomy_item_id'], $currentKey);
                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
                $counter++;
            }
        }

        usort($branch, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
        return $branch;
    }

    private function getProductComponentData(array $params)
    {
        $model = 'product';
        $results = [];
        if (isset($params['model']) && $model == $params['model']) {
            $query = $this->model;
            // if(isset($params['product_id'])){
            //     $query->where('product.product_id', '=', $params['product_id']);
            // }else{
            //     $query->where('product.product_code', '=', $params['slug']);
            // }
            $query->where('product.product_code', '=', $params['slug']);
            if (isset($params['joins']) && is_array($params['joins'])) {
                foreach ($params['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            $params['fields'][] = 'product.product_code as slug';
            $query->select($params['fields'])
                ->orderBy('product.product_id', 'ASC');

            $results = $query->findAll(false);
            if ($results) {
                $results = (array) $results[0] ?? [];
            } else {
                $results = [];
            }
        }
        return $results;
    }

    private function prepareFeatureResult(array $results, string $prefix = 'feature_image', int $count = 3): array
    {
        $items = [];
    
        $serial = [1 => 'one', 2 => 'two', 3 => 'three'];
    
        $class = [
            1 => 'grid-col-span-7',
            2 => 'grid-col-span-6',
            3 => 'grid-col-span-7'
        ];
    
        if($count > 0){
            for ($i = 1; $i <= $count; $i++) {
                $key = $serial[$i];
                $imageData = json_decode($results["{$prefix}_{$key}"] ?? '[]', true);
                $imageUrl = null;
        
                if (!empty($imageData) && is_array($imageData)) {
                    $imageUrl = $imageData[0]['objectURL'] ?? $imageData[0]['image'] ?? null;
                }
        
                $title = $results["{$prefix}_{$key}_title"] ?? null;
                $description = $results["{$prefix}_{$key}_description"] ?? null;
        
                if ($imageUrl || $title || $description) {
                    $items[] = [
                        'id' => "{$prefix}-{$i}",
                        'title' => $title,
                        'description' => $description,
                        'img' => $imageUrl,
                        'heading' => $title,
                        'link' => '#',
                        'class' => "th-masonry-grid-item {$class[$i]}",
                        'des' => $description
                    ];
                }
                    
            }
        }else{
            $specificationImage = json_decode($results["specifications_image"] ?? '[]', true);
            $specificationImageUrl = isset($specificationImage[0]['objectURL']) ? $specificationImage[0]['objectURL'] : '';
                $specifications = $this->parseProductSpecifications($results['specifications'] ?? null);
            $items[] = [
                'id' => "specifications",
                'title' => $results["specifications_title"] ?? null,
                'specifications' => $specifications,
                'img' => $specificationImageUrl,
                'heading' => $results["specifications_title"] ?? null,
            ];
        }
    
        return $items;
    }

    private function validateCodeUniqueness(string $code, array $existingCodes, string $codeType): bool
    {
        return !isset($existingCodes[$code]);
    }

    private function determineFormStatus(array $validData, array $updated, array $invalid): string
    {
        $hasManufacturer = isset($validData['manufacturer_code'])
            || (isset($updated[0]['data']['manufacturer_code']))
            || (isset($invalid[0]['data']['manufacturer_code']));

        return $hasManufacturer ? 'manufacturer' : 'vendor';
    }

    private function validateSlug($value, string $field, array $requiredFields = []): ?string
    {
        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }

        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);

        if (in_array($field, $requiredFields) && empty($value)) {
            $this->addError($field, 'is mandatory');
            return null;
        }

        return substr(trim($slug, '-'), 0, 191);
    }

    /**
     * Process batch product-to-category relationships.
     * Ensures each imported product is properly linked to its existing categories.
     *
     * @param array $categoryRelationships   List of relationships generated from validation.
     * @param array $productIdsMap           Product code → product ID mapping (from insertion).
     * @param array $taxonomyNameToIdMapByCategory    Category name → taxonomy_item_id mapping (from taxonomy).
     *
     * @throws Exception If any database operation fails.
     */

    private function processBatchProductToRelationships(array $productToRelationships, array $productIdsMap = [], array $taxonomyNameToIdMapByCategory = []): array 
    {
        // If there are no category relationships to process, exit early.
        if (empty($productToRelationships)) {
            return [];
        }

        // Initialize containers for batch inserts and tracking.
        $validCategories = [];          // Stores validated product-category (product_to_taxonomy_item) pairs ready to insert.
        $validTags = [];                // Stores validated product-tags (	product_to_taxonomy_item) pairs ready to insert.
        $newCategoriesData = [];       // For categories that don't yet exist (currently disabled).
        $newCategoryProductsMap = [];  // Keeps track of which products belong to new categories.
        $processed = [];               // Tracks unique (product_id-category_id) pairs to prevent duplicates.
        $processedToTag = [];          // Tracks unique (product_id-tag_id) pairs to prevent duplicates.

        try {
            // Start a database transaction for safe batch operations.
            $this->db->beginTransaction();

            // Step 2: Process each product–category relationship.
            foreach ($productToRelationships as $relationship) {
                // get product id from productIdsMap
                $productId = $relationship['product_id'] ?? null;
                // Skip if no valid product_id could be determined.
                if (!$productId) {
                    continue;
                }

                // Step 3: Handle existing category data (validated categories from CSV).
                // $existingCategories = $relationship['categories_data'];
                if (!empty($relationship['categories_data'])) {
                    foreach ($relationship['categories_data'] as $categoryData) {
                        $categoryId = $categoryData['category_id'] ?? null;

                        // Skip if no category_id is present.
                        if ($categoryId) {
                            // Create a unique composite key (product_id-category_id)
                            // to avoid inserting duplicate links.
                            $unique = $productId . '-' . $categoryId;
                            if (!in_array($unique, $processed, true)) {
                                $validCategories[] = [
                                    'product_id' => $productId,
                                    'taxonomy_item_id' => $categoryId
                                ];
                                $processed[] = $unique;
                            }
                        }
                    }
                }

                if (!empty($relationship['tags_data'])) {
                    foreach ($relationship['tags_data'] as $tag) {
                        $tagId = $tag['tag_id'] ?? null;

                        // Skip if no tag_id is present.
                        if ($tagId) {
                            // Create a unique composite key (product_id-tag_id)
                            // to avoid inserting duplicate links. unique = 103-113
                            $uniqueToTag = $productId . '-' . $tagId;
                            // unique check with prepare valid tags data in the array [[], [],[]]
                            if (!in_array($uniqueToTag, $processedToTag, true)) {
                                $validTags[] = [
                                    'product_id' => $productId,
                                    'taxonomy_item_id' => $tagId
                                ];
                                $processedToTag[] = $uniqueToTag;
                            }
                        }
                    }
                }
            }
          

            // Step 6: Insert all valid product–category relationships.
            // Insert all category relationships
            if (!empty($validCategories)) {
                // Use UPSERT to prevent duplicate key errors on repeated imports.
                $this->productToTaxonomyItem->upsert(
                    $validCategories,
                    ['product_id', 'taxonomy_item_id']
                );
            }
            // insert all tags relationships with product table
            if (!empty($validTags)) {
                // Use UPSERT to prevent duplicate key errors on repeated imports.
                $this->productToTaxonomyItem->upsert(
                    $validTags,
                    ['product_id', 'taxonomy_item_id']
                );
            }

            // Commit the transaction — everything succeeded.
            $this->db->commit();
        } catch (Exception $e) {
            // Roll back all database changes on any failure.
            $this->db->rollBack();
            // Rethrow exception with contextual message.
            throw new Exception("Failed to process category relationships: " . $e->getMessage());
        }
        return ['valid_categories' => $validCategories, 'valid_tags' => $validTags];
        // return [
        //     'categories_summary' => $validCategories, // $categories,
        //     'tag_summary' => [], // $tags,
        // ];
    }

    /**
     * Generate a key-value map (e.g., manufacturer_code => manufacturer_id)
     * for fast lookup during import.
     */
    private function getIdMap($model, string $keyColumn, string $valueColumn): array
    {
        $items = $model->select([$valueColumn, $keyColumn])->findAll(false);
        return array_column($items, $valueColumn, $keyColumn);
    }

    /**
     * Merge a record with default fields unless it already includes a product_id.
     * Ensures consistency for new products while leaving existing ones untouched.
     */
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['product_id']) && $record['product_id'] ? $record : array_merge($defaultFields, $record);
    }

    private function insertMedia(array $mediaData): array
    {
        $productCodes = array_column($mediaData, 'meta');
        $this->media->upsert($mediaData, ['path']);
        $mediaIds = $this->media->whereIn('meta', $productCodes)->select(['meta', 'media_id'])->limit(0)->findAll();
        $mediaIdsMap = array_column($mediaIds, 'media_id', 'meta');
        return $mediaIdsMap;
    }

    private function insertProductsAndContents(array $products, array $contents, array $mediaIdsMap = []): array
    {
        $insertedCount = 0;
        $insertedContentCount = 0;
        $codeToId = [];

        if (empty($products)) {
            return ['inserted_count' => 0, 'inserted_content_count' => 0];
        }
        $products = array_values($products);

        try {
            $this->db->beginTransaction();

            // STEP 1: Pre-fetch existing product IDs
            $productCodes = array_column($products, 'product_code');
            $this->model->clearQuery();
            $existingProducts = $this->model->select(['product_id', 'product_code'])
                ->whereIn('product_code', $productCodes)
                ->limit(0)
                ->findAll(false);

            // Build existing mapping
            foreach ($existingProducts as $row) {
                $codeToId[$row['product_code']] = (int)$row['product_id'];
            }

            // STEP 2: Separate new vs existing products
            $newProducts = [];
            $existingProductCodes = array_keys($codeToId);

            foreach ($products as $product) {
                if(isset($mediaIdsMap[$product['product_code']])){
                    $product['media_id'] = $mediaIdsMap[$product['product_code']];
                }else{
                    $product['media_id'] = null;
                }
                if (!in_array($product['product_code'], $existingProductCodes)) {
                    $newProducts[] = $product;
                }
            }

            // STEP 3: Insert only new products
            if (!empty($newProducts)) {
                $this->model->insert($newProducts);

                // STEP 4: Get IDs for newly inserted products
                // This should work because we're querying after the insert within the same transaction
                $newProductCodes = array_column($newProducts, 'product_code');
                $newProductIds = $this->model->select(['product_id', 'product_code'])
                    ->whereIn('product_code', $newProductCodes)
                    ->limit(0)
                    ->findAll(false);

                foreach ($newProductIds as $row) {
                    $codeToId[$row['product_code']] = (int)$row['product_id'];
                }
            }

            // STEP 5: Update existing products
            $updateProducts = array_filter($products, function ($product) use ($existingProductCodes) {
                return in_array($product['product_code'], $existingProductCodes);
            });
            $updateProducts = array_values($updateProducts);

            if (!empty($updateProducts)) {
                // Ensure product_type_id is set for all update products (required field)
                foreach ($updateProducts as &$product) {
                    if (!isset($product['product_type_id']) || $product['product_type_id'] === null) {
                        $product['product_type_id'] = 1;
                    }
                }
                unset($product); // Unset reference to avoid issues
                $this->model->upsert($updateProducts, ['product_code']);
            }

            $insertedCount = count($newProducts) + count($updateProducts);

            // STEP 6: Process contents with all product IDs now available
            if (!empty($contents)) {
                $finalContentData = [];
                foreach ($contents as $index => $content) {
                    $productCode = $products[$index]['product_code'] ?? null;
                    if ($productCode && isset($codeToId[$productCode])) {
                        $content['product_id'] = $codeToId[$productCode];
                        $finalContentData[] = $content;
                    }
                }

                if (!empty($finalContentData)) {
                    $insertedContentCount = $this->productContent->upsert($finalContentData, ['product_id', 'language_id']);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert products: " . $e->getMessage());
        }

        return ['inserted_count' => $insertedCount, 'inserted_content_count' => $insertedContentCount, 'productIds' => $codeToId];
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Get categories for validation (category_name => category_id mapping)
     */
    private function getCategoriesForValidation(): array
    {
        try {
            // Initialize an empty array to store the mapped categories
            $categories = [];
            // Fetch all taxonomy items where:
            // - The taxonomy_id equals 1 (indicating "category" type taxonomy)
            // - The name field is not empty
            // Select only the 'taxonomy_item_id' and rename 'name' as 'category_name'
            // Order the result by 'taxonomy_item_id' for consistent output
            $results = $this->taxonomyItem->select(['taxonomy_item_id', 'name as category_name'])
                ->where('name', '!=', '')
                ->where('taxonomy_id', '=', 1)
                ->orderBy('taxonomy_item_id')
                ->findAll();
            // Loop through each taxonomy record from the database result
            foreach ($results as $row) {
                // Trim whitespace from the category name and use it as the key
                // Map each category name to its corresponding taxonomy_item_id
                $categories[trim($row['category_name'])] = (int)$row['taxonomy_item_id'];
            }
            // Return the final associative array of categories
            // Example: ['Workstations' => 1, 'Fixed Height Workstations' => 2, 'Height Adjustable Workstations' => 3  ...]
            return $categories;
        } catch (Exception $e) {
            // Log any exception that occurs during the query or processing
            error_log("Error getting categories for validation: " . $e->getMessage());
            // Return an empty array to prevent the program from breaking
            return [];
        }
    }

    /**
     * Get tags for validation (tags_name => tag_id mapping)
     */
    private function getTagsForValidation(): array
    {
        try {
            // Initialize an empty array to store the mapped tags
            $tags = [];
            // Fetch all taxonomy items where:
            // - The taxonomy_id equals 1 (indicating "category" type taxonomy)
            // - The name field is not empty
            // Select only the 'taxonomy_item_id' and rename 'name' as 'category_name'
            // Order the result by 'taxonomy_item_id' for consistent output
            $results = $this->taxonomyItem->select(['taxonomy_item_id', 'name as tag_name'])
                ->where('name', '!=', '')
                ->where('taxonomy_id', '=', 2)
                ->orderBy('taxonomy_item_id')
                ->findAll();
            // Loop through each taxonomy record from the database result
            foreach ($results as $row) {
                // Trim whitespace from the category name and use it as the key
                // Map each category name to its corresponding taxonomy_item_id
                $tags[trim($row['tag_name'])] = (int)$row['taxonomy_item_id'];
            }
            // Return the final associative array of tags
            // Example: ['10W Wireless Charging' => 112, 'Universal Integration' => 113, '30W Shared USB Charging' => 114  ...]
            return $tags;
        } catch (Exception $e) {
            // Log any exception that occurs during the query or processing
            error_log("Error getting tags for validation: " . $e->getMessage());
            // Return an empty array to prevent the program from breaking
            return [];
        }
    }

    private function headersToFields(array $headers): array
    {
        $fields = [];
        foreach ($headers as $header) {
            $fields[$header] = null;
        }
        return $fields;
    }

    /**
     * Prepare a base array of all required product fields with default values.
     * Ensures that every record has a consistent structure before validation.
     */
    private function getDefaultFields(array $headers): array
    {
        $defaultFields = $this->headersToFields($headers);

        // Set default values for required fields
        $defaultFields['km_item_id'] = 0;
        $defaultFields['product_type_id'] = 1;
        $defaultFields['class_id'] = 1;
        $defaultFields['company_id'] = 1;
        $defaultFields['admin_id'] = 1;
        $defaultFields['parent_id'] = null;
        $defaultFields['model'] = '';
        $defaultFields['description'] = '';
        $defaultFields['warranty_period'] = '';
        $defaultFields['factory_code'] = '';
        $defaultFields['sku'] = '';
        $defaultFields['isbn'] = '';
        $defaultFields['barcode'] = '';
        $defaultFields['track_stock'] = 0;
        $defaultFields['stock_quantity'] = 0;
        $defaultFields['stock_status_id'] = 1;
        $defaultFields['lead_days'] = 0;
        $defaultFields['melbourne_lead_days'] = 0;
        $defaultFields['safety_stock'] = 0;
        $defaultFields['qty_alert'] = 0;
        $defaultFields['manufacturer_id'] = null;
        $defaultFields['vendor_id'] = null;
        $defaultFields['import_vendor_id'] = null;
        $defaultFields['factory_vendor_id'] = null;
        $defaultFields['product_range_id'] = null;
        $defaultFields['product_category_id'] = 1;
        $defaultFields['edgetape_colour_id'] = null;
        $defaultFields['requires_shipping'] = 1;
        $defaultFields['tax_type_id'] = null;
        $defaultFields['material'] = '';
        $defaultFields['weight'] = 0.00000000;
        $defaultFields['weight_type_id'] = null;
        $defaultFields['length'] = 0.00000000;
        $defaultFields['length_type_id'] = null;
        $defaultFields['width'] = null;
        $defaultFields['height'] = null;
        $defaultFields['depth'] = null;
        $defaultFields['price'] = null;
        $defaultFields['old_price'] = null;
        $defaultFields['min_order_quantity'] = 1;
        $defaultFields['out_of_stock_status'] = null;
        $defaultFields['carton_qm'] = null;
        $defaultFields['size'] = null;
        $defaultFields['carton_width'] = 0.00000;
        $defaultFields['carton_depth'] = 0.00000;
        $defaultFields['carton_height'] = 0.00000;
        $defaultFields['gross_weight'] = null;
        $defaultFields['date_available'] = null;
        $defaultFields['template'] = '';
        $defaultFields['views'] = 0;
        $defaultFields['subtract_stock'] = 1;
        $defaultFields['status'] = 0;
        $defaultFields['is_featured'] = 0;
        $defaultFields['sort_order'] = 0;
        $defaultFields['project_price_qty'] = null;
        $defaultFields['project_price_discount'] = 0.00000;
        $defaultFields['active'] = 1;
        $defaultFields['archive'] = 0;

        return $defaultFields;
    }

    /**
     * Get default fields for related products import
     */
    private function getDefaultRelatedProductFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for related products table
        $defaults['product_code'] = '';
        $defaults['related_product_code'] = '';

        return $defaults;
    }

    /**
     * Get default fields for digital assets import
     */
    private function getDefaultDigitalAssetFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for product_to_digital_asset table
        $defaults['product_code'] = '';
        $defaults['digital_asset_id'] = null;

        return $defaults;
    }

    /**
     * Get default fields for product attributes import
     */
    private function getDefaultProductAttributeFields(array $headers): array
    {
        $defaults = [];
        foreach ($headers as $h) {
            $defaults[$h] = null;
        }

        // Set defaults for product_attribute table (based on migration)
        $defaults['product_code'] = '';
        $defaults['attribute_id'] = null;
        $defaults['language_id'] = 1; // Default language
        $defaults['value'] = '';

        return $defaults;
    }

    /**
     * Get default fields for product variants import
     */
    private function getDefaultProductVariantFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for product_variant table
        $defaults['product_code'] = '';
        $defaults['variant_product_code'] = '';

        return $defaults;
    }

    private function getDefaultManufacturerVendorFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for manufacturer and vendor table
        $defaults['name'] = '';
        $defaults['slug '] = '';
        $defaults['image '] = 'image.jpg';
        $defaults['sort_order '] = '1';

        return $defaults;
    }

    private function getDefaultProductCertificatesFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for product_certificate table
        $defaults['title'] = '';
        $defaults['description '] = '';
        $defaults['sort_order '] = '1';
        $defaults['logo '] = '';

        return $defaults;
    }
    private function getDefaultProductRelatedProjectsFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for product_related_project table
        $defaults['product_id'] = '';
        $defaults['project_id'] = '';
        return $defaults;
    }

    /**
     * Get default fields for product options import
     */
    private function getDefaultProductOptionFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for product_option table (based on migration)
        $defaults['product_code'] = '';
        $defaults['option_id'] = null;
        $defaults['type_id'] = null;
        $defaults['name'] = '';
        $defaults['value'] = null;
        $defaults['meta_description'] = null;
        $defaults['required'] = 0;

        return $defaults;
    }

    /**
     * Get default fields for product images import
     */
    private function getDefaultProductImageFields(array $headers): array
    {
        $defaults = $this->headersToFields($headers);

        // Set defaults for product_image table (based on migration)
        $defaults['product_code'] = '';
        $defaults['image_link'] = '';
        $defaults['image'] = json_encode([]);
        $defaults['sort_order'] = 0;
        $defaults['status'] = json_encode(['active' => true]);
        $defaults['way_points'] = json_encode([]);

        return $defaults;
    }

    public function removeWayPoint(array $data): array
    {
        $model_id = $data['product_id'] ?? null;
        $point_id = $data['point_id'] ?? null;
    
        if (!$model_id || !$point_id) {
            return [
                'success' => false,
                'message' => 'Invalid product_id or point_id'
            ];
        }
    
        $query = $this->model->where('product_id', '=', $model_id)->first();
    
        if (!$query) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
    
        $way_points = $query->banner_way_points;
    
        // Decode safely
        $way_points = $way_points ? json_decode($way_points, true) : [];
    
        if (!is_array($way_points)) {
            $way_points = [];
        }
    
        // Filter out the waypoint
        $way_points = array_values(array_filter($way_points, function ($point) use ($point_id) {
            return isset($point['id']) && $point['id'] != $point_id;
        }));
    
        $updated = $query->update([
            'banner_way_points' => json_encode($way_points)
        ]);
    
        return [
            'success' => true,
            'message' => 'Way point removed successfully',
            'way_points' => $updated ? $way_points : []
        ];
    }

    public function getProductTitlesByProductIds(array $productIds): array
    {
       $this->model->clearQuery();
       $titles = [];
       $query = $this->model->whereIn('product_id', $productIds);
       $query->join('product_content', 'product_content.product_id', '=', 'product.product_id');
       $query->select(['product.product_id', 'product_content.title as product_title']);
       $results = $query->findAll(false);
    
       foreach($results as $result){
        $titles[$result['product_id']] = $result['product_title'];
       }
       return $titles;
       
    }

    public function deleteProductGalleryImageById(array $ids, string $property = 'images'): array
    {
        $deletedIds = []; // not use
       
        // delete multiple file from db.
        $deleted = '';
        if($property == 'images'){
            $this->productImage->clearQuery();
            $deleted = $this->productImage->deleteMultiple($ids);
        }elseif($property == 'downloads'){
            $this->designResourceDocument->clearQuery();
            $deleted = $this->designResourceDocument->deleteMultiple($ids);  
        }
        if($deleted){
            return [
                'success' => true,
                'deleted_ids' => $ids,
                'property' => $property,
            ];
        }
        return [
            'success' => false,
            'deleted_ids' => [],
            'property' => $property,
        ];
    }

    public function deleteProductCertificateById(array $files, string $property = 'certificates', $product_id=null): array
    {
        $this->productCertificate->clearQuery();
    
        $certificateIds = [];
        $titles = [];
    
        foreach ($files as $file) {
    
            if (!empty($file['product_certificate_id'])) {
                $certificateIds[] = (int) $file['product_certificate_id'];
                continue;
            }
    
            if (!empty($file['name'])) {
                $titles[] = (string) $file['name'];
            }
        }
    
        // Find certificates for files without product_certificate_id
        if (!empty($product_id) && !empty($titles)) {
    
            $certificates = $this->productCertificate
                ->clearQuery()
                ->where('product_id', '=', $product_id)
                ->whereIn('title', array_unique($titles))
                ->findAll(false);
    
            foreach ($certificates as $certificate) {
                if (!empty($certificate['product_certificate_id'])) {
                    $certificateIds[] = (int) $certificate['product_certificate_id'];
                }
            }
        }
    
        $certificateIds = array_values(array_unique(array_filter($certificateIds)));
    
        if (!empty($certificateIds)) {
    
            $deleted = $this->productCertificate
                ->clearQuery()
                ->deleteMultiple($certificateIds);
    
            return [
                'success' => (bool) $deleted,
                'deleted_ids' => $certificateIds,
                'property' => $property,
            ];
        }
    
        return [
            'success' => false,
            'deleted_ids' => [],
            'property' => $property,
        ];
    }

    public function updateProductDocumentFormat(array $document): array | bool
    {
        $doc = $this->designResourceDocument->where('design_resource_document_id', '=', $document['design_resource_document_id'])->first();
        if (!$doc) {
            return false;
        }
        $doc->update([
            'format' => $document['format'],
        ]);
        return $document;
    }

}
