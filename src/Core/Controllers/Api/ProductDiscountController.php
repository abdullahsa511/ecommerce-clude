<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ProductDiscountRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;

class ProductDiscountController extends ApiController
{
    private ProductDiscountRepositoryInterface $productDiscountRepository;

    public function __construct(
        ProductDiscountRepositoryInterface $productDiscountRepository,
    ) {
        parent::__construct();
        $this->productDiscountRepository = $productDiscountRepository;
    }

    /**
     * Get all length types.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $productDiscounts = $this->productDiscountRepository->findAll();
        return $this->renderResponse($productDiscounts);
    }

    /**
     * Get a length type by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $productDiscount = $this->productDiscountRepository->find((int)$id);
        if (!$productDiscount) {
            return $this->renderError(404, 'Length type not found');
        }
        return $this->renderResponse($productDiscount->data);
    }

    /**
     * Create a new length type.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->all();
            if ($data instanceof Response) {
                return $data;
            }

            $request->validate([
                'product_id' => 'required|int',
                'user_group_id' => 'required|int',
                'price' => 'required',
                'quantity' => 'nullable|int',
                'priority' => 'nullable|int',
                'from_date' => 'required',
                'to_date' => 'required'
            ], $data);
            $messages = [];
            // check integer quantity and priority are not negative
            if (isset($data['quantity'])) {
                $data['quantity'] = (int)$data['quantity'];
                $data['quantity'] < 1 ? $messages['quantity'] = ['Quantity must be an integer.'] : null;
            }
            if (isset($data['priority'])) {
                $data['priority'] = (int)$data['priority'];
                $data['priority'] < 1 ? $messages['priority'] = ['Priority must be an integer.'] : null;
            }
            // date format from_date and to_date
            if (isset($data['from_date'])) {
                $data['from_date'] = date('Y-m-d', strtotime($data['from_date']));
            }
            if (isset($data['to_date'])) {
                $data['to_date'] = date('Y-m-d', strtotime($data['to_date']));
            }

            if (count($messages) > 0) {
                return $this->renderError(422, 'Validation error.', $messages);
            }

            $productDiscount = $this->productDiscountRepository->createProductDiscount($data);
            return $this->renderResponse($productDiscount);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update a length type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        $data = $request->all();
        if ($data instanceof Response) {
            return $data;
        }
        try {
            $request->validate([
                'product_id' => 'required|int',
                'user_group_id' => 'required|int',
                'price' => 'required',
                'from_date' => 'required',
                'to_date' => 'required'
            ], $data);
            $messages = [];
            // check integer quantity and priority are not negative
            if (isset($data['quantity'])) {
                $data['quantity'] = (int)$data['quantity'];
                $data['quantity'] < 1 ? $messages['quantity'] = ['Quantity must be an integer.'] : null;
            }
            if (isset($data['priority'])) {
                $data['priority'] = (int)$data['priority'];
                $data['priority'] < 1 ? $messages['priority'] = ['Priority must be an integer.'] : null;
            }
            // date format from_date and to_date
            if (isset($data['from_date'])) {
                $data['from_date'] = date('Y-m-d', strtotime($data['from_date']));
            }
            if (isset($data['to_date'])) {
                $data['to_date'] = date('Y-m-d', strtotime($data['to_date']));
            }

            if (count($messages) > 0) {
                return $this->renderError(422, 'Validation error.', $messages);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        $existingProductDiscount = $this->productDiscountRepository->find((int)$id);
        if (!$existingProductDiscount) {
            return $this->renderError(404, 'Length type not found');
        }

        $productDiscount = $this->productDiscountRepository->updateProductDiscount((int)$id, $data);
        if (!$productDiscount) {
            return $this->renderError(500, 'Failed to update length type');
        }

        return $this->renderResponse($productDiscount);
    }

    /**
     * Delete a length type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $variant = $this->productDiscountRepository->deleteProductDiscount((int) $id);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importProductDiscounts(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            // $result = $this->productDiscountRepository->importStatuses($csv_file_path, 'length_type_id');
            $result = $this->productDiscountRepository->importCSVs($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
}
