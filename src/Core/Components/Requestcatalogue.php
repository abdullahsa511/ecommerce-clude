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

class Requestcatalogue extends ComponentBase {
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
            'name' => 'requestcatalogue',
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
        // $results['section_title'] = 'Request Catalogue';
        // $results['section_subtitle'] = "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet<br> egestas et. Pellentesque libero donec sit egestas orci.";
        // $results['image'] = '/img/contact/contact-location-one.jpeg';
        // $results['email'] = 'Email* (required)';
        // $results['company_name'] = 'Company Name* (required)';
        // $results['full_name'] = 'Full Name* (required)';
        // $results['type'] = 'Type* (required)';
        // $results['file'] = 'Drag and Drop or Click to Upload File';
        // $results['text'] = 'Add Text';
        

        // return $results;


        $component = $this->componentRepository->getComponentByName('requestcatalogue');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Extract data from component structure
        $results = [];
        
        // Map section_title to hero_title
        $results['section_title'] = $component->section_title ?? '';
        // $results['section_subtitle'] = $component->section_subtitle ?? '';

        $subtitle = $component->section_subtitle ?? '';

        $parts = explode('available in ', $subtitle);

        if (count($parts) === 2) {
            $results['section_subtitle'] = $parts[0] . 'available in<br>' . $parts[1];
        } else {
            $results['section_subtitle'] = $subtitle; // fallback if text changes
        }




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
        
        
        // Extract data from items array (4 elements with name and value fields)
        if (isset($component->items) && is_array($component->items) && count($component->items) > 0) {
            $items = $component->items[0]; // Get the first array of items
            
            foreach ($items as $item) {
                if (isset($item['name']) && isset($item['value'])) {
                    $fieldName = $item['name'];
                    $fieldValue = $item['value'];
                    
                    // Map specific field names to the expected format
                    switch ($fieldName) {
                        case 'email':
                            $results['email'] = $fieldValue;
                            break;
                        case 'company_name':
                            $results['company_name'] = $fieldValue;
                            break;
                        case 'full_name':
                            $results['full_name'] = $fieldValue;
                            break;
                        case 'tour_type':
                            $results['tour_type'] = $fieldValue;
                            break;
                        case 'type':
                            $results['type'] = $fieldValue;
                            break;
                        case 'text':
                            $results['text'] = $fieldValue;
                            break;
                        case 'image':
                            // Extract objectURL from the hero_image array structure
                            if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                $imageData = $fieldValue[0];
                                if (isset($imageData['objectURL'])) {
                                    $results['image'] = $imageData['objectURL'];
                                } else {
                                    $results['image'] = '';
                                }
                            } else {
                                $results['image'] = $fieldValue;
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
