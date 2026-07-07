<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{

    /*
    |--------------------------------------------------------------------------
    | Product Retrieval
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get a single product with all related data
     *
     * @param int|null $productId
     * @param string|null $slug
     * @param int|null $languageId
     * @param bool $includePromotion
     * @param bool $includePoints
     * @param bool $includeStockStatus
     * @param bool $includeWeightType
     * @param bool $includeLengthType
     * @param bool $includeRating
     * @param bool $includeReviews
     * @return array|null
     */

    public function get(?int $productId = null, ?string $slug = null, ?int $languageId = null,
        bool $includePromotion = false,
        bool $includePoints = false,
        bool $includeStockStatus = false,
        bool $includeWeightType = false,
        bool $includeLengthType = false,
        bool $includeRating = false,
        bool $includeReviews = false
    ): ?array;

    /**
     * Get product by id
     *
     * @param int $productId
     * @param int $languageId
     * @return Product|null
     */
    public function getProductById(int $productId, int $languageId = 1): ?Product;

    /**
     * Get product by slug
     *
     * @param string $slug
     * @return Product|null
     */
    public function getBySlug(string $slug): ?Product;

    /**
     * Get product by product code
     *
     * @param string $productCode
     * @return Product|null
     */
    public function getByProductCode(string $productCode): ?Product;

    public function getProductDimension(string $productCode): ?array;
    /**
     * Get product by code with default configuration
     *
     * @param string $productCode
     * @return Product|null
     */
    public function getProductByCodeWithDefaultConfiguration(string $productCode): ?Product;

    /**
     * Get product list
     *
     * @return array
     */
    public function productList(): array;

    /**
     * Get all products with filters
     *
     * @param array $filters
     * @return array
     */
    public function getAll(array $filters = []): array;

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions(): array;

    /*
    |--------------------------------------------------------------------------
    | Product Category
    |--------------------------------------------------------------------------
    */

    /**
     * Get products by parent category id
     *
     * @param int $categoryId
     * @return array
     */
    public function getProductsByParentCategoryId(int $categoryId): array;

    /**
     * Get products by category id
     *
     * @param int $categoryId
     * @return array
     */
    public function getProductsByCategoryId(int $categoryId): array;



    /**
     * Get products by showroom section product ids
     *
     * @param array $productIds
     * @return array
     */
    public function getProductsByShowroomSectionProductIds(array $productIds): array;

    /**
     * Get products by category slug
     *
     * @param string $categorySlug
     * @param array $params
     * @return array|null
     */
    public function getProductsByCategorySlug(string $categorySlug, array $params): ?array;

    /**
     * Get product categories
     *
     * @return array
     */
    public function getCategories(): array;


    /*
    |--------------------------------------------------------------------------
    | Product Creation / Update
    |--------------------------------------------------------------------------
    */

    /**
     * Create product
     *
     * @param ProductData $productData
     * @return Product
     */
    public function createProduct(ProductData $productData): Product;

    /**
     * Update product
     *
     * @param ProductData $productData
     * @return Product
     */
    public function updateProduct(ProductData $productData): Product;

    /**
     * Insert products
     *
     * @param array $data
     * @return bool
     */
    public function insertProducts(array $data): bool;


    /*
    |--------------------------------------------------------------------------
    | Product Media
    |--------------------------------------------------------------------------
    */

    /**
     * Update product images
     *
     * @param array $images
     * @param int $productId
     * @return bool
     */
    public function productImage(array $images, int $productId): bool;

    /**
     * Insert product images
     *
     * @param array $data
     * @param int $product_id
     * @return array
     */
    public function insertProductImages(array $data, int $product_id): array;

    /**
     * Insert product table image file
     *
     * @param array $data
     * @param string $property
     * @param int $product_id
     * @return bool
     */
    public function insertProductTableImageFile(array $data, string $property, int $product_id): bool;

    /**
     * Delete product image
     *
     * @param int $product_image_id
     * @return bool
     */
    public function deleteProductImage(int $product_image_id): bool;


    /*
    |--------------------------------------------------------------------------
    | Product Relations
    |--------------------------------------------------------------------------
    */

        /**
     * Update product related products
     *
     * @param string $slug
     * @return bool
     */
    public function getFamilyProducts(int $productId, string $slug): array;

    /**
     * Update product related products
     *
     * @param array $relatedIds
     * @param int $productId
     * @return bool
     */
    public function productRelated(array $relatedIds, int $productId): bool;

    /**
     * Get related products
     *
     * @param int $productId
     * @param int $limit
     * @param string $productFamilyCode
     * @return array
     */
    public function getRelatedProducts(int $productId, int $limit = 4, string $productFamilyCode = ''): array;

    /**
     * Delete related product
     *
     * @param int $product_id
     * @param int $related_product_id
     * @return bool
     */
    public function deleteRelatedProduct(int $product_id, int $related_product_id): bool;
    /**
     * Delete related product
     *
     * @param int $product_id
     * @param int $related_product_id
     * @return bool
     */
    public function removeProductFromFamily(int $product_id, int $related_product_id): bool;

    /**
     * Get product also like
     *
     * @param string $productSlug
     * @param int $limit
     * @return array
     */
    public function getProductAlsoLike(string $productSlug, int $limit = 4): array;


    /*
    |--------------------------------------------------------------------------
    | Product Search
    |--------------------------------------------------------------------------
    */

    /**
     * Related product search
     *
     * @param string $search
     * @return array
     */
    public function relatedProductSearch(string $search): array;

    /**
     * Variant product search
     *
     * @param string $search
     * @return array
     */
    public function variantProductSearch(string $search): array;

    /**
     * Digital asset search
     *
     * @param string $search
     * @return array
     */
    public function digitalAssetSearch(string $search): array;

    /**
     * Get product search for waypoints
     *
     * @param string $query
     * @return array
     */
    public function getProductSearchForWaypoints(string $query): array;


    /*
    |--------------------------------------------------------------------------
    | Product Taxonomy
    |--------------------------------------------------------------------------
    */

    /**
     * Get product tags
     *
     * @return array
     */
    public function getTags(): array;

    /**
     * Get product finishes
     *
     * @return array
     */
    public function getFinishes(): array;

    /**
     * Insert product taxonomies
     *
     * @param array $data
     * @return bool
     */
    public function insertProductTaxonomies(array $data): bool;


    /*
    |--------------------------------------------------------------------------
    | Product Components (Frontend CMS Components)
    |--------------------------------------------------------------------------
    */

    /**
     * Get product hero component data
     *
     * @param array $param
     * @return array
     */
    public function getProductHeroComponentData(array $param): array;

    /**
     * Get featured product slider component data
     *
     * @param array $params
     * @return array
     */
    public function getFeaturedProductSliderComponentData(array $params);

    /**
     * Get product featured projects slider component data
     *
     * @param array $params
     * @return array
     */
    public function getProductFeaturedProjectsSliderComponentData(array $params);

    /**
     * Get featured product masonry component data
     *
     * @param array $param
     * @return array
     */
    public function getFeaturedProductMasonryComponentData(array $param);

    /**
     * Get product feature component data
     *
     * @param array $params
     * @return array
     */
    public function getProductFeatureComponentData(array $params);

    /**
     * Get product story masonry component data
     *
     * @param array $params
     * @return array
     */
    public function getProductStoryMasonryComponentData(array $params);

    /**
     * Get product specifications component data
     *
     * @param array $params
     * @return array
     */
    public function getProductSpecificationsComponentData(array $params = []);

    /**
     * Get product related project component data
     *
     * @param string $slug
     * @return array
     */
    public function getProductRelatedProjectComponentData(string $slug): array;

    /**
     * Get product call to action component data
     *
     * @param int $productId
     * @param array $fields
     * @param int $limit
     * @return array
     */
    public function getProductCallToActionComponentData(int $productId, array $fields, int $limit = 3);

    /**
     * Get product sustainability component data
     *
     * @param int $productId
     * @param array $fields
     * @param int $limit
     * @return array
     */
    public function getProductSustainabilityComponentData(int $productId, array $fields, int $limit = 1);

    /**
     * Get category seating details component data
     *
     * @param array $params
     * @return array
     */
    public function getCategorySeatingDetailsComponentData(array $params);

    /**
     * Get products by category details component
     *
     * @param array $param
     * @return array
     */
    public function getProductsByCategoryDetailsComponent(array $param): array;

    /**
     * Get slider products
     *
     * @param array $param
     * @return array
     */
    public function getSliderProducts(array $param): array;


    /**
     * Get product instagram slider component data
     *
     * @param string $slug
     * @return array
     */
    public function getProductInstagramSliderComponentData(string $slug): array;


    /*
    |--------------------------------------------------------------------------
    | Product Tabs
    |--------------------------------------------------------------------------
    */

    /**
     * Get product video gallery images data
     *
     * @param array $params
     * @return array
     */
    public function getProductVideoGallaryImagesData(array $params): array;

    /**
     * Get product downloads tab data
     *
     * @param array $params
     * @return array
     */
    public function getProductDownloadsTabData(array $params): array;

    /**
     * Get product certifications tab data
     *
     * @param array $params
     * @return array
     */
    public function getProductCertificationsTabData(array $params): array;


    /*
    |--------------------------------------------------------------------------
    | Product Import
    |--------------------------------------------------------------------------
    */

    /**
     * Import products from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProducts(string $csvFilePath): array;

    /**
     * Import product images from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductsImages(string $csvFilePath): array;

    /**
     * Import related products from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importRelatedProducts(string $csvFilePath): array;

    /**
     * Import product related projects from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductRelatedProjects(string $csvFilePath): array;

    /**
     * Import products digital assets from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductsDigitalAssets(string $csvFilePath): array;

    /**
     * Import products attributes from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductsAttributes(string $csvFilePath): array;

    /**
     * Import products variants from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductsVariants(string $csvFilePath): array;

    /**
     * Import products options from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductsOptions(string $csvFilePath): array;

    /**
     * Import products tags from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductsTags(string $csvFilePath): array;

    /**
     * Import manufacturer vendors from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importManufacturerVendors(string $csvFilePath): array;

    /**
     * Import product certificates from CSV
     *
     * @param string $csvFilePath
     * @return array
     */
    public function importProductCertificates(string $csvFilePath): array;


    /*
    |--------------------------------------------------------------------------
    | Waypoints / Category Tools
    |--------------------------------------------------------------------------
    */

    /**
     * Update waypoints
     *
     * @param array $data
     * @return array
     */
    public function updateWayPoints(array $data): array;

    /**
     * Update category banner waypoints
     *
     * @param array $data
     * @return array
     */
    public function updateCategoryBannerWayPoints(array $data): array;

    /**
     * Update category order
     *
     * @param array $data
     * @return array
     */
    public function updateCategoryOrder(array $data): array;


    /*
    |--------------------------------------------------------------------------
    | Product Preparation
    |--------------------------------------------------------------------------
    */

    /**
     * Prepare products data
     *
     * @param array $products
     * @return array
     */
    public function prepareProducts(array $products): array;

    /**
     * Remove way point
     *
     * @param array $data
     * @return array
     */
    public function removeWayPoint(array $data): array;


    public function getProductTitlesByProductIds(array $productIds): array;

    public function deleteProductGalleryImageById(array $ids, string $property = 'images'): array;
    public function insertProductCertificates(array $data,  int $product_id): array;
    public function deleteProductCertificateById(array $files, string $property = 'certificates', $product_id = null): array;
    public function insertProductResources(array $resources, int $product_id): array;
    public function removeProductRelatedProject(int $product_id, int $project_id): bool;

    public function getProductMetadata($productId): array;

    public function updateProductDocumentFormat(array $document): array | bool;

}
