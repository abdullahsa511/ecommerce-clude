<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ProductTypeRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class ProductTypeController extends ApiController
{
    private ProductTypeRepositoryInterface $productTypeRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        ProductTypeRepositoryInterface $productTypeRepository,
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->productTypeRepository = $productTypeRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all product types with optional filtering
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $productTypes = $this->productTypeRepository->findAll();
        return $this->renderResponse($productTypes);
    }

    /**
     * Get a single product type by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {

        $productType = $this->productTypeRepository->find((int)$id);
        if(!$productType){
            return $this->renderError(404, 'Product type not found');
        }
        $data = $productType->data;
        if(!$data->image){
            $data->image = [];
        }else{
            $data->image = [
                [
                    'product_type_image_id' => $id,
                    'image' => $data->image,
                    'objectURL' => $data->image ?? '',
                    'size' => 256,
                    'type' => 'image/jpeg',
                    'status' => [
                        'name' => 'Uploaded',
                        'severity' => 'success'
                    ],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        }
        return $this->renderResponse($data);
    }

    /**
     * Create a new product type
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'type' => 'required|string',
                'plural' => 'required|string',
                'icon' => 'required|string',
                'site_id' => 'required|integer',
                'source' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $productType = $this->productTypeRepository->create($data);
        return $this->renderResponse($productType->data);
    }

    /**
     * Update an existing product type
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'string|required',
                'type' => 'string|required',
                'plural' => 'string|nullable',
                'icon' => 'string|nullable',
                'site_id' => 'integer|nullable',
                'source' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingProductType = $this->productTypeRepository->find((int)$id);
        if (!$existingProductType) {
            return $this->renderError(404, 'Product type not found');
        }
        unset($data['image']);
        $productType = $this->productTypeRepository->update((int)$id, $data);
        if (!$productType) {
            return $this->renderError(500, 'Failed to update product type');
        }
        
        return $this->renderResponse($productType->data);
    }

    /**
     * Delete a product type
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $existingProductType = $this->productTypeRepository->find((int)$id);
        if (!$existingProductType) {
            return $this->renderError(404, 'Product type not found');
        }

        $result = $this->productTypeRepository->delete((int)$id);
        if (!$result) {
            return $this->renderError(500, 'Failed to delete product type');
        }
        
        return $this->renderResponse(['message' => 'Product type deleted successfully']);
    }

    // importProductTypes
    public function importProductTypes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productTypeRepository->importProductTypes($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function upload(Request $request, int $product_type_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/product-types/';
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->productTypeRepository->updateProductTypeImage($result['files'], $product_type_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete vendor image
    public function deleteImage(Request $request, int $product_type_id): Response
    {
        $deleted = $this->productTypeRepository->deleteProductTypeImage($product_type_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }

}
