<?php

declare(strict_types=1);

namespace App\Core\Repositories\Coupon;

use App\Core\Models\Coupon\Coupon;
use App\Core\Models\Coupon\CouponData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface CouponRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all coupons
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get coupons by status
     *
     * @param int $status
     * @return array
     */
    public function findByStatus(int $status): array;

    /**
     * Get coupon by code
     *
     * @param string $code
     * @return Coupon|null
     */
    public function findByCode(string $code): ?Coupon;

    /**
     * Create a new coupon
     *
     * @param CouponData $couponData
     * @return Coupon
     */
    public function createCoupon(CouponData $couponData): Coupon;

    /**
     * Update an existing coupon
     *
     * @param CouponData $couponData
     * @return Coupon
     */
    public function updateCoupon(CouponData $couponData): Coupon;

    /**
     * Get a coupon by ID
     *
     * @param int $couponId
     * @return Coupon
     */
    public function showCoupon(int $couponId): Coupon;

    /**
     * Delete a coupon
     *
     * @param int $id
     * @return bool
     */


    public function deleteCoupon(int $id): ?Coupon;

    public function delete(int $id): bool;
} 