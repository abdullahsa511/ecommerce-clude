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

use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\NeedHelp\NeedHelpRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use function App\Core\System\utils\env;
use function App\Core\System\utils\config;

class Needhelp extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    protected $componentRepository;
    protected $needHelpRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository, 
        NeedHelpRepositoryInterface $needHelpRepository,
        array $options = []
    )
    {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->needHelpRepository = $needHelpRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }

    public static function getComponentMeta()
    {
        return [
            'name' => 'needhelp',
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
        $component = $this->componentRepository->getComponentByName('needhelp');
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
        $results['section_class'] = 'th-need-help-container grid-5';

        // pages link  start
        $base_url = env('APP_URL');

        if(isset($params['page']) && $params['page'] === 'catalogue'){
            $results['section_subtitle'] = "Need help selecting the right finishes or configurations from the catalogue? Speak with our team today.";
            $results['section_class'] = 'th-need-help-container grid-3';
        }

        if(isset($params['page']) && $params['page'] === 'contact'){
            $results['section_class'] = 'th-need-help-container grid-3';
        }
        if(isset($params['page']) && $params['page'] === 'blog-details'){
            $results['section_class'] = 'th-need-help-container grid-3';
        }
        if(isset($params['page']) && $params['page'] === 'contact-sales'){
            $results['section_class'] = 'th-need-help-container grid-4';
        }
        if(isset($params['page']) && $params['page'] === 'catalogue-confirmation'){
            $results['section_class'] = 'th-need-help-container grid-3';
        }
        if(isset($params['page']) && $params['page'] === 'project-details'){
            $results['section_class'] = 'th-need-help-container grid-3';
            $results['section_subtitle'] = "Planning a fit-out like ".$params['project_title']."? Talk to one of our experts today, request a catalogue, or visit our online store.";
        }
        
        // Extract items array with title and image for each item
        $results['items'] = [];
        
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                $skip = false;
                
                if (is_array($item)) {
                    foreach ($item as $field) {
                        if (isset($field['name']) && isset($field['value'])) {
                            $fieldName = $field['name'];
                            $fieldValue = $field['value'];
                            
                            switch ($fieldName) {
                                case 'icon':
                                    $itemData['icon'] = $fieldValue;
                                    break;
                                case 'title':
                                    $itemData['title'] = $fieldValue;
                                    
                                    if(isset($params['page']) && $params['page'] === 'catalogue'){
                                        if(in_array($fieldValue, ["Request a Catalogue", "Ready to Order?"])){
                                            $skip = true;
                                        }
                                    }
                                    if(isset($params['page']) && $params['page'] === 'blog-details'){
                                        if(in_array($fieldValue, ["Discover New Ideas", "Virtual Showroom"])){
                                            $skip = true;
                                        }
                                    }
                                    if(isset($params['page']) && $params['page'] === 'contact-sales'){
                                        $titleLower = is_string($fieldValue) ? strtolower(trim($fieldValue)) : '';
                                        if($titleLower === 'talk to an expert'){
                                            $skip = true;
                                        }
                                    }

                                    if(isset($params['page']) && $params['page'] === 'contact'){
                                        $titleLower = is_string($fieldValue) ? strtolower(trim($fieldValue)) : '';
                                      if(in_array($titleLower, ["virtual showroom", "talk to an expert"])){
                                        $skip = true;
                                      }
                                    }

                                    if(isset($params['page']) && $params['page'] === 'catalogue-confirmation'){
                                        $titleLower = is_string($fieldValue) ? strtolower(trim($fieldValue)) : '';
                                      if(in_array($titleLower, ["request a catalogue", "ready to order?"])){
                                        $skip = true;
                                      }
                                    }
                                    
                                    if(isset($params['page']) && $params['page'] === 'project-details'){
                                        $titleLower = is_string($fieldValue) ? strtolower(trim($fieldValue)) : '';
                                      if(in_array($titleLower, ["discover new ideas", "virtual showroom"])){
                                        $skip = true;
                                      }
                                    }

                                    

                                    break;
                                case 'description':
                                    $itemData['description'] = $fieldValue;
                                    break;
                                case 'link_text':
                                    $itemData['link_text'] = $fieldValue;
                                    break;
                                case 'link':
                                    $itemData['link'] = $fieldValue;
                                    break;
                            }
                        }
                    }
                }

                // if (
                //     isset($params['page']) &&
                //     $params['page'] === 'contact' &&
                //     isset($itemData['title']) &&
                //     $itemData['title'] === 'Talk to an Expert'
                // ) {
                //     $itemData['link_text'] = 'Contact Sales';
                //     $itemData['link'] = '/contact-sales';
                // }

                if($skip){
                    continue;
                }
                
                
                // Only add item if it has both title and image
                if (isset($itemData['title']) && isset($itemData['icon']) && isset($itemData['description']) && isset($itemData['link_text']) && isset($itemData['link'])) {
                    $results['items'][] = $itemData;
                }
            }
        }

        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";
        
        return $results;
    }
}
