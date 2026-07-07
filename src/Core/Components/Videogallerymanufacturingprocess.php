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

use function App\Core\System\utils\config;

class Videogallerymanufacturingprocess extends ComponentBase {
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
            'name' => 'videogallerymanufacturingprocess',
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
        $component = $this->componentRepository->getComponentByName('videogallerymanufacturingprocess');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        // Extract data from component structure
        $results = [];
        
        
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                
                if (is_array($item)) {
                    foreach ($item as $field) {
                        if (isset($field['name']) && isset($field['value'])) {
                            $fieldName = $field['name'];
                            $fieldValue = $field['value'];
                            
                            switch ($fieldName) {
                                case 'src':
                                    if (is_string($fieldValue) && strpos($fieldValue, '//') === 0) {
                                        $itemData['src'] = $fieldValue;
                                    } else {
                                        if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                            $videoData = $fieldValue[0];
                                            if (isset($videoData['objectURL'])) {
                                                $itemData['src'] = $videoData['objectURL'];
                                            } else {
                                                $itemData['src'] = $fieldValue;
                                            }
                                        } else {
                                            $itemData['src'] = $fieldValue;
                                        }
                                    }
                                    break;
                                 case 'thumb':
                                        if (is_array($fieldValue) && isset($fieldValue[0]) && is_array($fieldValue[0])) {
                                            $imageData = $fieldValue[0];
                                            if (isset($imageData['objectURL'])) {
                                                $itemData['thumb'] = $imageData['objectURL'];
                                                $itemData['poster'] = $imageData['objectURL'];
                                            } else {
                                                $itemData['thumb'] = '';
                                            }
                                        } else {
                                            $itemData['thumb'] = $fieldValue;
                                            $itemData['poster'] = $fieldValue;
                                        }
                                        if(isset($itemData['isVideo']) && !$itemData['isVideo']) unset($itemData['poster']);
                                        break;
                                case 'subHtml':
                                    $itemData['subHtml'] = $fieldValue;
                                    break;
                                case 'isVideo':
                                    $itemData['isVideo'] = $fieldValue;
                                    if(!$fieldValue) unset($itemData['poster']);
                                    break;
                                default:
                                    $itemData[$fieldName] = $fieldValue;
                                    break;
                            }
                        }
                    }
                }
                
                // Only add item if it has both title and image
                if (isset($itemData['src']) && isset($itemData['thumb']) && isset($itemData['subHtml'])) {
                    $results[] = $itemData;
                }
            }
        }
        $image = isset($component->image[0]['objectURL']) ? $component->image[0]['objectURL'] : '';
        $config = config('APP_ADMIN_URL');
        return [
            'items' => $results,
            'background_image' => $image,
            'section_title' => isset($component->section_title) ? $component->section_title : '',
            'section_subtitle' => isset($component->section_subtitle) ? $component->section_subtitle : '',
            'component_link' => $config."/components/{$component->component_id}/items",
        ];
    }
}
