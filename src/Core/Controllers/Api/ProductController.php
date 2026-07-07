<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Models\Product\ProductData;
use App\Core\Models\Product\ProductResponse;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use Exception;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Product\ProductMetaRepositoryInterface;

class ProductController extends ApiController
{
    private ProductRepositoryInterface $productRepository;
    private TaxonomyItemRepositoryInterface $taxonomyItemRepository;
    private MediaRepositoryInterface $mediaRepository;
    private ProductMetaRepositoryInterface $productMetaRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        MediaRepositoryInterface $mediaRepository,
        TaxonomyItemRepositoryInterface $taxonomyItemRepository,
        ProductMetaRepositoryInterface $productMetaRepository
    ) {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->mediaRepository = $mediaRepository;
        $this->taxonomyItemRepository = $taxonomyItemRepository;
        $this->productMetaRepository = $productMetaRepository;
    }

    /**
     * Get all sites.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $products = $this->productRepository->findAll();

        // Decode image field for each product
        foreach ($products as &$product) {
            if (isset($product['image']) && is_string($product['image'])) {
                $decodedImage = json_decode($product['image'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $product['image'] = $decodedImage;
                }
            }
        }

        return $this->renderResponse($products);
    }

    /**
     * Show a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $product = $this->productRepository->getProductById((int)$id);
        if (!$product) {
            return $this->renderError(404, 'Product not found');
        }
        $response = new ProductResponse($product->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new site.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $product = $request->input('product');
            $productData = new ProductData($product);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $product = $this->productRepository->createProduct($productData);
        if (!$product) {
            return $this->renderError(500, 'Failed to create product');
        }
        $product = new ProductResponse($product->data);
        return $this->renderResponse($product);
    }

    /**
     * Update a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */

    public function update(Request $request, int $id): Response
    {
        // return $this->renderError(500, 'Failed to update product');
        try {
            $product = $request->input('product');
            $productData = new ProductData($product);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $product = $this->productRepository->updateProduct($productData);

        if (!$product) {
            return $this->renderError(500, 'Failed to update product');
        }


        $product = new ProductResponse($product->data);
        return $this->renderResponse($product);
    }

    /**
     * Delete a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->productRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Product deleted successfully']);
    }

    public function getCategories(Request $request): Response
    {
        $categories = $this->productRepository->getCategories();
        return $this->renderResponse($categories);
    }

    public function productList(Request $request): Response
    {
        $productList = $this->productRepository->productList();
        return $this->renderResponse($productList);
    }

    public function relatedProductSearch(Request $request): Response
    {
        $relatedProducts = $this->productRepository->relatedProductSearch($request->input('search'));
        return $this->renderResponse($relatedProducts);
    }

    public function getProductsByCategory(Request $request): Response
    {
        // $category = $request->query('category');
        // $search = $request->query('search');
        // $products = $this->productRepository->getProductsByCategory($category, $search);
        return $this->renderResponse([]);
    }

    public function variantProductSearch(Request $request): Response
    {
        $variantProducts = $this->productRepository->variantProductSearch($request->input('search'));
        return $this->renderResponse($variantProducts);
    }

    public function digitalAssetSearch(Request $request): Response
    {
        $digitalAssets = $this->productRepository->digitalAssetSearch($request->input('search'));
        return $this->renderResponse($digitalAssets);
    }

    // public function createRelatedProducts(Request $request): Response
    // {
    //     $relatedProducts = $this->productRepository->createRelatedProducts($request->input('related_products'));
    //     return $this->renderResponse($relatedProducts);
    // }
    // delete related product
    public function deleteRelatedProduct(Request $request, int $product_id, int $related_product_id): Response
    {
        try {
            $this->productRepository->deleteRelatedProduct($product_id, $related_product_id);
            return $this->renderResponse(['message' => 'Related product deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete related product: ' . $e->getMessage());
        }

    }
    public function removeProductFromFamily(Request $request, int $product_id, int $related_product_id): Response
    {
        try {
            $this->productRepository->removeProductFromFamily($product_id, $related_product_id);
            return $this->renderResponse(['message' => 'Product removed from family successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to remove product from family: ' . $e->getMessage());
        }

    }
    public function removeProductRelatedProject(Request $request, int $product_id, int $project_id): Response
    {
        try {
            $this->productRepository->removeProductRelatedProject($product_id, $project_id);
            return $this->renderResponse(['message' => 'Product removed from family successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to remove product from family: ' . $e->getMessage());
        }

    }

    public function upload(Request $request, int $product_id): Response
    {
        $property = $request->input('property');
        
        // Set default size
        // $size = [
        //     'width' => 945,
        //     'height' => 630,
        // ];
        $thumbSize = null;
        $folderName = 'image';
        $is_banner = false;
        // Override size based on property
        if($property == 'banner_image'){
            $folderName = 'banner';
            // $size = [
            //     'width' => 1600,
            //     'height' => 657,
            // ];
            $is_banner = true;
        }
        elseif ($property == 'image') {
            $folderName = 'image';
            // $size = [
            //     'width' => 748,
            //     'height' => 642,
            // ];
            // $thumbSize = [
            //     'width' => 436,
            //     'height' => 552,
            // ];
        }
        elseif ($property == 'main_image_one') {
            $folderName = 'main-image-one';
            // $size = [
            //     'width' => 691,
            //     'height' => 496,
            // ];
        } elseif ($property == 'main_image_two') {
            $folderName = 'main-image-two';
            // $size = [
            //     'width' => 537,
            //     'height' => 496,
            // ];
        } elseif($property == 'feature_image_one' || $property == 'feature_image_two' || $property == 'feature_image_three'){
            $folderName = 'feature';
            // $size = [
            //     'width' => 429,
            //     'height' => 314,
            // ];
        }elseif($property == 'specifications_image'){
            $folderName = 'specification';
            // $size = [
            //     'width' => 748,
            //     'height' => 642,
            // ];
        }
        
        if($request->files() || isset($_FILES['files'])){
          $files = $request->files() ?? $_FILES['files'];
          
          if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
          }

        //   $folderName = str_replace('_', '-', $property);
            $uploadDir = '';
            if($property == 'downloads'){
                $uploadDir = "media/design-resource/models";
            }elseif($property == 'certificates'){
                $uploadDir = "media/Certificates";
            }else{
                $uploadDir = "media/Products/{$folderName}";
            }

          $data = [
            'files' => $files,
            'upload_dir' => $uploadDir
          ];

          $result = $this->mediaRepository->upload($data, [], 'media/Products', null, $is_banner);

          if(!$result){
            return $this->renderError(500, 'Failed to upload media');
          }
          if ($product_id > 0) {
                if ($property == 'product_images') {
                    if(isset($result['files'])){
                          $result['files'] = $this->productRepository->insertProductImages($result['files'], $product_id);
                    }
                }elseif($property == 'certificates'){
                    $this->productRepository->insertProductCertificates($result['files'], $product_id);
                }elseif($property == 'downloads'){
                    $result = $this->productRepository->insertProductResources($result['files'], $product_id);
                    return $this->renderResponse($result);
                }else{
                    $this->productRepository->insertProductTableImageFile($result['files'], $property, $product_id);  
                }
            }
          return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    public function deleteByPath(Request $request): Response
    {
        $path = $request->input('path');
        if (!$path) {
            return $this->renderError(422, 'Path is required');
        }
        $this->mediaRepository->deleteMediaByPath($path);
        return $this->renderResponse(['message' => 'Media deleted successfully']);
    }

    public function deleteProductImage(Request $request, int $product_image_id): Response
    {
        $deleted = $this->productRepository->deleteProductImage($product_image_id);
        return $this->renderResponse(['message' => 'Media deleted successfully', 'deleted' => $deleted]);
    }

    public function getOptions(Request $request): Response
    {
        $options = $this->productRepository->getOptions();
        return $this->renderResponse($options);
    }

    public function importProducts(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProducts($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importProductsImages(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsImages($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importRelatedProducts(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importRelatedProducts($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importProductsSortByCategory(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsSortByCategory($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importProductsDigitalAssets(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsDigitalAssets($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importProductsAttributes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsAttributes($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importProductsVariants(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsVariants($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importProductsOptions(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsOptions($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importProductsTags(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductsTags($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importManufacturerVendors(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importManufacturerVendors($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    // import product certificates
    public function importProductCertificates(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductCertificates($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    // import product related projects
    public function importProductRelatedProjects(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productRepository->importProductRelatedProjects($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function getProductsByCategorySlug(Request $request, string $category): Response
    {
        // $params = $request->query();
        $params = [
            'current_page' => (int) $request->query('current_page') ?? 1,
            'per_page' => (int) $request->query('per_page') ?? 40,
            'offset' => (int) $request->query('offset') ?? 0,
            'material_id'    => (int) $request->query('material_id', 0),
            'feature_id'     => (int) $request->query('feature_id', 0),
            'weight_id'      => $request->query('weight_id', 0),
            'certificate_id' => (int) $request->query('certificate_id', 0),
            'item_count' => (int) $request->query('item_count') ?? 0
        ];
        // $filterableFields = ['material_id', 'feature_id', 'weight_id', 'certificate_id'];
        // foreach ($filterableFields as $field) {
        //     $value = $request->query($field);
        //     $params[$field] = $field != 'weight_id' ? (int) $value : explode('-', $value);
        // }
        $products = $this->productRepository->getProductsByCategorySlug($category, $params);
        return $this->renderResponse($products);
    }

    protected function  getItemFields()
    {
        return [
            'id' => '',
            'item_type_id' => '',
            'company_id' => '',
            'vendor_id' => '',
            'import_vendor_id' => '',
            'factory_vendor_id' => '',
            'item_range_id' => '',
            'item_category_id' => '',
            'sort_order' => '',
            'item_code' => '',
            'factory_code' => '',
            'web_sku' => '',
            'description' => '',
            'specifications' => '',
            'warranty_period' => '',
            'active' => '',
            'width' => '',
            'height' => '',
            'depth' => '',
            'carton_qm' => '',
            'gross_weight' => '',
            'boradusages_sixteen' => '',
            'boardusages_eighteen' => '',
            'boardusages_twentyfive' => '',
            'boardusages_thirtythree' => '',
            'boardusages_fifty' => '',
            'lead_days' => '',
            'quote_rating' => '',
            'quote_image' => '',
            'web_link' => '',
            'print_sticker' => '',
            'track_stock' => '',
            'user_note' => '',
            'zone' => '',
            'archive' => '',
            'tlf_code' => '',
            'project_price_qty' => '',
            'project_price_discount' => ''
        ];
    }


    public function updateWayPoints(Request $request): Response
    {

        $data = $request->all();

        $this->productRepository->updateWayPoints($data);
        return $this->renderResponse(['message' => 'Way points updated successfully']);
    }

    public function productSearchForWaypoints(Request $request): Response
    {
        $query = $request->query('search');
        $products = $this->productRepository->getProductSearchForWaypoints((string) $query);
        return $this->renderResponse($products);
    }
    
    public function updateCategoryBannerWayPoints(Request $request): Response
    {
        $data = $request->all();
        $this->productRepository->updateCategoryBannerWayPoints($data);
        return $this->renderResponse(['message' => 'Way points updated successfully']);
    }

    public function updateCategoryOrder(Request $request): Response
    {
        $data = $request->input('categories') ?? [];
        $result = $this->taxonomyItemRepository->updateCategoryOrder($data);
        if (!$result) {
            return $this->renderError(500, $result['message']);
        }
        return $this->renderResponse($result);
    }

    public function removeWayPoint(Request $request): Response
    {
        $data = $request->all();
        $removed = $this->productRepository->removeWayPoint($data);
        return $this->renderResponse($removed);
    }

    public function deleteProductGalleryImage(Request $request): Response
    {
        try {
            $data = $request->validate([
                'product_image_ids' => 'nullable|array',
                'design_resource_document_ids' => 'nullable|array',
                'certificates' => 'nullable|array',
                'property' => 'nullable|string',
                'product_id' => 'nullable|number',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $property = $data['property'] ?? 'images';

        if ($property == 'images') {
            $ids = array_values(array_filter( // array value reset key value.
                array_map('intval', $data['product_image_ids']), // only keep integer data.
                static fn (int $id): bool => $id > 0, // keep greater than 0
            ));
            if ($ids === []) {
                return $this->renderError(422, 'No valid image ids provided');
            }
    
            $productGalleryImage = $this->productRepository->deleteProductGalleryImageById($ids, $property);
            return $this->renderResponse($productGalleryImage);
        }elseif($property == 'downloads'){
            $ids = array_values(array_filter( // array value reset key value.
                array_map('intval', $data['design_resource_document_ids']), // only keep integer data.
                static fn (int $id): bool => $id > 0, // keep greater than 0
            ));
            if ($ids === []) {
                return $this->renderError(422, 'No valid image ids provided');
            }
    
            $productGalleryImage = $this->productRepository->deleteProductGalleryImageById($ids, $property);
            return $this->renderResponse($productGalleryImage);
        }

        $productGalleryImage = $this->productRepository->deleteProductCertificateById($data['certificates'], $property, $data['product_id']);
        return $this->renderResponse($productGalleryImage);


    }
    public function importProductMeta(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        $result = $this->productMetaRepository->importProductMeta($csv_file_path);
        return $this->renderResponse($result);
    }

    public function updateProductDocumentFormat(Request $request): Response
    {
        $document = $request->validate([
            'design_resource_document_id' => 'required|integer',
            'format' => 'required|string',
        ]);
        if (!$document) {
            return $this->renderError(422, 'Document is required');
        }
        $productDocument = $this->productRepository->updateProductDocumentFormat((array) $document);
        if (!$productDocument) {
            return $this->renderError(422, 'Failed to update document format');
        }
        return $this->renderResponse($productDocument);
    }
}
