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
use App\Core\Repositories\Project\ProjectRepositoryInterface;

use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use function App\Core\System\utils\config;
class Productfeaturedprojectslider extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds


    protected $componentRepository;
    protected $projectRepository;
    protected $productRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository, 
        ProjectRepositoryInterface $projectRepository,
        ProductRepositoryInterface $productRepository,
        array $options = []
    )
    {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->projectRepository = $projectRepository;
        $this->productRepository = $productRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'productfeaturedprojectslider',
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
        $component = $this->componentRepository->getComponentByName('productfeaturedprojectslider');
        if(!$component || !isset($component->component_id)){
            return [];
        }
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
            $params['is_recent'] = $item['is_recent'];
            $params['is_featured'] = $item['is_featured'];
        }
        $items = $this->productRepository->getProductFeaturedProjectsSliderComponentData($params);
        // $component->items = array_map(function($item) {
        //     $imageObject = json_decode($item['image'], true);
        //     if(isset($imageObject[0]['objectURL'])) {
        //         $item['image'] = $imageObject[0]['objectURL'];
        //     }
        //     return $item;
        // }, $items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        //subtitle add a product name
        
        $componentArray = $component->toArray();
        $config = config('APP_ADMIN_URL');
        $componentArray['component_link'] = $config."/components/{$component->component_id}/items";
        $componentArray['items'] = $items;
        return $componentArray;
    }
}
