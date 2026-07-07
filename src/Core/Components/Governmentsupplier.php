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

class Governmentsupplier extends ComponentBase {
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
            'name' => 'governmentsupplier',
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
        // $results['image_one'] = '/img/about/supplier-img1.png';
        // $results['title_one'] = 'Government supplier';
        // $results['description_one'] = 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.';
        // $results['link_text_one'] = 'Location';
        // $results['image_two'] = '/img/about/supplier-img2.png';
        // $results['title_two'] = 'Environmental policy';
        // $results['description_two'] = 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.';
        // $results['link_text_two'] = 'Read more';

        // return $results;


        $component = $this->componentRepository->getComponentByName('governmentsupplier');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Extract data from component structure
        $results = [];
        
        
        // Extract data from items array (4 elements with name and value fields)
        if (isset($component->items) && is_array($component->items) && count($component->items) > 0) {
            $items = $component->items[0]; // Get the first array of items
            
            foreach ($items as $item) {
                if (isset($item['name']) && isset($item['value'])) {
                    $fieldName = $item['name'];
                    $fieldValue = $item['value'];
                    
                    // Map specific field names to the expected format
                    switch ($fieldName) {
                        case 'title_one':
                            $results['title_one'] = $fieldValue;
                            break;
                        case 'title_two':
                            $results['title_two'] = $fieldValue;
                            break;
                        case 'description_one':
                            $results['description_one'] = $fieldValue;
                            break;
                        case 'description_two':
                            $results['description_two'] = $fieldValue;
                            break;
                        case 'link_text_one':
                            $results['link_text_one'] = $fieldValue;
                            break;
                        case 'link_text_two':
                            $results['link_text_two'] = $fieldValue;
                            break;
                        case 'image_one':
                            // Extract objectURL from the hero_image array structure
                            if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                $imageData = $fieldValue[0];
                                if (isset($imageData['objectURL'])) {
                                    $results['image_one'] = $imageData['objectURL'];
                                } else {
                                    $results['image_one'] = '';
                                }
                            } else {
                                $results['image_one'] = $fieldValue;
                            }
                            break;
                        case 'image_two':
                            // Extract objectURL from the hero_image array structure
                            if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                $imageData = $fieldValue[0];
                                if (isset($imageData['objectURL'])) {
                                    $results['image_two'] = $imageData['objectURL'];
                                } else {
                                    $results['image_two'] = '';
                                }
                            } else {
                                $results['image_two'] = $fieldValue;
                            }
                            break;
                        
                        default:
                            // For any other fields, use the name as the key
                            $results[$fieldName] = $fieldValue;
                            break;
                    }
                }
            }
        }
        
        return $results;
    }
}