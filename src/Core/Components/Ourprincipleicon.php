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

class Ourprincipleicon extends ComponentBase {
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
            'name' => 'ourprincipleicon',
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
        // $results = [];
        // $results['sectionTitle'] = "Our Principles";
        // $results['sectionSubtitle'] = "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas";
        // $results['items'] = [
        //     [
        //         'imageCircle' => [
        //             'img' => '/img/solution/principle-icon-1.png',
        //         ],
        //         'title' => 'OFFER THE BEST SERVICE',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.'
        //     ],
        //     [
        //         'imageCircle' => [
        //             'img' => '/img/solution/principle-icon-2.png',
        //         ],
        //         'title' => 'PROVIDE THE HIGHEST QUALITY PRODUCTS',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.'
        //     ],
        //     [
        //         'imageCircle' => [
        //             'img' => '/img/solution/principle-icon-3.png',
        //         ],
        //         'title' => 'SELL AT A FAIR PRICE',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.'
        //     ]
        // ];

        $component = $this->componentRepository->getComponentByName('ourprincipleicon');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        // Extract data from component structure
        $results = [];
        
        // Map section_title and section_subtitle
        $results['section_title'] = $component->section_title ?? '';
        $results['section_subtitle'] = $component->section_subtitle ?? '';
        
        // Extract items array with title and image for each item
        $results['items'] = [];
        
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                
                if (is_array($item)) {
                    foreach ($item as $field) {
                        if (isset($field['name']) && isset($field['value'])) {
                            $fieldName = $field['name'];
                            $fieldValue = $field['value'];
                            
                            switch ($fieldName) {
                                case 'title':
                                    $itemData['title'] = $fieldValue;
                                    break;
                                case 'description':
                                    $itemData['description'] = $fieldValue;
                                    break;
                                case 'image':
                                    if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                        $imageData = $fieldValue[0];
                                        if (isset($imageData['objectURL'])) {
                                            $itemData['image'] = $imageData['objectURL'];
                                        } else {
                                            $itemData['image'] = '';
                                        }
                                    } else {
                                        $itemData['image'] = $fieldValue;
                                    }
                                    break;
                            }
                        }
                    }
                }
                
                // Only add item if it has both title and image
                if (isset($itemData['title']) && isset($itemData['description']) && isset($itemData['image'])) {
                    $results['items'][] = $itemData;
                }
            }
        }
        
        return $results;
    }
}
