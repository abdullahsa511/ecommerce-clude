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
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;

use function App\Core\System\utils\env;


class Heroproduct extends ComponentBase {
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
            'name' => 'heroproduct',
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
        $product = isset($params['slug']) ? ucwords(str_replace(['_', '-'], ' ', strtolower($params['slug']))) : '';
        $baseUrl = env('APP_URL');
        $component = $this->componentRepository->getComponentByName('heroproduct');
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count']??14;
            $params['fields'] = $item['fields'];
            if(isset($item['related_models']) && is_array($item['related_models'])){
                foreach($item['related_models'] as $joinModel){
                    $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
                }
            }
        }
        $results = $this->productRepository->getProductHeroComponentData($params);
        $productName = isset($results['title']) ? $results['title'] : $results['name'];
        $results['banner_image'] = $results['banner_image']??$results['image']??"[]";
        $results['banner_image'] = json_decode($results['banner_image'], true);
        $results['banner_image'] = isset($results['banner_image'][0]['objectURL']) ? $results['banner_image'][0]['objectURL'] : null;
        $results['banner_videos'] = isset($results['video_url']) && $results['video_url'] ? json_decode($results['video_url'], true) : [];
        
        // if($params['slug'] == 'link'){
        //     // Banner videos - scan video folder for collage (video1, video2, video3, etc.)
        //     $videoDir = (defined('DIR_MEDIA') ? rtrim(DIR_MEDIA, DIRECTORY_SEPARATOR) : '') . DIRECTORY_SEPARATOR . 'Products' . DIRECTORY_SEPARATOR . 'banner' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR;
        //     if (!defined('DIR_MEDIA') || !is_dir($videoDir)) {
        //         $videoDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'Products' . DIRECTORY_SEPARATOR . 'banner' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR;
        //     }
        //     $results['banner_videos'] = [];
        //     if (is_dir($videoDir)) {
        //         $files = glob($videoDir . '*.mp4');
        //         if ($files) {
        //             foreach ($files as $file) {
        //                 $filename = basename($file);
        //                 $results['banner_videos'][] = '/media/Products/banner/video/' . str_replace(' ', '%20', $filename);
        //             }
        //             sort($results['banner_videos']);
        //         }
        //     }
        //     if (empty($results['banner_videos'])) {
        //         $results['banner_videos'] = [
        //             '/media/Products/banner/video/video1.mp4',
        //             '/media/Products/banner/video/video2.mp4',
        //             '/media/Products/banner/video/video3.mp4',
        //         ];
        //     }
        // }
        
        $results['buttons'] = $component->buttons ?? [];
        // $results['way_points'] = isset($results['banner_way_points']) ? json_decode($results['banner_way_points'], true) : [];
        $results['edit_link'] = env('APP_ADMIN_URL')."/ecommerce/products/{$results['product_id']}/general";
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        $parentCategory = isset($results['parent_category']) ? $results['parent_category'] : '';
        $parentCategoryName = isset($results['parent_category_name']) ? $results['parent_category_name'] : '';
        $childCategory = isset($results['child_category']) ? $results['child_category'] : '';
        $childCategoryName = isset($results['child_category_name']) ? $results['child_category_name'] : '';
        $breadcrumbs = [
            [
                'name' => 'Home',
                'link' => '/',
            ],
            [
                'name' => $parentCategoryName,
                'link' => $baseUrl. '/categories/'. $parentCategory,
            ],
            [
                'name' => $childCategoryName,
                'link' => $baseUrl. '/products/'. $childCategory,
            ],
            [
                'name' => $productName,
                'link' => '#',
            ]
        ];

        $results['product_url'] = '/products/'.$params['category'].'/'.$params['slug'];
        $results['breadcrumbs'] = $breadcrumbs;

        return $results;
    }
}
