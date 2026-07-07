<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\ProductOptionGroup\ProductOptionGroupRepositoryInterface;

/**
 * All variant setup related data control from here.
 */
class ProductOptionGroupController extends ApiController
{
    private ProductOptionGroupRepositoryInterface $productOptionGroupRepository;
    public function __construct(
        ProductOptionGroupRepositoryInterface $productOptionGroupRepository
    ) {
        parent::__construct();
        $this->productOptionGroupRepository = $productOptionGroupRepository;
    }

    /**
     * Fetch all variants
     */
    public function getProductOptionGroups(Request $request): Response
    {
        $data = $this->productOptionGroupRepository->getProductOptionGroups();
        return $this->renderResponse($data);
    }


    public function getProductOptionGroupById(Request $request, $id): Response
    {
        $data = $this->productOptionGroupRepository->getProductOptionGroupById($id);
        return $this->renderResponse($data);
    }

    public function createProductOptionGroup(Request $request): Response
    {
        try {
            $data = $request->all();
            if ($data instanceof Response) {
                return $data;
            }

            $request->validate([
                'option_group_name' => 'required|string',
                'product_id' => 'required|int',
                'product_variant_id' => 'required|int',
                'sort_order' => 'required|int',
            ], $data);

            $existingName = $this->productOptionGroupRepository->findByName($data);
            if ($existingName) {
                return $this->renderError(400, 'Product Optin group name is already in use.');
            }
           
            // Proceed to create option
            $productOptionGroup = $this->productOptionGroupRepository->createProductOptionGroup($data);
            return $this->renderResponse($productOptionGroup);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function updateProductOptionGroup(Request $request, $id): Response
    {
        $data = $request->all();

        if ($data instanceof Response) {
            return $data;
        }
        try {
            $request->validate([
                // 'option_group_name' => 'required|string|notExists:`product_option_group`,option_group_name ,product_option_group_id ,' . (int) $id,
                'option_group_name' => 'required|string',
                'product_id' => 'required|int',
                'product_variant_id' => 'required|int',
                'sort_order' => 'required|int',
            ], $data);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        
        $this->productOptionGroupRepository->clearQuery();
        $existingProductOptionGroup = $this->productOptionGroupRepository->getProductOptionGroupById($id);
        if (!$existingProductOptionGroup) {
            return $this->renderError(400, 'Product option group with the id ' . $id . ' not found.');
        }
        $productOptionGroup = $this->productOptionGroupRepository->updateProductOptionGroup($data, $id);
        return $this->renderResponse($productOptionGroup);
    }

    public function deleteProductOptionGroup(Request $request, $id): Response
    {
        try {
            $productOptionGroup = $this->productOptionGroupRepository->deleteProductOptionGroup((int) $id);
            return $this->renderResponse($productOptionGroup);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importProductOptionGroups(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productOptionGroupRepository->importProductOptionGroups($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function searchProductOptionGroups(Request $request): Response
    {
        $name = $request->query('option_group_name');
        // $product_id = $request->query('product_id');
        $product_id = $request->input('product_id') ?? $request->query('product_id');
        $product_variant_id = $request->input('product_variant_id') ?? $request->query('product_variant_id');
        $productOptionGroups = $this->productOptionGroupRepository->searchProductOptionGroups($name,(int) $product_id,(int) $product_variant_id);
        return $this->renderResponse($productOptionGroups);
    }

    public function searchItemOptionGroups(Request $request): Response
    {
        $name = $request->query('option_group_name');
        // $product_id = $request->query('product_id');
        $product_id =$request->query('product_id');
        $product_variant_id = $request->query('product_variant_id');
        $productOptionGroups = $this->productOptionGroupRepository->searchItemOptionGroups($name,(int) $product_id,(int) $product_variant_id);
        return $this->renderResponse($productOptionGroups);
    }
    
    public function searchItemOptionGroupsByQuery(Request $request): Response
    {
        $name = $request->query('query');
        // $product_id = $request->query('product_id');
        $product_id =$request->query('product_id');
        $product_variant_id = $request->query('product_variant_id');
        $productOptionGroups = $this->productOptionGroupRepository->searchItemOptionGroupsByQuery($name,(int) $product_id,(int) $product_variant_id);
        return $this->renderResponse($productOptionGroups);
    }
}
