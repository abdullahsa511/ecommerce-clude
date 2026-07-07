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
use App\Core\System\Event;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
class Productslider extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProductRepositoryInterface $productRepository,
        array $options = []
    ) {
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
            'name' => 'productslider',
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
        // $results['items'] = [
        //     [
        //         'id' => 1,
        //         'name' => 'Archi',
        //         'image' => '/img/category-seating/Archi.png',
        //         'tags' => [
        //             ['name' => 'AFRDI Certified'],
        //             ['name' => 'OBP Certified']
        //         ],
        //         'finishes' => [
        //             ['name' => 'Black Fabric', 'color' => 'black-fabric'],
        //             ['name' => 'Black Premium Polyurethane', 'color' => 'black-premium'],
        //             ['name' => 'Mocha Premium Polyurethane', 'color' => 'mocha-premium'],
        //             ['name' => 'Cream Premium Polyurethane', 'color' => 'white', 'img' => '/img/finishes/finish-2.jpg']
        //         ],
        //         'category' => 'Task Seating'
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'Miro',
        //         'image' => '/img/category-seating/Miro.png',
        //         'tags' => [
        //             ['name' => 'AFRDI Certified'],
        //             ['name' => 'OBP Certified'],
        //             ['name' => 'Some Tag Name Here'],
        //             ['name' => 'Tag Name Here'],
        //             ['name' => 'Tag Name Here As Well']
        //         ],
        //         'category' => 'Task Seating'
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => 'Miro S',
        //         'image' => '/img/category-seating/Miro S.png',
        //         'tags' => [
        //             ['name' => 'AFRDI Certified'],
        //             ['name' => 'OBP Certified'],
        //             ['name' => 'Some Tag Name Here'],
        //             ['name' => 'Tag Name Here'],
        //             ['name' => 'Tag Name Here As Well']
        //         ],
        //         'category' => 'Task Seating'
        //     ],
        //     [
        //         'id' => 4,
        //         'name' => 'Kove',
        //         'image' => '/img/category-seating/Kove.png',
        //         'tags' => [
        //             ['name' => 'AFRDI Certified'],
        //             ['name' => 'OBP Certified'],
        //             ['name' => 'Some Tag Name Here'],
        //             ['name' => 'Tag Name Here'],
        //             ['name' => 'Tag Name Here As Well']
        //         ],
        //         'category' => 'Task Seating'
        //     ]
        // ];
        

        $component = $this->componentRepository->getComponentByName('productslider');
        $params = [];
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['is_featured'] = $item['is_featured'];
            $params['fields'] = $item['fields'];
        }
        $component->items = $this->productRepository->getSliderProducts($params);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        return $component->toArray();
    }
}
