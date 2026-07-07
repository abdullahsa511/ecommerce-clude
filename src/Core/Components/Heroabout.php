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
use function App\Core\System\utils\env;


class Heroabout extends ComponentBase {
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
            'name' => 'heroabout',
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
        $component = $this->componentRepository->getComponentByName('heroabout');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Extract data from component structure
        $results = [];
        
        // Map section_title to hero_title
        $results['hero_title'] = $component->section_title ?? '';
        
        $results['image'] = isset($component->image) && is_array($component->image) && isset($component->image[0]) && is_array($component->image[0]) ? $component->image[0]['objectURL'] : '';

        $results['buttons'] = [];

        if (isset($component->buttons) && is_array($component->buttons) && count($component->buttons) > 0) {
            foreach ($component->buttons as $key => $button) {
                $results['buttons'][$key] = [
                    'title'  => $button['title'] ?? null,
                    'icon'   => $button['icon'] ?? null,
                    'link'   => $button['url'] ?? null,
                    'target' => $button['target'] ?? null,
                    'anchor_class'  => $key == 0 ? 'th-btn text-capitalize' : 'th-btn-outline text-capitalize',
                    'div_class'  => $key == 0 ? 'position-relative pb-3' : 'position-relative',
                ];
            }
        }


        // Map section_subtitle to hero_subtitle
        $results['hero_subtitle'] = $component->section_subtitle ?? '';
        if (isset($component->banner_way_points)) {
            if (is_string($component->banner_way_points)) {
                $results['way_points'] = json_decode($component->banner_way_points, true);
            } elseif (is_array($component->banner_way_points)) {
                $results['way_points'] = $component->banner_way_points;
            } else {
                $results['way_points'] = [];
            }
        } else {
            $results['way_points'] = [];
        }
        // Extract data from items array (4 elements with name and value fields)
        if (isset($component->items) && is_array($component->items) && count($component->items) > 0) {
            $items = $component->items[0]; // Get the first array of items
            
            foreach ($items as $item) {
                if (isset($item['name']) && isset($item['value'])) {
                    $fieldName = $item['name'];
                    $fieldValue = $item['value'];
                    
                    // Map specific field names to the expected format
                    switch ($fieldName) {
                        case 'hero_button_label_white':
                            $results['hero_button_label_white'] = $fieldValue;
                            break;
                        case 'hero_button_label_outline':
                            $results['hero_button_label_outline'] = $fieldValue;
                            break;
                        case 'hero_image':
                            // Extract objectURL from the hero_image array structure
                            if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                $imageData = $fieldValue[0];
                                if (isset($imageData['objectURL'])) {
                                    $results['hero_image'] = $imageData['objectURL'];
                                } else {
                                    $results['hero_image'] = '';
                                }
                            } else {
                                $results['hero_image'] = $fieldValue;
                            }

                            // $results['way_points'] = isset($component->banner_way_points) ? json_decode($component->banner_way_points, true) : [];
                            break;
                        case 'load_btn':
                            $results['loadBtn'] = $fieldValue;
                            break;
                        default:
                            // For any other fields, use the name as the key
                            $results[$fieldName] = $fieldValue;
                            break;
                    }
                }
            }
        }
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        $results['edit_link'] = env('APP_ADMIN_URL')."/content/about/edit/{$component->component_id}/general";

        $results['showroom_button_link'] = env('APP_URL')."/contact-us";
        $results['contact_sales_button_link'] = env('APP_URL')."/contact-sales";

        return $results;
    }
}
