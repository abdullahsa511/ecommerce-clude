<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Coupon\CouponProductRepositoryInterface;
use App\Core\Models\Coupon\CouponResponse;
use App\Core\Models\Coupon\CouponItemData;

class CouponItemController extends ApiController
{
    private CouponProductRepositoryInterface $couponProductRepository;

    public function __construct(CouponProductRepositoryInterface $couponProductRepository)
    {
        parent::__construct();
        $this->couponProductRepository = $couponProductRepository;
    }

    /**
     * Get all quotes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $quotes = $this->couponProductRepository->findAll();
        return $this->renderResponse($quotes);
    }

    /**
     * Get quote by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $couponProducts = $this->couponProductRepository->findByCouponId((int)$id);
        if(!$couponProducts){
            return $this->renderError(404, 'Coupon products not found');
        }
        return $this->renderResponse($couponProducts);
    }

    /**
     * Create a new quote
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $couponProducts = $request->input('couponProduct');

            $couponProducts = $this->couponProductRepository->createCouponProducts($couponProducts);
            if(!$couponProducts){
                return $this->renderError(500, 'Failed to create coupon products');
            }
            
            return $this->renderResponse([
                'message' => 'Coupon products created successfully',
                'data' => $couponProducts
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to create coupon products: ' . $e->getMessage());
        }
    }

    public function productList(Request $request, $id): Response
    {
        $productList = $this->couponProductRepository->productList($id);
        return $this->renderResponse($productList);
    }
} 