<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\PostCategory\TaxonomyRepository;
use App\Core\Repositories\PostCategory\TaxonomyItemRepository;
use App\Core\Repositories\Taxonomy\TaxonomyItemRepositoryInterface as OriginalTaxonomyItemRepositoryInterface;
use App\Core\Models\PostCategory\TaxonomyItemData;
use App\Core\Models\PostCategory\TaxonomyItemResponse;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class TaxonomyController extends ApiController
{
    private TaxonomyRepository $taxonomyRepository;
    private TaxonomyItemRepository $taxonomyItemRepository;
    private OriginalTaxonomyItemRepositoryInterface $categoryRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        TaxonomyItemRepository $taxonomyItemRepository,
        OriginalTaxonomyItemRepositoryInterface $categoryRepository,
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->taxonomyRepository = $taxonomyRepository;
        $this->taxonomyItemRepository = $taxonomyItemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all taxonomies with pagination and filtering.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $result = $this->taxonomyRepository->findAll();
        return $this->renderResponse($result);
    }

    /**
     * Get a taxonomy by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $taxonomy = $this->taxonomyRepository->find((int)$id);
        if(!$taxonomy){
            return $this->renderError(404, 'Taxonomy not found');
        }
        return $this->renderResponse($taxonomy->data);
    }

    /**
     * Create a new taxonomy.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'post_type' => 'required|string',
                'type' => 'required|string',
                'site_id' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomy = $this->taxonomyRepository->create($data);
        return $this->renderResponse($taxonomy->data);
    }

    /**
     * Update a taxonomy.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'post_type' => 'string|nullable',
                'type' => 'string|nullable',
                'site_id' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingTaxonomy = $this->taxonomyRepository->find((int)$id);
        if (!$existingTaxonomy) {
            return $this->renderError(404, 'Taxonomy not found');
        }

        $taxonomy = $this->taxonomyRepository->update((int) $id, $data);
        if (!$taxonomy) {
            return $this->renderError(500, 'Failed to update taxonomy');
        }
        
        return $this->renderResponse($taxonomy->data);
    }

    /**
     * Delete a taxonomy.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->taxonomyRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Taxonomy deleted successfully']);
    }

    /**
     * Get all posts for a taxonomy.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function posts(Request $request, $id): Response
    {
        $taxonomy = $this->taxonomyRepository->find((int)$id);
        if (!$taxonomy) {
            return $this->renderError(404, 'Taxonomy not found');
        }

        $posts = $taxonomy->posts();
        return $this->renderResponse($posts);
    }

    /**
     * Get all taxonomy items for a taxonomy.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function items(Request $request, $id): Response
    {
        $taxonomy = $this->taxonomyRepository->find((int)$id);
        if (!$taxonomy) {
            return $this->renderError(404, 'Taxonomy not found');
        }

        $items = $taxonomy->taxonomyItem();
        return $this->renderResponse($items);
    }

    /**
     * Get taxonomy content for all languages.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function content(Request $request, $id): Response
    {
        $taxonomy = $this->taxonomyRepository->find((int)$id);
        if (!$taxonomy) {
            return $this->renderError(404, 'Taxonomy not found');
        }

        $content = $taxonomy->taxonomyContent();
        return $this->renderResponse($content);
    }



    /**
     * Get all product tags.
     *
     * @param Request $request
     * @return Response
     */
    public function getProductTags(Request $request): Response
    {
        try {
            $tagTaxonomyIds = $this->taxonomyRepository->getTagTaxonomyIds();
            
            if (empty($tagTaxonomyIds)) {
                return $this->renderResponse([]);
            }

            // Get taxonomy items for ALL tag taxonomy IDs
            $tags = $this->taxonomyItemRepository->getTaxonomyItemsByTaxonomyIds($tagTaxonomyIds, [
                'taxonomy_item.taxonomy_item_id',
                'taxonomy_item.image',
                'taxonomy_item.sort_order',
                'taxonomy_item.status',
                'taxonomy_item_content.name',
                'taxonomy_item_content.slug',
                'taxonomy_item_content.content as description'
            ]);

            // Format the response to match the expected structure
            $formattedTags = array_map(function($tag) {
                return [
                    'tag_id' => $tag['taxonomy_item_id'],
                    'thumbnail' => !empty($tag['image']) ? json_decode($tag['image'], true)['src'] ?? '' : '',
                    'name' => $tag['name'],
                    'slug' => $tag['slug'],
                    'description' => $tag['description'] ?? '',
                    'sort_order' => $tag['sort_order'],
                    'status' => $tag['status']
                ];
            }, $tags);

            return $this->renderResponse($formattedTags);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to retrieve product tags: ' . $e->getMessage());
        }
    }

    /**
     * Show a product tag.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function showProductTag(Request $request, $id): Response
    {
        $taxonomyItem = $this->taxonomyItemRepository->showTaxonomyItem((int)$id);
        if (!$taxonomyItem) {
            return $this->renderError(404, 'Product tag not found');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new product tag.
     *
     * @param Request $request
     * @return Response
     */
    public function createProductTag(Request $request): Response
    {
        try {
            $productTag = $request->input('productTag') ?? $request->all();
            // validate the product tag
            $validated = $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
            ], $productTag);

            if($validated instanceof Response){
                return $validated;
            }
            $taxonomyItemData = new TaxonomyItemData($productTag);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomyItem = $this->taxonomyItemRepository->createTaxonomyItem($taxonomyItemData);
        if (!$taxonomyItem) {
            return $this->renderError(500, 'Failed to create product tag');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    /**
     * Update a product tag.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateProductTag(Request $request, $id): Response
    {
        try {
            $productTag = $request->input('productTag') ?? $request->all();
            $validated = $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
            ], $productTag);
            if($validated instanceof Response){
                return $validated;
            }
            $taxonomyItemData = new TaxonomyItemData($productTag);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomyItem = $this->taxonomyItemRepository->updateTaxonomyItem((int)$id, $taxonomyItemData);
        if (!$taxonomyItem) {
            return $this->renderError(500, 'Failed to update product tag');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    /**
     * Delete a product tag.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteProductTag(Request $request, $id): Response
    {
        $deleted = $this->taxonomyItemRepository->deleteTaxonomyItem((int)$id);
        if (!$deleted) {
            return $this->renderError(500, 'Failed to delete product tag');
        }
        
        return $this->renderResponse(['message' => 'Product tag deleted successfully']);
    }

    /**
     * Get all product finishes.
     *
     * @param Request $request
     * @return Response
     */
    public function getProductFinishes(Request $request): Response
    {
        try {
            // First, get finish taxonomy IDs from TaxonomyRepository
            $finishTaxonomyIds = $this->taxonomyRepository->getFinishTaxonomyIds();
            
            // If no finish taxonomies found, return empty array
            if (empty($finishTaxonomyIds)) {
                return $this->renderResponse([]);
            }

            // Get taxonomy items for ALL finish taxonomy IDs
            $finishes = $this->taxonomyItemRepository->getTaxonomyItemsByTaxonomyIds($finishTaxonomyIds, [
                'taxonomy_item.taxonomy_item_id',
                'taxonomy_item.image',
                'taxonomy_item.sort_order',
                'taxonomy_item.status',
                'taxonomy_item_content.name',
                'taxonomy_item_content.slug',
                'taxonomy_item_content.content as description'
            ]);

            // Format the response to match the expected structure
            $formattedFinishes = array_map(function($finish) {
                return [
                    'finish_id' => $finish['taxonomy_item_id'],
                    'thumbnail' => !empty($finish['image']) ? json_decode($finish['image'], true)['src'] ?? '' : '',
                    'name' => $finish['name'],
                    'slug' => $finish['slug'],
                    'description' => $finish['description'] ?? '',
                    'sort_order' => $finish['sort_order'],
                    'status' => $finish['status']
                ];
            }, $finishes);

            return $this->renderResponse($formattedFinishes);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to retrieve product finishes: ' . $e->getMessage());
        }
    }

    /**
     * Show a product finish.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function showProductFinish(Request $request, $id): Response
    {
        $taxonomyItem = $this->taxonomyItemRepository->showTaxonomyItem((int)$id);
        if (!$taxonomyItem) {
            return $this->renderError(404, 'Product finish not found');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new product finish.
     *
     * @param Request $request
     * @return Response
     */
    public function createProductFinish(Request $request): Response
    {
        try {
            $productFinish = $request->input('productFinish') ?? $request->all();
            // Set taxonomy_id to 11 for product finishes
            $productFinish['taxonomy_id'] = 11;
            $taxonomyItemData = new TaxonomyItemData($productFinish);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomyItem = $this->taxonomyItemRepository->createTaxonomyItem($taxonomyItemData);
        if (!$taxonomyItem) {
            return $this->renderError(500, 'Failed to create product finish');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    public function createProductCategory(Request $request): Response
    {
        try {
            $productCategory = $request->input('productCategory') ?? $request->all();
            // Set taxonomy_id to 11 for product finishes
            $productCategory['taxonomy_id'] = 1;
            $taxonomyItemData = new TaxonomyItemData($productCategory);
            // $taxonomyItemData = $productCategory;
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomyItem = $this->taxonomyItemRepository->createTaxonomyItem($taxonomyItemData);
        if (!$taxonomyItem) {
            return $this->renderError(500, 'Failed to create product category');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    /**
     * Update a product finish.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateProductCategory(Request $request, $id): Response
    {
        try {
            $productCategory = $request->input('category') ?? $request->all();
            // Set taxonomy_id to 11 for product finishes
            $productCategory['taxonomy_id'] = 1;
            $taxonomyItemData = new TaxonomyItemData($productCategory);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomyItem = $this->taxonomyItemRepository->updateTaxonomyItem((int)$id, $taxonomyItemData);
        if (!$taxonomyItem) {
            return $this->renderError(500, 'Failed to update product category');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    public function updateProductFinish(Request $request, $id): Response
    {
        try {
            $productFinish = $request->input('productFinish') ?? $request->all();
            // Set taxonomy_id to 11 for product finishes
            $productFinish['taxonomy_id'] = 11;
            $taxonomyItemData = new TaxonomyItemData($productFinish);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxonomyItem = $this->taxonomyItemRepository->updateTaxonomyItem((int)$id, $taxonomyItemData);
        if (!$taxonomyItem) {
            return $this->renderError(500, 'Failed to update product finish');
        }
        
        $response = new TaxonomyItemResponse($taxonomyItem->data);
        return $this->renderResponse($response);
    }

    /**
     * Delete a product finish.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteProductFinish(Request $request, $id): Response
    {
        $deleted = $this->taxonomyItemRepository->deleteTaxonomyItem((int)$id);
        if (!$deleted) {
            return $this->renderError(500, 'Failed to delete product finish');
        }
        
        return $this->renderResponse(['message' => 'Product finish deleted successfully']);
    }

    public function importTaxonomyItems(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $taxonomy_id = $request->input('taxonomy_id') ?? 1;
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $result = $this->categoryRepository->importTaxonomyItems($csv_file_path, (int) $taxonomy_id);
        return $this->renderResponse(['success' => $result]);
    }

    // importTaxonomies
    public function importTaxonomies(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->taxonomyRepository->importTaxonomies($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    // taxonomyTypes
    public function getTaxonomyTypes(Request $request): Response
    {
        $taxonomyTypes = $this->taxonomyRepository->getTaxonomyTypes();
        return $this->renderResponse($taxonomyTypes);
    }
    
    // upload taxonomy item image
    public function upload(Request $request, int $taxonomy_item_id): Response
    {
        $property = $request->input('property');
        // Set default size
        $size = [
            'width' =>987,
            'height' => 600,
        ];

        $folder = '';
        $is_banner = false;
        if ($property == 'image' || $property == 'banner_image') {
            $folder = 'media/categories/banner';
            $is_banner = true;
        }
        if ($property == 'slider_image') {
            $folder = 'media/categories/slider';
        }
        
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder, null, $is_banner);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->taxonomyItemRepository->updateTaxonomyItemImage($result['files'], $property, $taxonomy_item_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete vendor image
    public function deleteImage(Request $request, int $taxonomy_item_id): Response
    {
        $property = $request->input('property');
        $deleted = $this->taxonomyItemRepository->deleteTaxonomyItemImage($taxonomy_item_id, $property);
        if (!$deleted) {
            return $this->renderError(500, 'Failed to delete taxonomy item image');
        }
        return $this->renderResponse(['message' => 'Taxonomy item image deleted successfully']);
    }
} 