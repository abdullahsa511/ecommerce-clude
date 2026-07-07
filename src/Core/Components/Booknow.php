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
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use function App\Core\System\utils\config;
class Booknow extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ShowroomRepositoryInterface $showroomRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ShowroomRepositoryInterface $showroomRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->showroomRepository = $showroomRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'booknow',
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
        $component = $this->componentRepository->getComponentByName('booknow');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Extract data from component structure
        $results = [];
        
        // Map section_title to hero_title
        $results['section_title'] = $component->section_title ?? '';
    
        // Extract data from items array (4 elements with name and value fields)
        if (isset($component->items) && is_array($component->items) && count($component->items) > 0) {
            $items = $component->items[0]; // Get the first array of items
            
            foreach ($items as $item) {
                if (isset($item['name']) && isset($item['value'])) {
                    $fieldName = $item['name'];
                    $fieldValue = $item['value'];
                    
                    // Map specific field names to the expected format
                    switch ($fieldName) {
                        case 'name':
                            $results['name'] = $fieldValue;
                            break;
                        case 'member':
                            $results['member'] = $fieldValue;
                            break;
                        case 'location':
                            $results['location'] = $fieldValue;
                            break;
                        case 'tour_type':
                            $results['tour_type'] = $fieldValue;
                            break;
                        case 'calendar_title':
                            $results['calendar_title'] = $fieldValue;
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

        $results['members'] = $this->showroomRepository->getMembersData();

        // echo "<pre>";
        // print_r($results);
        // echo "</pre>";
        // exit;

        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";
        return $results;
    }
}
