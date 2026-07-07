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
 * @Company: SA Technology
 * @Date: 04-10-2025
 * @Develop by: Mohammad Ali Abdullah
 */

namespace App\Core\Components;

use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\Variant\ProductVariantRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;

class Productdimenssions extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ProductRepositoryInterface $productRepository;
    private ProductVariantRepositoryInterface $productVariantRepository;
    private ComponentRepositoryInterface $componentRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }
    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'productdimenssions',
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
        $product = $this->productRepository->getProductDimension($params['slug']);
        if(!$product){
            $product = [];
        }
        // $product = $this->productRepository->getProductByCodeWithDefaultConfiguration($params['slug']);

        // echo '<pre>';
        // print_r($product);
        // echo '</pre>';

        $item = $this->productVariantRepository->getDefaultItemByProductId($product['product_id']);
        $productWidth = isset($product['width']) && !empty($product['width']) ? $product['width'] : null;
        $productHeight = isset($product['height']) && !empty($product['height']) ? $product['height'] : null;
        $productDepth = isset($product['depth']) && !empty($product['depth']) ? $product['depth'] : null;
        if(is_numeric($productWidth)){
            $productWidth = intval($productWidth * 1);
        }
        if(is_numeric($productHeight)){
            $productHeight = intval($productHeight * 1);
        }
        if(is_numeric($productDepth)){
            $productDepth = intval($productDepth * 1);
        }
        // echo '<pre>';
        // print_r($item);
        // echo '</pre>';
        $width = (is_numeric($productWidth) && $productWidth > 0) || !empty($productWidth) ? $productWidth : $item['display_width'];
        $height = (is_numeric($productHeight) && $productHeight > 0) || !empty($productHeight) ? $productHeight : $item['display_height'];
        $depth = (is_numeric($productDepth) && $productDepth > 0) || !empty($productDepth) ? $productDepth : $item['display_depth'];

      
        $component = $this->componentRepository->getComponentByName('productdimenssions');
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        $dimensions['display_width'] = $width;
        $dimensions['display_height'] = $height;
        $dimensions['display_depth'] = $depth;
        $image = null;
        if(isset($product['dimension_image'])){
            $image = json_decode($product['dimension_image'], true);
        }
        if(isset($image) && isset($image[0]) && isset($image[0]['objectURL'])){
            $dimensions['dimensions_image'] = $image[0]['objectURL'];
        }else{
            $dimensions['dimensions_image'] = $item['dimensions_image']??'';
        }
        return $dimensions;
    }
    
}




?>
