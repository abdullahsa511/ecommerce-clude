<?php

declare(strict_types=1);

namespace App\Core\Repositories\Cart;

use App\Core\Models\Cart\Coupon;
use App\Core\Models\Cart\CouponProduct;
use App\Core\Models\Cart\CouponTaxonomy;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    protected CouponProduct $couponProduct;
    protected CouponTaxonomy $couponTaxonomy;

    public function __construct(PDO $db, CouponProduct $couponProduct, CouponTaxonomy $couponTaxonomy)
    {
        parent::__construct($db, 'coupon', Coupon::class);
        $this->couponProduct = $couponProduct;
        $this->couponProduct->setDb($db);
        $this->couponTaxonomy = $couponTaxonomy;
        $this->couponTaxonomy->setDb($db);
    }

    /**
     * Get all coupons with pagination
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $query->orderBy('status', 'DESC')
              ->orderBy('coupon_id', 'ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    /**
     * Get coupon by various criteria
     */
    public function get(int $couponId): ?Coupon
    {
        $query = $this->model
            ->where('coupon_id', '=', $couponId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    /**
     * Get coupon taxonomies
     */
    public function getTaxonomies(int $coupon_id, int $language_id): array
    {
        $query = $this->model->select(['*'])
                            ->where('coupon_id', '=', $coupon_id)
                            ->with(['couponTaxonomy' => function($query) use ($language_id) {
                                $query->join('taxonomy_item_content', 'coupon_taxonomy.taxonomy_item_id', '=', 'taxonomy_item_content.taxonomy_item_id')
                                      ->where('taxonomy_item_content.language_id', '=', $language_id);
                            }]);

        $result = $query->findAll();
        return !empty($result) ? $result[0]['coupon_taxonomy_data'] : [];
    }

    /**
     * Get coupon products
     */
    public function getProducts(int $coupon_id, int $language_id): array
    {
        $query = $this->model->select(['*'])
                            ->where('coupon_id', '=', $coupon_id)
                            ->with(['couponProduct' => function($query) use ($language_id) {
                                $query->join('product_content', 'coupon_product.product_id', '=', 'product_content.product_id')
                                      ->where('product_content.language_id', '=', $language_id);
                            }]);

        $result = $query->findAll();
        return !empty($result) ? $result[0]['coupon_product_data'] : [];
    }

    /**
     * Set coupon taxonomies
     */
    public function setTaxonomies(array $coupon_taxonomy, int $coupon_id): bool
    {
        // First delete existing taxonomies
        $this->couponTaxonomy->where('coupon_id', '=', $coupon_id)->deleteMultiple([$coupon_id]);

        // Then insert new taxonomies
        $success = true;
        foreach ($coupon_taxonomy as $taxonomy_item_id) {
            $data = [
                'coupon_id' => $coupon_id,
                'taxonomy_item_id' => $taxonomy_item_id
            ];
            if (!$this->couponTaxonomy->create($data)) {
                $success = false;
                break;
            }
        }

        return $success;
    }

    /**
     * Set coupon products
     */
    public function setProducts(array $coupon_product, int $coupon_id): bool
    {
        // First delete existing products
        $this->couponProduct->where('coupon_id', '=', $coupon_id)->deleteMultiple([$coupon_id]);

        // Then insert new products
        $success = true;
        foreach ($coupon_product as $product_id) {
            $data = [
                'coupon_id' => $coupon_id,
                'product_id' => $product_id
            ];
            if (!$this->couponProduct->create($data)) {
                $success = false;
                break;
            }
        }

        return $success;
    }


} 