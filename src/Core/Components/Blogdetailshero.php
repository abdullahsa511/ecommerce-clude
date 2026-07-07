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
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;

use function App\Core\System\utils\env;

class Blogdetailshero extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];
     private PostRepositoryInterface $postRepository;
    private ComponentRepositoryInterface $componentRepository;

    public $cacheExpire = 0; //seconds

   
    public function __construct(
       PostRepositoryInterface $postRepository,
       ComponentRepositoryInterface $componentRepository,
        array $options = []
    ) {
        parent::__construct($options);
         $this->postRepository = $postRepository;
        $this->componentRepository = $componentRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'blogdetailshero',
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

        // Debug::printDebug($params, true);
        // return $params;
         $results = [];
        //  $results['heroitem'] = [
        //     [
        //         'id' => 1,
        //         'date' => 'October 06, 2025',
        //         'heading' => 'Embracing human-centric design: A path to enhanced usability and experience',
        //         'heroimg' => '/img/project-detail/hero.png',
        //     ]
        //  ];

         $component = $this->componentRepository->getComponentByName('blogdetailshero');
        if(isset($component->items[0])){
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
            $params['item_count'] = $item['item_count'];
            foreach($item['related_models'] as $joinModel){
                $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
            }
        }

        $results = $this->postRepository->getBlogDetailHeroComponentData($params);

        // var_dump($results); exit;

        $results['image_banner'] = json_decode($results['image_banner'], true) ?? [];
        if(is_string($results['image_banner'])) $results['image_banner'] = json_decode($results['image_banner'], true) ?? [];
        $results['image_banner'] = $results['image_banner'][0]['image'] ?? '/img/project-detail/hero.png';
        $results['link'] = env('APP_ADMIN_URL')."/posts/".$results['slug'];
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        $results['edit_link'] = isset($results['post_id']) ? env('APP_ADMIN_URL')."/posts/edit/{$results['post_id']}/general" : '';
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['created_at'] = isset($results['created_at']) ? date('F d, Y', strtotime($results['created_at'])) : '';
        $results['options'] = $this->options;
        $results['way_points'] = json_decode(($results['banner_way_points']??'[]'), true) ?? [];

        $breadcrumbs = [
            [
                'name' => 'Home',
                'link' => '/',
            ],
            [
                'name' => 'Blog',
                'link' => '/blog',
            ],
            [ 
                'name' => ucwords(str_replace('-', ' ', strtolower($results['slug']))),
            ],
        ];
        $results['breadcrumbs'] = $breadcrumbs;

        return $results;

        
    }
}
