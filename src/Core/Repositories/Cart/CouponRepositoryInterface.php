<?php

declare(strict_types=1);

namespace App\Core\Repositories\Cart;

use App\Core\Models\Cart\Coupon;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface CouponRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all coupons with pagination
     * 
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @return array{data: array, total: int}
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get coupon by various criteria
     * 
     * @param int|null $coupon_id Filter by coupon ID
     * @param string|null $code Filter by coupon code
     * @param int|null $free_shipping Filter by free shipping status
     * @param int|null $status Filter by coupon status
     * @return Coupon|null
     */
    public function get(int $couponId): ?Coupon;


    /**
     * Get coupon taxonomies
     * 
     * @param int $coupon_id Coupon ID
     * @param int $language_id Language ID
     * @return array
     */
    public function getTaxonomies(int $coupon_id, int $language_id): array;

    /**
     * Get coupon products
     * 
     * @param int $coupon_id Coupon ID
     * @param int $language_id Language ID
     * @return array
     */
    public function getProducts(int $coupon_id, int $language_id): array;

    /**
     * Set coupon taxonomies
     * 
     * @param array $coupon_taxonomy Array of taxonomy item IDs
     * @param int $coupon_id Coupon ID
     * @return bool
     */
    public function setTaxonomies(array $coupon_taxonomy, int $coupon_id): bool;

    /**
     * Set coupon products
     * 
     * @param array $coupon_product Array of product IDs
     * @param int $coupon_id Coupon ID
     * @return bool
     */
    public function setProducts(array $coupon_product, int $coupon_id): bool;
} 