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

use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;

use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;

class Productlist extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    protected $componentRepository;
    protected $productRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository, 
        ProductRepositoryInterface $productRepository,
        array $options = []
    )
    {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->productRepository = $productRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }

    public static function getComponentMeta()
    {
        return [
            'name' => 'productlist',
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
        $component = $this->componentRepository->getComponentByName('productlist');
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
            $params['limit'] = 0;
        }
        // get section subtitle from component
        $category = isset($params['subcategory']) ? $params['subcategory'] : '';
        $results = $this->productRepository->getProductsByCategorySlug($category, $params);
        $results['section_subtitle'] = isset($params['section_subtitle']) ? $params['section_subtitle'] : 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.';
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        return $results;
    }
} 