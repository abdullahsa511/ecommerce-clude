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

use function App\Core\System\utils\env;

class Featuredproductslider extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds


    // protected $componentRepository;
    // protected $projectRepository;
    private ComponentRepositoryInterface $componentRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ProductRepositoryInterface $productRepository;

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
            'name' => 'featuredproductslider',
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
        // echo "<pre>";
        // print_r($params);
        // echo "</pre>";
        // exit;
        $results = [];
        $component = $this->componentRepository->getComponentByName('featuredproductslider');
        // echo "<pre>";
        // print_r($component);
        // echo "</pre>";
        // exit;
        
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['is_featured'] = $item['is_featured'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }
        
        $results = $this->productRepository->getFeaturedProductSliderComponentData($params);
        $results['section_title'] = $component->section_title ?? 'Featured Products';
        $results['section_subtitle'] = $component->section_subtitle ?? "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.";
        $results['link_text'] = 'View All Products'; 
        $results['link_url'] = env('APP_URL').'/categories';

        // echo '<pre>';
        // print_r($results);
        // echo '</pre>';
        // exit;
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        return $results;
    }
}
