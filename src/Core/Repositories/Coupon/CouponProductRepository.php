<?php

declare(strict_types=1);

namespace App\Core\Repositories\Coupon;

use PDO;
use App\Core\Models\Coupon\CouponProduct;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Coupon\CouponProductData;
use App\Core\Models\Product\Product;

class CouponProductRepository extends BaseRepository implements CouponProductRepositoryInterface
{
    private Product $product;
    public function __construct(PDO $db, Product $product)
    {
        parent::__construct($db, 'coupon_product', CouponProduct::class);
        $this->product = $product;
        $this->product->setDb($db);
    }

    public function createCouponProducts(array $couponProducts): array
    {
        $mappedProducts = array_map(function($product) {
            return [
                'coupon_id' => $product['coupon_id'],
                'product_id' => $product['product_id']
            ];
        }, $couponProducts);

        $this->model->upsert($mappedProducts, ['coupon_id', 'product_id']);
        return $mappedProducts;
    }

    public function updateCouponProduct(CouponProductData $couponProductData): CouponProduct
    {
        $couponProductDataArray = $couponProductData->toArray();
        $couponProduct = $this->model->find($couponProductDataArray['coupon_product_id']);
        $couponProduct = $couponProduct->update($couponProductDataArray);

        return $couponProduct;
    }

    public function findByCouponId(int $couponId): array
    {
        return $this->model->where('coupon_id', '=', $couponId)->findAll();
    }

    public function deleteByCouponId(int $couponId): bool
    {
        $couponProducts = $this->model->where('coupon_id', '=', $couponId)->findAll();
        $deleted = true;
        
        foreach ($couponProducts as $couponProduct) {
            // $couponProduct is an array, not an object
            if (!$this->delete($couponProduct['coupon_product_id'])) {
                $deleted = false;
            }
        }
        
        return $deleted;
    }

    public function productList($id): array
    {
        $result = $this->product
            // ->with([
            //     'prices' => function($query){
            //         return $query->select(['price']);
            //     }
            // ])
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product.product_id', '=',  $id)
            ->select(['product.product_id', 'product.description', 'product.price', 'product_content.name'])
            ->orderBy('product.product_id', 'DESC')
            ->limit(50)
            ->findAll(false);
            
        return $result;
    }
} 