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
use App\Core\Repositories\Product\CategoryRepositoryInterface;
use function App\Core\System\utils\env;


class Categoryhero extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private CategoryRepositoryInterface $categoryRepository;


    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        CategoryRepositoryInterface $categoryRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->categoryRepository = $categoryRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'categoryhero',
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
        $results = [];
        if(isset($params['category']) && !isset($params['slug'])){
            $params['slug'] = $params['category'];
        }

        $component = $this->componentRepository->getComponentByName('categoryhero');

        if(isset($component->items[0])){
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
            $params['item_count'] = $item['item_count'];
            foreach($item['related_models'] as $joinModel){
                $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
            }
        }

        $results = $this->categoryRepository->getCategoryHeroComponentData($params);
       
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        $results['edit_link'] = env('APP_ADMIN_URL')."/ecommerce/categories/list";
        $breadcrumbs = [
            [
                'name' => 'Home',
                'link' => '/',
            ],
            [
                'name' => isset($results['title']) ? ucwords(strtolower($results['title'])) : '',
                'link' => isset($params['subcategory'])? $results['link'] ?? '':''
            ]
        ];
        if(isset($params['subcategory']) && !empty($params['subcategory'])){
           $subcategory = array_find($results['categories']??[], function($category) use ($params) {
               return $category['slug'] == $params['subcategory'];
           });
           if($subcategory){
            $breadcrumbs[] = [
                'name' => $subcategory['name']?? '',
                'link' => $subcategory['link'] ?? ''
            ];
           }
        }

        $results['breadcrumbs'] = $breadcrumbs;
        // echo '<pre>';
        // print_r($results['way_points']);
        // echo '</pre>';

        return $results;       
    }
}
