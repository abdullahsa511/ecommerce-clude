<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ProductOptionRepositoryInterface;
use App\Core\Exceptions\ValidationException;

/**
 * All variant setup related data control from here.
 */
class ProductOptionController extends ApiController
{
    private ProductOptionRepositoryInterface $productOptionRepository;
    public function __construct(
        ProductOptionRepositoryInterface $productOptionRepository
    ) {
        parent::__construct();
        $this->productOptionRepository = $productOptionRepository;
    }

    /**
     * Fetch all variants
     */
    public function getProductOptions(Request $request): Response
    {
        $data = $this->productOptionRepository->getProductOptions();
        return $this->renderResponse($data);
    }


    public function getProductOptionById(Request $request, $id): Response
    {
        $data = $this->productOptionRepository->getProductOptionById((int) $id);
        if (!$data) {
            throw new ValidationException([
                'global_message' => ['Product option not found'],
            ]);
        }
        return $this->renderResponse($data);
    }

    public function createProductOption(Request $request): Response
    {
        try {
            $data = $request->all();
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
            $validatedData = $request->validate([
                'product_id' => 'required|int',
                'product_variant_id' => 'required|int',
                'product_option_group_id' => 'required|int',
                'option_name' => 'required|string',
                'price' => 'nullable',
                'type_id' => 'nullable|int',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|int',
                'active_status' => 'nullable|int',
            ], $data);
            $uniqueCheck = $this->productOptionRepository->isProductOptionUnique($validatedData);
            if ($uniqueCheck) {
                throw new ValidationException([
                    'global_message' => ['Product option with the same product id, product variant id, product option group id and option name already exists.'],
                    'product_id' => [''],
                    'product_variant_id' => [''],
                    'product_option_group_id' => [''],
                    'option_name' => [''],
                ]);
            }
            // Proceed to create option
            $productOption = $this->productOptionRepository->createProductOption($validatedData);
            return $this->renderResponse($productOption);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function updateProductOption(Request $request, $id): Response
    {
        try {
            $data = $request->all();
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
            $validatedData = $request->validate([
                'product_option_id' => 'required|int',
                'product_id' => 'required|int',
                'product_variant_id' => 'nullable|int',
                'product_option_group_id' => 'nullable|int',
                'option_name' => 'required|string',
                'price' => 'nullable',
                'type_id' => 'nullable|int',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|int',
                'active_status' => 'nullable|int',
            ], $data);

            $uniqueCheck = $this->productOptionRepository->isProductOptionUnique($validatedData, (int) $id);
            if ($uniqueCheck) {
                throw new ValidationException([
                    'global_message' => ['Product option name with the same product variant id, product option group id already exists.'],
                    'product_id' => [''],
                    'product_variant_id' => [''],
                    'product_option_group_id' => [''],
                    'option_name' => [''],
                ]);
            }
            // Proceed to create option
            $productOption = $this->productOptionRepository->updateProductOption((int) $id, $validatedData);
            return $this->renderResponse($productOption);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function deleteProductOption(Request $request, $id): Response
    {
        try {
            $productOption = $this->productOptionRepository->deleteProductOption((int) $id);
            return $this->renderResponse($productOption);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importProductOptions(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productOptionRepository->importProductOptions($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function searchProductOptions(Request $request): Response
    {
        $name = $request->query('option_name');
        $product_id = $request->query('product_id');
        $productOptions = $this->productOptionRepository->searchProductOptions($name, (int) $product_id);
        return $this->renderResponse($productOptions);
    }

    public function searchItemOptionsByQuery(Request $request): Response
    {
        $name = $request->query('query');
        $product_id = $request->query('product_id');
        $product_variant_id = $request->query('product_variant_id');
        $product_option_group_id = $request->query('product_option_group_id');
        $productOptions = $this->productOptionRepository->searchItemOptionsByQuery($name, (int) $product_id, (int) $product_variant_id, (int) $product_option_group_id);
        return $this->renderResponse($productOptions);
    }
}
