<?php

/**
 * SA Technology
 *
 * Copyright (C) 2025  Shofiul Alam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace App\Core\Components;

use App\Core\System\Component\ComponentBase;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;
use App\Core\Repositories\Product\ProductAccessoriesRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\Variant\ProductVariantRepositoryInterface;
use function App\Core\System\utils\config;
class Productconfigurator extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ProductRepositoryInterface $productRepository;
    private ProductVariantRepositoryInterface $productVariantRepository;
    private DesignResourceRepositoryInterface $designResourceRepository;
    private ProductAccessoriesRepositoryInterface $productAccessoriesRepository;


    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        DesignResourceRepositoryInterface $designResourceRepository,
        ProductAccessoriesRepositoryInterface $productAccessoriesRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->designResourceRepository = $designResourceRepository;
        $this->productAccessoriesRepository = $productAccessoriesRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'productconfigurator',
            'class' => self::class,
            'validOptions' => [
                'component_id'
            ],
            'filePath' => __FILE__,
            'cacheKey' => null,
            'data' => [],
            'designOnly' => false
        ];
    }

    function results($params = []) {
        $product = $this->productRepository->getByProductCode($params['slug']);
        if(!$product){
            return [];
        }
        $product_id = $product->product_id;
        $results = $this->productVariantRepository->getVariantsByProductId($product_id);
        // echo '<pre>';
        // print_r($results);
        // echo '</pre>';
        $modelData = $this->designResourceRepository->getModelData($product_id);
        $product = (array) $product->data;
        $objectURL = null;
        if (!empty($product['image'])) {
            // If image is JSON string → decode it
            if (is_string($product['image'])) {
                $product['image'] = json_decode($product['image'], true);
            }
        
            $objectURL = $product['image'][0]['objectURL'] ?? null;
        
        }

        $accessories = $this->productAccessoriesRepository->getAccessoriesByProductId($product_id);
        $product['variants'] = $results;
        $product['modelData'] = $modelData;
        $product['accessories'] = $accessories;
        $product['image'] = $objectURL ?? null;

        $config = config('APP_ADMIN_URL');
        return $product;
    }
}
