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
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use App\Core\Utilities\Debug;

class Showroominteractivetoursection extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ShowroomRepositoryInterface $showroomRepository;
    private ComponentRepositoryInterface $componentRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ShowroomRepositoryInterface $showroomRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->showroomRepository = $showroomRepository;
    }
    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'showroominteractivetoursection',
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

    // function results($params = [])
    // {
    //     // Debug::dd($params['slug'], true);
    //     $showroom = $this->showroomRepository->findBySlug($params['slug']);
    //     $results = [
    //         // 🧭 Section head Data
    //         'head' => [
    //             'title' => $showroom->title ?? '',
    //             'map_image' => $showroom->image ?? '/img/showroom/map.png', //  '/img/showroom/map.png',
    //             'map_alt'   => $showroom->title ?? '', // 'Interactive map of the Sydney showroom'
    //         ]
    //     ];
    //     $results['items'] = $this->showroomRepository->getShowroomData();
    //     // Debug::dd($results, true);
    //     return $results;
    // }

    function results($params = []) {
        $component = $this->componentRepository->getComponentByName('showroominteractivetoursection');
        $results = [];
        if($component && isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }
        $results = $this->showroomRepository->getShowroomComponentData($params);
    
        
        
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        return $results;
    }
}
