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

use function App\Core\System\utils\config;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;
use App\Core\Repositories\Component\ComponentRepositoryInterface;

class Ourhistorymasonry extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'transform: translateY(0 px)',
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
        $results = [];
        // $results['items'] = [
        //     [
        //         'heading' => 'World-class product display',
        //         'img' => '/img/about/gallery-image1.png',
        //         'des' => 'A full collection of workstations from leg-based systems to panel constructions and height-adjustable offerings. Find the perfect configuration and aesthetic for your space.',
        //         'link_text' => 'View all World-class product display',
        //         'class' => 'grid-col-span-7',
        //         'style' => 'transform: translateY(0 px);padding-top:0 px'
        //     ],
        //     [
        //         'heading' => 'unparalleled service',
        //         'img' => '/img/about/gallery-image2.png',
        //         'des' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis',
        //         'link_text' => 'View all unparalleled service',
        //         'class' => 'grid-col-span-6',
        //         'style' => 'transform: translateY(49 px);padding-top:0 px'
        //     ],
        //     [
        //         'heading' => 'Product certifications',
        //         'img' => '/img/about/gallery-image3.png',
        //         'des' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
        //         'link_text' => 'View all Product certifications',
        //         'class' => 'grid-col-span-6',
        //         'style' => 'transform: translateY(95 px);padding-top:0 px'
        //     ],
        //     [
        //         'heading' => 'Product warranty',
        //         'img' => '/img/about/gallery-image4.png',
        //         'des' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
        //         'link_text' => 'View all Product warranty',
        //         'class' => 'grid-col-span-7',
        //         'style' => 'transform: translateY(130 px);padding-top:0 px'
        //     ]
        // ];

        // return $results;
        
        $component = $this->componentRepository->getComponentByName('ourhistorymasonry');
        $items = $this->componentRepository->getComponentItems($component);  
        $results['items'] = [];
        foreach($items as $key => $item){
            switch($key){
                case 0:
                    $item['class'] = 'grid-col-span-7';
                    $item['style'] = 'transform: translateY(0px);padding-top:0 px';
                    break;
                case 1:
                    $item['class'] = 'grid-col-span-6';
                    $item['style'] = 'transform: translateY(49 px);padding-top:0 px';
                    break;
                case 2:
                    $item['class'] = 'grid-col-span-6';
                    $item['style'] = 'transform: translateY(95 px);padding-top:0 px';
                    break;
                case 3:
                    $item['class'] = 'grid-col-span-7';
                    $item['style'] = 'transform: translateY(130 px);padding-top:0 px';
                    break;
            }
            $results['items'][] = $item;
        }

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['section_title'] = $component->section_title;
        $results['section_subtitle'] = $component->section_subtitle;
        $results['description'] = $component->description;
        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";
        return $results;
        
    }
}