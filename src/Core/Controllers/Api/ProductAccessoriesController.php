<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ProductAccessoriesRepositoryInterface;


class ProductAccessoriesController extends ApiController
{
    private ProductAccessoriesRepositoryInterface $productAccessoriesRepository;


    public function __construct(
        ProductAccessoriesRepositoryInterface $productAccessoriesRepository,
    )
    {
        parent::__construct();
        $this->productAccessoriesRepository = $productAccessoriesRepository;
    }


    public function index(Request $request): Response
    {
        // $result = $this->productAccessoriesRepository->findAll();

        $result = $this->productAccessoriesRepository->getAccessoriesData();

        return $this->renderResponse($result);
    }

    public function getAccessoriesById(Request $request, $id): Response
    {
        $result = $this->productAccessoriesRepository->getAccessoriesById((int) $id);
        return $this->renderResponse($result);
    }


    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'status' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingProductAccessories = $this->productAccessoriesRepository->find((int)$id);
        if (!$existingProductAccessories) {
            return $this->renderError(404, 'Product accessories not found');
        }

        $productAccessories = $this->productAccessoriesRepository->update((int) $id, $data);
        if (!$productAccessories) {
            return $this->renderError(500, 'Failed to update product accessories');
        }
        
        return $this->renderResponse($productAccessories->data);
    }

    public function delete(Request $request, $id): Response
    {
        try {
            $this->productAccessoriesRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Product accessories deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete product accessories: ' . $e->getMessage());
        }
    }

    //import
    public function importAccessories(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        try {
            $result = $this->productAccessoriesRepository->importAccessories($csv_file_path);
            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

} 