<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Coupon\CouponRepositoryInterface;
use App\Core\Repositories\Coupon\CouponProductRepositoryInterface;
use App\Core\Models\Coupon\CouponData;
use App\Core\Models\Coupon\CouponResponse;

class CouponController extends ApiController
{
    private CouponRepositoryInterface $couponRepository;
    private CouponProductRepositoryInterface $couponProductRepository;

    public function __construct(
        CouponRepositoryInterface $couponRepository,
        CouponProductRepositoryInterface $couponProductRepository
    ) {
        parent::__construct();
        $this->couponRepository = $couponRepository;
        $this->couponProductRepository = $couponProductRepository;
    }

    /**
     * Get all coupons
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $coupons = $this->couponRepository->all();
        return $this->renderResponse($coupons);
    }

    /**
     * Get coupon by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        // // $coupon = $this->couponRepository->showCoupon((int)$id);
        // $coupon = $this->couponRepository->find((int)$id);
        // if(!$coupon){
        //     return $this->renderError(404, 'Coupon not found');
        // }
        // // $response = new CouponResponse($coupon->data);
        // return $this->renderResponse($coupon->data);


        $coupon = $this->couponRepository->showCoupon((int)$id);
        if(!$coupon){
            return $this->renderError(404, 'Coupon not found');
        }
        $response = new CouponResponse($coupon->data);
        return $this->renderResponse($response);

    }

    /**
     * Create a new coupon
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $coupon = $request->input('coupon');
            $couponDetails = $coupon['couponDetails'];
            // validate coupon data
            $request->validate([
                'name' => 'required|string',
                'code' => 'required|string',
                'discount' => 'required|numeric',
                'type' => 'required|string',
                'free_shipping' => 'required|boolean',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
            ], $couponDetails);
            // validate coupon

            $couponData = new CouponData($coupon);
            $couponProducts = $coupon['couponProducts'] ?? [];
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $coupon = $this->couponRepository->createCoupon($couponData);
        if(!$coupon){
            return $this->renderError(500, 'Failed to create coupon');
        }

        // Handle coupon products if provided
        if(!empty($couponProducts)){
            // Add coupon_id to each product
            $couponProductsWithId = array_map(function($product) use ($coupon) {
                return [
                    // 'coupon_id' => $coupon->coupon_id,
                    'product_id' => $product['product_id']
                ];
            }, $couponProducts);
            
            $this->couponProductRepository->createCouponProducts($couponProductsWithId);
        }

        $response = new CouponResponse($coupon->data);
        return $this->renderResponse($response);
    }

    /**
     * Update a coupon
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $requests = $request->all();
            $coupon = $requests['updatedCoupon'];
            $couponDetails = $coupon['couponDetails'];
            // validate coupon data
            $request->validate([
                'name' => 'required|string',
                'code' => 'required|string',
                'discount' => 'required|numeric',
                'type' => 'required|string',
                'free_shipping' => 'required|boolean',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
            ], $couponDetails);
            // validate coupon

            // $coupon = $request->input('coupon');
            $couponData = new CouponData($coupon);
            $couponProducts = $coupon['couponProducts'] ?? [];
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $coupon = $this->couponRepository->updateCoupon($couponData);
        if(!$coupon){
            return $this->renderError(500, 'Failed to update coupon');
        }

        // Handle coupon products if provided
        if(!empty($couponProducts)){
            // Delete existing coupon products first
            $this->couponProductRepository->deleteByCouponId($id);
            
            // Add coupon_id to each product
            $couponProductsWithId = array_map(function($product) use ($coupon) {
                return [
                    'coupon_id' => $coupon->coupon_id,
                    'product_id' => $product['product_id']
                ];
            }, $couponProducts);
            
            $this->couponProductRepository->createCouponProducts($couponProductsWithId);
        }

        $response = new CouponResponse($coupon->data);
        return $this->renderResponse($response);
    }

    /**
     * Delete a coupon
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteCoupon(Request $request, $id): Response
    {
        $coupon = $this->couponRepository->showCoupon((int) $id);
        if (!$coupon) {
            return $this->renderError(404, 'Coupon not found');
        }

        try {
            // Delete associated coupon products first
            $this->couponProductRepository->deleteByCouponId((int) $id);
            
            // Delete the coupon
            $this->couponRepository->deleteCoupon((int) $id);
            return $this->renderResponse(['message' => 'Coupon deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete coupon: ' . $e->getMessage());
        }
    }

    public function importCoupons(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->couponRepository->importCSVs($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
} 