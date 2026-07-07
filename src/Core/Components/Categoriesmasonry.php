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
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use function App\Core\System\utils\env;
class Categoriesmasonry extends ComponentBase {
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
            'name' => 'categoriesmasonry',
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
        $component = $this->componentRepository->getComponentByName('categoriesmasonry');
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
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        
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
                            $isMultiSelect = ($field['type']['type']['type'] ?? null) === 'MultiSelect';

                            if ($isMultiSelect) {
                                if ($fieldName === 'link') {
                                    // For link field, create a single object with all properties
                                    $itemData[$fieldName] = [];
                                    if (is_array($fieldValue)) {
                                        foreach ($fieldValue as $option) {
                                            if (isset($option['label']) && isset($option['value'])) {
                                                $itemData[$fieldName][$option['label']] = $option['value'];
                                            }
                                        }
                                    }
                                } else {
                                    // For other fields like subcategories, create array of objects
                                    $itemData[$fieldName] = [];
                                    if (is_array($fieldValue)) {
                                        foreach ($fieldValue as $option) {
                                            if (isset($option['label']) && isset($option['value'])) {
                                                $itemData[$fieldName][] = [$option['label'] => $option['value']];
                                            }
                                        }
                                    }
                                }
                            } else {
                                switch ($fieldName) {
                                    case 'heading':
                                        $itemData['heading'] = $fieldValue;
                                        break;
                                    case 'class':
                                        $itemData['class'] = $fieldValue;
                                        break;
                                    case 'des':
                                        $itemData['des'] = $fieldValue;
                                        break;
                                    case 'class':
                                        $itemData['class'] = $fieldValue;
                                        break;
                                    case 'style':
                                        $itemData['style'] = $fieldValue;
                                        break;
                                    case 'sort_order':
                                        $itemData['sort_order'] = $fieldValue;
                                        break;
                                    case 'image':
                                        // Extract objectURL from the image array structure
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
                }
                
                // Only add item if it has required fields
                if (isset($itemData['heading']) && isset($itemData['img']) && isset($itemData['des']) && isset($itemData['class']) && isset($itemData['style'])) {
                    $results['items'][$itemData['sort_order']] = $itemData;
                }
            }
        }
        usort($results['items'], function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
        
        return $results;
    }
}
