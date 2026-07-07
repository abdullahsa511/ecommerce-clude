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
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;

class Featuredmaterialslider extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private DesignResourceRepositoryInterface $designResourceRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        DesignResourceRepositoryInterface $designResourceRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->designResourceRepository = $designResourceRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'featuredmaterialslider',
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

    function results() {
        // $results = [];
        // $results['section_title'] = "Featured Materials";
        // $results['section_subtitle'] = "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque. d";
        // $results['section_link_text'] = "View All Products";
        // $results['items'] = [
        //     [
        //         'image' => '/img/project-detail/material 1.png',
        //         'category' => 'Finish',
        //         'name' => 'ABBEY',
        //         'description' => 'Lorem Ipsum d'
        //     ],
        //     [
        //         'image' => '/img/project-detail/material 2.png',
        //         'category' => 'Textile',
        //         'name' => 'Access Mesh',
        //         'description' => 'Lorem Ipsum'
        //     ],
        //     [
        //         'image' => '/img/project-detail/material 3.png',
        //         'category' => 'Textile',
        //         'name' => 'Afghan Seating',
        //         'description' => 'Lorem Ipsum'
        //     ],
        //     [
        //         'image' => '/img/project-detail/material 4.png',
        //         'category' => 'Finish',
        //         'name' => 'Amethyst',
        //         'description' => 'Lorem Ipsum'
        //     ],
        //     [
        //         'image' => '/img/project-detail/material 5.png',
        //         'category' => 'Finish',
        //         'name' => 'Amethyst',
        //         'description' => 'Lorem Ipsum'
        //     ]
        // ];

        // return $results;

        $component = $this->componentRepository->getComponentByName('featuredmaterialslider');
        if(!$component || !isset($component->component_id)){
            return [];
        }
        $params = [];
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
            $params['is_recent'] = $item['is_recent'];
            $params['is_featured'] = $item['is_featured'];
            // foreach($item['related_models'] as $joinModel){
            //     $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
            // }
        }

        $results = $this->designResourceRepository->getFeaturedMaterialSlider($params);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['section_title'] = $component->section_title ?? 'Featured Materials';
        $results['section_subtitle'] = $component->section_subtitle ?? 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.';
        // $results['section_link_text'] = $component->section_link_text ?? 'View All Materials';
        

        return $results;
    }
}
