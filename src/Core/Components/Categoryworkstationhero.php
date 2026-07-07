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
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use App\Core\System\Event;


class Categoryworkstationhero extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private TaxonomyItemRepositoryInterface $taxonomyRepository;


    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        TaxonomyItemRepositoryInterface $taxonomyRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'categoryworkstationhero',
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
        // $results = [
        //     'title' => 'WORKSTATION D',
        //     'subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque. D',
        //     'image' => '/img/category-workstation/hero.png',
        //     'categories' => [
        //         ['name' => 'Task Seating', 'url' => "/"],
        //         ['name' => 'Execute Seating', 'url' => "/"],
        //         ['name' => 'Training Seating', 'url' => "/"],
        //         ['name' => 'Occasional Seating', 'url' => "/"],
        //         ['name' => 'Stools', 'url' => "/"],
        //         ['name' => 'Lounges', 'url' => "/"],
        //     ]
        // ];
        // return $results;

        $component = $this->componentRepository->getComponentByName('categoryworkstationhero');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        // Extract data from component structure
        $results = [];
        
        // Map section_title and section_subtitle
        $results['title'] = $component->section_title ?? '';
        $results['subtitle'] = $component->section_subtitle ?? '';

        // Extract objectURL from the image array structure for the main image
        if (isset($component->image) && is_array($component->image) && isset($component->image[0]) && is_array($component->image[0])) {
            $imageData = $component->image[0];
            if (isset($imageData['objectURL'])) {
                $results['image'] = $imageData['objectURL'];
            } else {
                $results['image'] = '';
            }
        } else {
            $results['image'] = $component->image ?? '';
        }
        
        // Extract items array with title and image for each item
        $results['categories'] = [];
        
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                
                if (is_array($item)) {
                    foreach ($item as $field) {
                        if (isset($field['name']) && isset($field['value'])) {
                            $fieldName = $field['name'];
                            $fieldValue = $field['value'];
                            
                            switch ($fieldName) {
                                case 'name':
                                    $itemData['name'] = $fieldValue;
                                    break;
                                case 'url':
                                    $itemData['url'] = $fieldValue;
                                    break;
                                
                            }
                        }
                    }
                }
                
                // Only add item if it has both name and url
                if (isset($itemData['name']) && isset($itemData['url'])) {
                    $results['categories'][] = $itemData;
                }
            }
        }
        
        return $results;
    }
}
