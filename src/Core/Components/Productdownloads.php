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

use App\Core\Repositories\Component\ComponentRepository;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use App\Core\Utilities\Debug;

class Productdownloads extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ProductRepositoryInterface $productRepository;
    private ComponentRepositoryInterface $componentRepository;
    private DesignResourceRepositoryInterface $designResourceRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProductRepositoryInterface $productRepository,
        DesignResourceRepositoryInterface $designResourceRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->productRepository = $productRepository;
        $this->designResourceRepository = $designResourceRepository;
    }
    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'productdownloads',
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
        $component = $this->componentRepository->getComponentByName('productdownloads');
        // $results = [];
        if($component && isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];

            if(isset($item['related_models']) && is_array($item['related_models'])){
                foreach($item['related_models'] as $joinModel){
                    $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
                }
            }
        }
        $params['product_id'] = $product->product_id;
        $params['resource_types'] = ['models', 'documents'];
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        // $results = $this->designResourceRepository->getProductsByResourceType($params);
        $results = $this->productRepository->getProductDownloadsTabData($params);
        // $results['title'] = (!empty($component->items) && isset($component->items[0]['title'])) ? $component->items[0]['title'] : '';
        return $results;
    }
    
}




?>
