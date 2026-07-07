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

class Designprocess extends ComponentBase {
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
            'name' => 'designprocess',
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
        // $results['section_title'] = 'Our Design Process';
        // $results['section_subtitle'] = "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.";
        // $results['items'] = [
        //     [
        //         'image' => '/img/contact-us/design-process-1.jpg',
        //         'step_number' => '01',
        //         'heading' => 'Planning',
        //         'details' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
        //     ],
        //     [
        //         'image' => '/img/contact-us/design-process-2.jpg',
        //         'step_number' => '02',
        //         'heading' => 'Proposal',
        //         'details' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
        //     ],
        //     [
        //         'image' => '/img/contact-us/design-process-3.jpg',
        //         'step_number' => '03',
        //         'heading' => 'Installation',
        //         'details' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
        //     ],
        // ];

        // return $results;

        $component = $this->componentRepository->getComponentByName('designprocess');
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
                                case 'step_number':
                                    $itemData['step_number'] = $fieldValue;
                                    break;
                                case 'heading':
                                    $itemData['heading'] = $fieldValue;
                                    break;
                                case 'details':
                                    $itemData['details'] = $fieldValue;
                                    break;
                                case 'image':
                                    // Extract objectURL from the image array structure
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
                if (isset($itemData['step_number']) && isset($itemData['heading']) && isset($itemData['details']) && isset($itemData['image'])) {
                    $results['items'][] = $itemData;
                }
            }
        }
        
        return $results;
        
    }
}
