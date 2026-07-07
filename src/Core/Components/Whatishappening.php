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
use App\Core\Repositories\Component\ComponentRepositoryInterface;

use function App\Core\System\utils\convertDateToMonthYear;
use function App\Core\System\utils\env;

class Whatishappening extends ComponentBase {
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
            'name' => 'whatishappening',
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
        $component = $this->componentRepository->getComponentByName('whatishappening');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        // Extract data from component structure
        $results = [];
        
        // Map section_title and section_subtitle
        $results['section_title'] = $component->section_title ?? '';
        $results['below_title'] = $component->section_subtitle ?? '';
        $results['below_description'] = $component->description ?? '';
        $results['title_link'] = $component->section_link ?? '';

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
                                case 'created':
                                    $itemData['created'] = convertDateToMonthYear($fieldValue);
                                    break;
                                default:
                                    $itemData[$fieldName] = $fieldValue;
                                    break;
                            }
                        }
                    }
                }
                
                // Only add item if it has both title and image
                if (isset($itemData['title']) && isset($itemData['image'])) {
                    $results['items'][] = $itemData;
                }
            }
        }
        $results['component_link'] = env('APP_ADMIN_URL') . "/components/{$component->component_id}/items";
        
        return $results;
    }
}
