<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;


class DesignResourceController extends ApiController
{
    private DesignResourceRepositoryInterface $designResourceRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        DesignResourceRepositoryInterface $designResourceRepository,
        MediaRepositoryInterface $mediaRepository,
    ) {
        parent::__construct();
        $this->designResourceRepository = $designResourceRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all taxonomies with pagination and filtering.
     *
     * @param Request $request
     * @return Response
     */
    public function finishes(Request $request): Response
    {
        $result = $this->designResourceRepository->where(['type' => 'finish'])->findAll();
        return $this->renderResponse($result);
    }

    /**
     * Get a taxonomy by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function showFinish(Request $request, $id): Response
    {
        $finish = $this->designResourceRepository->where(['type' => 'finish', 'id' => $id])->first();
        if (!$finish) {
            return $this->renderError(404, 'Finish not found');
        }
        return $this->renderResponse($finish->data);
    }

    /**
     * Create a new taxonomy.
     *
     * @param Request $request
     * @return Response
     */
    public function createFinish(Request $request): Response
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

        $finish = $this->designResourceRepository->create($data);
        return $this->renderResponse($finish->data);
    }

    /**
     * Update a taxonomy.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateFinish(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'string|nullable',
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
    public function deleteFinish(Request $request, $id): Response
    {
        $this->taxonomyRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Taxonomy deleted successfully']);
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
            $formattedFinishes = array_map(function ($finish) {
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
     * Create a new product finish.
     *
     * @param Request $request
     * @return Response
     */
    public function addProductFinish(Request $request): Response
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

    /**
     * Update a product finish.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function removeProductFinish(Request $request, $id): Response
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

    public function importDesignResources(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $resource_type = $request->input('resource_type') ?? 'documents';
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        // $resource_type = $request->input('resource_type') ?? 'finishes';

        $results = $this->designResourceRepository->importDesignResources($csv_file_path, $resource_type);
        return $this->renderResponse(['success' => $results]);
    }

    public function importFinishes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $resource_type = $request->input('resource_type') ?? 'finishes';

        $finishes = $this->designResourceRepository->importResources($csv_file_path, $resource_type);
        return $this->renderResponse(['success' => $finishes]);
    }
    public function importTextiles(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $resource_type = $request->input('resource_type') ?? 'textiles';

        $finishes = $this->designResourceRepository->importResources($csv_file_path, $resource_type);
        return $this->renderResponse(['success' => $finishes]);
    }

    public function importColors(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $resource_type = $request->input('resource_type') ?? 'colors';

        $colors = $this->designResourceRepository->importResources($csv_file_path, $resource_type);
        return $this->renderResponse(['success' => $colors]);
    }

    public function importDocuments(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $resource_type = $request->input('resource_type') ?? 'documents';

        $results = $this->designResourceRepository->importResources($csv_file_path, $resource_type);
        return $this->renderResponse(['success' => $results]);
    }

    public function importModels(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $resource_type = $request->input('resource_type') ?? 'models';

        $results = $this->designResourceRepository->importResources($csv_file_path, $resource_type);
        return $this->renderResponse(['success' => $results]);
    }



    // ================================ tab content =================================
    private function prepareParams(array $params, string $type): array
    {
        $params['model'] = 'design_resource';
        $params['type'] = $type;
        $params['fields'] = array(
            0 => '`design_resource`.`design_resource_id`',
            1 => '`design_resource`.`title`',
            2 => '`design_resource`.`link_text`',
            3 => '`design_resource`.`img`',
            4 => '`design_resource`.`slug`',
            5 => '`design_resource`.`description`',
            6 => '`design_resource`.`media_id`',
            7 => '`design_resource`.`resource_type`',
            8 => '`design_resource`.`grade`',
            9 => '`design_resource`.`is_featured`',
            10 => '`design_resource`.`img2`',
            11 => '`design_resource`.`type`',
            12 => '`design_resource`.`sort_order`',
        );
        return $params;
    }
    public function getDesignResourceImages(Request $request): Response
    {
        $queryParams = $request->query();
        $params = [];
        $params['per_page'] = (int) (isset($queryParams['per_page']) && $queryParams['per_page']*1 > 0 ? $queryParams['per_page'] : 60);
        $params['current_page'] = (int) (isset($queryParams['current_page']) && $queryParams['current_page']*1 > 0 ? $queryParams['current_page'] : 1);
        $params['offset'] = (int) ($queryParams['offset'] ?? 0);
        $params['context'] = $queryParams['context'] ?? null;
        $params['category'] = isset($queryParams['category']) &&$queryParams['category'] ?($queryParams['category']*1): null;
        $params['model_id'] = $queryParams['model_id'] ?? null;
        $params['model_name'] = $queryParams['model_name'] ?? null;
        $params['search_value'] = $queryParams['searchValue'] ?? null;
        $params = $this->prepareParams($params, 'images');
        $images = $this->designResourceRepository->getDesignResourceImagesComponentData($params);
        return $this->renderResponse($images);
    }

    public function getDesignResourceModels(Request $request): Response
    {

        $queryParams = $request->query();
        $params = [];
        // $params['per_page'] = (int) (isset($queryParams['per_page']) && $queryParams['per_page']*1 > 0 ? $queryParams['per_page'] : 60);
        $params['per_page'] = 250;
        $params['current_page'] = (int) (isset($queryParams['current_page']) && $queryParams['current_page']*1 > 0 ? $queryParams['current_page'] : 1);
        $params['offset'] = (int) ($queryParams['offset'] ?? 0);
        $params['context'] = $queryParams['context'] ?? null;
        $params['category'] = $queryParams['category'] ?? null;
        $params['model_id'] = $queryParams['model_id'] ?? null;
        $params['model_name'] = $queryParams['model_name'] ?? null;
        $params['search_value'] = $queryParams['searchValue'] ?? null;
        $params = $this->prepareParams($params, 'models');
        $designModel = $this->designResourceRepository->getDesignResourceModelsComponentData($params);
        return $this->renderResponse($designModel);
    }


    public function getDesignResourceDocuments(Request $request): Response
    {
        $queryParams = $request->query();
        $params = [];
        // $params['per_page'] = (int) (isset($queryParams['per_page']) && $queryParams['per_page']*1 > 0 ? $queryParams['per_page'] : 60);
        $params['per_page'] = 250;
        $params['current_page'] = (int) (isset($queryParams['current_page']) && $queryParams['current_page']*1 > 0 ? $queryParams['current_page'] : 1);
        $params['offset'] = (int) ($queryParams['offset'] ?? 0);
        $params['context'] = $queryParams['context'] ?? null;
        $params['category'] = $queryParams['category'] ?? null;
        $params['model_id'] = $queryParams['model_id'] ?? null;
        $params['model_name'] = $queryParams['model_name'] ?? null;
        $params['search_value'] = $queryParams['searchValue'] ?? null;
        $params = $this->prepareParams($params, 'documents');
        $designDocuments = $this->designResourceRepository->getDesignResourceDocumentsComponentData($params);
        return $this->renderResponse($designDocuments);
    }

    public function getDesignResourceFinishes(Request $request): Response
    {
        $queryParams = $request->query();
        $params = [];
        // $params['per_page'] = (int) (isset($queryParams['per_page']) && $queryParams['per_page']*1 > 0 ? $queryParams['per_page'] : 60);
        $params['per_page'] = 250;
        $params['current_page'] = (int) (isset($queryParams['current_page']) && $queryParams['current_page']*1 > 0 ? $queryParams['current_page'] : 1);
        $params['offset'] = (int) ($queryParams['offset'] ?? 0);
        $params['context'] = $queryParams['context'] ?? null;
        $params['category'] = $queryParams['category'] ?? null;
        $params['model_id'] = $queryParams['model_id'] ?? null;
        $params['model_name'] = $queryParams['model_name'] ?? null;
        $params = $this->prepareParams($params, 'finishes');
        $params['search_value'] = $queryParams['searchValue'] ?? null;
        $designfinishes = $this->designResourceRepository->getDesignResourceFinishesComponentData($params);
        return $this->renderResponse($designfinishes);
    }


    public function getDesignResourceTextiles(Request $request): Response
    {
        $queryParams = $request->query();
        $params = [];
        // $params['per_page'] = (int) (isset($queryParams['per_page']) && $queryParams['per_page']*1 > 0 ? $queryParams['per_page'] : 60);
        $params['per_page'] = 250;
        $params['current_page'] = (int) (isset($queryParams['current_page']) && $queryParams['current_page']*1 > 0 ? $queryParams['current_page'] : 1);
        $params['offset'] = (int) ($queryParams['offset'] ?? 0);
        $params['context'] = $queryParams['context'] ?? null;
        $params['category'] = $queryParams['category'] ?? null;
        $params['model_id'] = $queryParams['model_id'] ?? null;
        $params['model_name'] = $queryParams['model_name'] ?? null;
        $params = $this->prepareParams($params, 'textiles');
        $params['search_value'] = $queryParams['searchValue'] ?? null;
        $designtextiles = $this->designResourceRepository->getDesignResourceTextilesComponentData($params);
        return $this->renderResponse($designtextiles);
    }




    // public function getDesignResourceFinishesTextiles(Request $request): Response
    // {
    //     $params = $request->query();
    //     $params['fields'] = array (
    //         0 => '`project_image`.`image_link`',
    //         1 => '`project_image`.`image`',
    //         2 => '`project`.`slug`',
    //     );
    //     $params['joins'] = array (
    //         0 => 
    //         array (
    //           0 => 'project',
    //           1 => 'project_image.project_id',
    //           2 => ' = ',
    //           3 => 'project.project_id',
    //         ),
    //     );
    //     $params['model'] = 'project_image';
    //     $images = $this->designResourceRepository->getDesignResourceFinishesTextilesComponentData($params);
    //     return $this->renderResponse($images);
    // }
    // ================================= document models =================================
    // public function getDesignResource(Request $request): Response
    // {
    //     $resource_type = $request->input('resource_type') ?? 'documents';
    //     $designResources = $this->designResourceRepository->getDesignResource($resource_type);
    //     return $this->renderResponse($designResources);
    // }
    public function getDesignResourceById(Request $request, $id): Response
    {
        $resource_type = $request->query('resource_type') ?? 'documents';
        $designResource = $this->designResourceRepository->getDesignResourceById((int) $id, $resource_type);
        return $this->renderResponse($designResource);
    }
    public function getDesignResourceByIdByResourceType(Request $request, $id): Response
    {
        $resource_type = $request->query('resource_type') ?? 'documents';
        $designResource = $this->designResourceRepository->getDesignResourceByIdByResourceType((int) $id, $resource_type);
        return $this->renderResponse($designResource);
    }
    // document create
    public function createDesignResourceDocument(Request $request): Response
    {
        $data = $request->all();
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'type' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $designResource = $this->designResourceRepository->createDesignResource($data);
        return $this->renderResponse($designResource);
    }
    // document update
    public function updateDesignResourceDocument(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
                'media_id' => 'nullable|integer',
                'design_resource_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $designResource = $this->designResourceRepository->updateDesignResource($data, (int) $id);
        return $this->renderResponse($designResource);
    }
    // document delete
    public function deleteDesignResourceDocument(Request $request, $id): Response
    {
        $designResource = $this->designResourceRepository->deleteDesignResourceDocument((int) $id);
        return $this->renderResponse($designResource);
    }

    // model create
    public function createDesignResourceModel(Request $request): Response
    {
        $data = $request->all();
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'brand' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'type' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
                'media_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $designResource = $this->designResourceRepository->createDesignResource($data);
        return $this->renderResponse($designResource);
    }
    // model update
    public function updateDesignResourceModel(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'media_id' => 'nullable|integer',
                'design_resource_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $designResource = $this->designResourceRepository->updateDesignResource($data, (int) $id);
        return $this->renderResponse($designResource);
    }
    // model delete
    public function deleteDesignResourceModel(Request $request, $id): Response
    {
        $designResource = $this->designResourceRepository->deleteDesignResourceDocument((int) $id);
        return $this->renderResponse($designResource);
    }

    // finish create
    public function createDesignResourceFinish(Request $request): Response
    {
        $data = $request->all();
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'brand' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'type' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
                'media_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $designResource = $this->designResourceRepository->createDesignResource($data);
        return $this->renderResponse($designResource);
    }
    // finish update
    public function updateDesignResourceFinish(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'brand' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'type' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
                'media_id' => 'nullable|integer',
                'design_resource_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $designResource = $this->designResourceRepository->updateDesignResource($data, (int) $id);
        return $this->renderResponse($designResource);
    }
    // finish delete
    public function deleteDesignResourceFinish(Request $request, $id): Response
    {
        $designResource = $this->designResourceRepository->deleteDesignResourceDocument((int) $id);
        return $this->renderResponse($designResource);
    }
    // get option by finishes data
    public function getTextilesDataByType(Request $request, $grade): Response
    {
        // Decode the URL-encoded $option to a readable string (e.g., "Fabric%20D" => "Fabric D")
        $grade = urldecode((string) $grade);
        $designResource = $this->designResourceRepository->getTextilesDataByType($grade);
        return $this->renderResponse($designResource);
    }

    // textile create
    public function createDesignResourceTextile(Request $request): Response
    {
        $data = $request->all();
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'brand' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'type' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
                'media_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $designResource = $this->designResourceRepository->createDesignResource($data);
        return $this->renderResponse($designResource);
    }
    // textile update
    public function updateDesignResourceTextile(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'resource_type' => 'required|string',
                'link_text' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'grade' => 'nullable|string',
                'brand' => 'nullable|string',
                'hex_value' => 'nullable|string',
                'type' => 'nullable|string',
                'img' => 'nullable|array',
                'img2' => 'nullable|array',
                'media_id' => 'nullable|integer',
                'design_resource_id' => 'nullable|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $designResource = $this->designResourceRepository->updateDesignResource($data, (int) $id);
        return $this->renderResponse($designResource);
    }
    // textile delete
    public function deleteDesignResourceTextile(Request $request, $id): Response
    {
        $designResource = $this->designResourceRepository->deleteDesignResourceTextile((int) $id);
        return $this->renderResponse($designResource);
    }

    // upload design resource
    public function uploadDesignResource(Request $request, int $id): Response
    {
        $property = $request->input('property');
        $resource_type = $request->input('resource_type');
        $formats = $request->input('formats');
        if($formats){   
            $formats = json_decode($formats, true);
            $formats = array_column($formats, 'format', 'index');
        }
        // set default size
        $size = [
            'width' => 945,
            'height' => 630,
        ];
        // set upload folder
        match ($resource_type) {
            'documents' => $folder = 'media/design-resource/documents',
            'models' => $folder = 'media/design-resource/models',
            'finishes' => $folder = 'media/design-resource/finishes',
            'textiles' => $folder = 'media/design-resource/textiles',
        };

        if($property == 'image_thumb_url'){
            $folder = 'media/design-resource/finishes/258X258';
        }
        
        // upload files
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];
            if($formats){
            foreach ($files as $index => &$file) {
                    $file['format'] = $formats[$index*1] ?? '';
                }
            }

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                // 'upload_dir' => $request->input('upload_dir', $folder)
            ];
            if($property == 'img' || $property == 'img2'){
                $allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'image/jpg',
                ];
                if(!in_array($resource_type, ['models', 'documents'])){
                    $folder = 'media/Products/image';
                }
            }else{
                $allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'image/jpg',
                    'application/pdf',
                    'application/vnd.ms-excel',                       // Excel (xls)
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel (xlsx)
                    'application/vnd.google-apps.spreadsheet',         // Google Sheets
                    'application/zip',                                 // zip
                    'application/x-zip-compressed',
                    'multipart/x-zip',
                    'application/x-compressed',
                ];
            }
            
            // old upload 
            if(in_array($resource_type, ['models', 'documents'])){
                // $result = $this->mediaRepository->uploadFiles($data, $allowedTypes, $folder);
                $result = $this->mediaRepository->upload($data, $allowedTypes, $folder);
            }else{
                $result = $this->mediaRepository->upload($data, $allowedTypes, $folder);
            }
            
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
            if ($id) {
                if ($property == 'img' || $property == 'img2' || $property == 'image_thumb_url') {
                    // upload design resource image
                    $designResource = $this->designResourceRepository->uploadDesignResource($result['files'], $property, $resource_type, $id);
                } else {
                    // upload design resource document
                    $designResource = $this->designResourceRepository->uploadDesignResourceDocument($result['files'], $resource_type, $id);
                }
            }

            return $this->renderResponse($property == 'img' || $property == 'img2' ? $result : $designResource);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    public function updateModelDocumentFormat(Request $request): Response
    {
        $document = $request->validate([
            'design_resource_document_id' => 'required|integer',
            'format' => 'required|string',
        ]);
        if(!$document){
            return $this->renderError(422, 'Document is required');
        }
        $designResource = $this->designResourceRepository->updateModelDocumentFormat((array) $document);
        if(!$designResource){
            return $this->renderError(422, 'Failed to update document format');
        }
        return $this->renderResponse($designResource);
    }

    public function getDesignResourceDocumentTypes(Request $request): Response
    {
        $resource_type = $request->query('resource_type') ?? 'documents';
        if (!in_array($resource_type, ['documents', 'models', 'finishes', 'textiles'])) {
            return $this->renderError(422, 'Invalid resource type');
        }
        $documentTypes = $this->designResourceRepository->getDesignResourceDocumentTypes($resource_type);
        return $this->renderResponse($documentTypes);
    }

    public function deleteDesignResourceDocRecord(Request $request, $id): Response
    {
        $designResource = $this->designResourceRepository->deleteDesignResourceDocRecord((int) $id);
        return $this->renderResponse($designResource);
    }


    // account resource filter api
    public function filterCategoriesByContextType(Request $request, $context_type, $resource_type = ""): Response
    {
        $contextType = $this->designResourceRepository->filterCategoriesByContextType($context_type, $resource_type);
        return $this->renderResponse($contextType);
    }

    public function filterCategroyIdByCategoryName(Request $request, $category_id): Response
    {
        $categoryId = $this->designResourceRepository->filterCategroyIdByCategoryName((int) $category_id);
        return $this->renderResponse($categoryId);
    }

    public function filterModelNameByModelId(Request $request): Response
    {
        $model_type = $request->query('context')??'';
        $model_id = $request->query('category')??0;
        $model_name = $request->query('search')??'';
        $modelName = $this->designResourceRepository->filterModelNameByModelId($model_type, (int) $model_id, $model_name);
        return $this->renderResponse($modelName);
    }

    public function globalSearch(Request $request): Response
    {
        $queryString = $request->query('query')??'';
        $results = $this->designResourceRepository->globalSearch($queryString);
        return $this->renderResponse($results);
    }

    public function getPopularSearch(Request $request): Response
    {
        $results = $this->designResourceRepository->getPopularSearch();
        return $this->renderResponse($results);
    }

    public function globalSearchByContext(Request $request): Response
    {
        $queryString = $request->query('query') ?? '';
        $contexts = $request->query('contexts') ?? '';
        $allQuery = $request->allQuery();
        $perPage = max(1, (int) ($allQuery['per_page'] ?? 40));

        $hasExplicitCurrentPage =
            \array_key_exists('current_page', $allQuery)
            || \array_key_exists('page', $allQuery);

        $pageCandidate = isset($allQuery['page'])
            ? (int) $allQuery['page']
            : (isset($allQuery['current_page']) ? (int) $allQuery['current_page'] : 1);
        $pageCandidate = max(1, $pageCandidate);

        if (\array_key_exists('offset', $allQuery)) {
            $offset = max(0, (int) $allQuery['offset']);
            $currentPage = $pageCandidate;
            if (!$hasExplicitCurrentPage && $perPage > 0) {
                $currentPage = intdiv($offset, $perPage) + 1;
            }
            $currentPage = max(1, $currentPage);
        } elseif ($hasExplicitCurrentPage && $pageCandidate > 1) {
            // Full refresh / deep link: cumulatively load pages 1..N by keeping page and resetting offset (offset=0 ⇒ limit=N*per_page).
            $currentPage = $pageCandidate;
            $offset = 0;
        } else {
            $currentPage = max(1, $pageCandidate);
            $offset = ($currentPage - 1) * $perPage;
        }

        $results = $this->designResourceRepository->globalSearchByContext(
            $queryString,
            $contexts,
            $perPage,
            $currentPage,
            $offset,
        );
        return $this->renderResponse($results);
    }

    public function getResourcesByDesk(Request $request): Response
    {
        $search_query = $request->query('search_query')??'';
        $resource_type = $request->query('resource_type')??'';
        $resources = $this->designResourceRepository->getResourcesByDesk($search_query, $resource_type);
        return $this->renderResponse($resources);
    }

    public function deleteDesignResourceByIds(Request $request): Response
    {
        try {
            $data = $request->validate([
                'resource_document_ids' => 'required|array',
                'property' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $ids = array_values(array_filter(
            array_map('intval', $data['resource_document_ids']),
            static fn (int $id): bool => $id > 0,
        ));

        if ($ids === []) {
            return $this->renderError(422, 'No valid resource document ids provided');
        }

        $property = $data['property'] ?? 'models';
        $designResource = $this->designResourceRepository->deleteDesignResourceModelById($ids, $property);
        return $this->renderResponse($designResource);
    }

    public function relatedResourceSearch(Request $request): Response
    {
        $relatedResources = $this->designResourceRepository->relatedResourceSearch($request->input('search'));
        return $this->renderResponse($relatedResources);
    }
}
