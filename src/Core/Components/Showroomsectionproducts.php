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
use App\Core\Utilities\Debug;

class Showroomsectionproducts extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    public function __construct(
        array $options = []
    ) {
        parent::__construct($options);
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'showroomsectionproducts',
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
        $results['products'] = [
            [
                'id' => 1,
                'name' => 'Archi',
                'image' => '/img/showroom/details/image1.png',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list1.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified']
                ],
            ],
            [
                'id' => 2,
                'name' => 'Miro',
                'image' => '/img/showroom/details/image2.png',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list2.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
            [
                'id' => 3,
                'name' => 'Miro S',
                'image' => '/img/showroom/details/image3.png',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list3.png',
                'tags' => [
                    ['name' => 'AFRDI Certified 2'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
            [
                'id' => 4,
                'name' => 'Kove',
                'image' => '/img/showroom/details/image4.png',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list4.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
            [
                'id' => 5,
                'name' => 'Jecton',
                'image' => '/img/showroom/details/image5.png',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list5.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
            [
                'id' => 6,
                'name' => 'Handson',
                'image' => '/img/showroom/details/image6.png',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list1.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
            [
                'id' => 7,
                'name' => 'Modern',
                'image' => '/img/showroom/details/image7.jpg',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list2.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
            [
                'id' => 8,
                'name' => 'Silvarst',
                'image' => '/img/showroom/details/image8.jpg',
                'description' => 'Miro is a versatile seating solution that adapts to any workspace.',
                'fabric_image' => '/img/showroom/details/list3.png',
                'tags' => [
                    ['name' => 'AFRDI Certified'],
                    ['name' => 'OBP Certified'],
                    ['name' => 'Some Tag Name Here'],
                    ['name' => 'Tag Name Here'],
                    ['name' => 'Tag Name Here As Well']
                ],
            ],
        ];

        return $results;

        
        // $component = $this->componentRepository->getComponentByName('blogmain');
        // if(isset($component->items[0])){
        //     $item = $component->items[0];
        //     $params['model'] = $item['model'];
        //     $params['fields'] = $item['fields'];
        //     $params['item_count'] = $item['item_count'];
        //     foreach($item['related_models'] as $joinModel){
        //         $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
        //     }
        // }

        // $componentItems = [];

        // $componentItems['section_title'] = $component->section_title ?? $component->title?? '';
        // $componentItems['section_subtitle'] = $component->section_subtitle ?? $component->subtitle?? '';
        // $results = $this->postRepository->getBlogMainComponentData($params);
        
        // $results['section_title'] = $componentItems['section_title'];
        // $results['section_subtitle'] = $componentItems['section_subtitle'];
        // $results['section_subtitle2'] = $results['section_subtitle2'];
        // $results['section_subtitle3'] = $results['section_subtitle3'];
        // $results['section_subtitle4'] = $results['section_subtitle4'];
        // $results['img'] = $results['img'];

        // list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        // $results['options'] = $this->options;
       

        // return $results;
    }
}
