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

use function App\Core\System\utils\config;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;

class Designresources extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    protected $componentRepository;
    protected $designResourceRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository, 
        DesignResourceRepositoryInterface $designResourceRepository,
        array $options = []
    )
    {
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
            'name' => 'designresources',
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
        // $results['section_title'] = 'Design Resources';
        // $results['section_subtitle'] = "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.";
        // $results['items'] = [
        //     [
        //         'img' => '/img/bg/home/home_dr-fabrics.jpg',
        //         'title' => 'Model Library',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
        //         'link' => '/account/resources/models'

        //     ],
        //     [
        //         'img' => '/img/bg/home/home_dr-models.jpg',
        //         'title' => 'Image Gallery',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.'
        //     ],
        //     [
        //         'img' => '/img/bg/home/home_dr-finish.jpg',
        //         'title' => 'Fabrics',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.'
        //     ],
        //     [
        //         'img' => '/img/bg/home/home_dr-models.jpg',
        //         'title' => 'Image Gallery',
        //         'description' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.'
        //     ]
        // ];
        // return $results;
        $component = $this->componentRepository->getComponentByName('designresources');
        
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Map section_title and section_subtitle
        $results['section_title'] = $component->section_title ?? '';
        $results['section_subtitle'] = $component->section_subtitle ?? '';
        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";

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
                                case 'link':
                                    $itemData['link'] = $fieldValue;
                                    break;
                                case 'img':
                                    if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                        $imageData = $fieldValue[0];
                                        if (isset($imageData['objectURL'])) {
                                            $itemData['img'] = $imageData['objectURL'];
                                        } else {
                                            $itemData['img'] = '';
                                        }
                                    } else {
                                        $itemData['img'] = $fieldValue;
                                    }
                                    break;
                            }
                        }
                    }
                }
                
                // Only add item if it has both title and image
                if (isset($itemData['title']) && isset($itemData['description']) && isset($itemData['img'])) {
                    $results['items'][] = $itemData;
                }
            }
        }
        
        return $results;
    }
}
