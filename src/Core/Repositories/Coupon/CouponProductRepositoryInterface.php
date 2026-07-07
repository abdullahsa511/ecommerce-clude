<?php

declare(strict_types=1);

namespace App\Core\Repositories\Coupon;

use App\Core\Models\Coupon\CouponProduct;
use App\Core\Models\Coupon\CouponProductData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface CouponProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create coupon products
     *
     * @param array $couponProducts
     * @return array
     */
    public function createCouponProducts(array $couponProducts): array;

    /**
     * Update an existing coupon product
     *
     * @param CouponProductData $couponProductData
     * @return CouponProduct
     */
    public function updateCouponProduct(CouponProductData $couponProductData): CouponProduct;

    /**
     * Get coupon products by coupon ID
     *
     * @param int $couponId
     * @return array
     */
    public function findByCouponId(int $couponId): array;

    /**
     * Delete coupon products by coupon ID
     *
     * @param int $couponId
     * @return bool
     */
    public function deleteByCouponId(int $couponId): bool;

    public function productList($id): array;
} 