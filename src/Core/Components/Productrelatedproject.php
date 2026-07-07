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
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;

class Productrelatedproject extends ComponentBase {
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
            'name' => 'productrelatedproject',
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
        // echo '<pre>';
        // print_r($params);
        // echo '</pre>';
        // exit;
        $slug = isset($params['slug']) ? $params['slug'] : '';
        $component = $this->componentRepository->getComponentByName('productrelatedproject');
        $results['section_title'] = $component->section_title;
        $results['section_subtitle'] = $component->section_subtitle;
        $params = [];
        if(isset($component->items[0])) {
            $params['model'] = $component->items[0]['model'];
            $params['item_count'] = $component->items[0]['item_count'];
            $params['fields'] = $component->items[0]['fields'];
            foreach($params['fields'] as $key => $field) {
                if(in_array($field, ['product.class', 'product.style'])) {
                    unset($params['fields'][$key]);
                }
            }
        }
        // $params['slug'] = $params['slug'];
        $items = $this->productRepository->getProductRelatedProjectComponentData($slug);
        // echo '<pre>';
        // print_r($items);
        // echo '</pre>';

        $results['items'] = $items;

        list($results) = Event::trigger(__CLASS__,__FUNCTION__, $results);
        return array_merge($results, $this->options);
    }
}
