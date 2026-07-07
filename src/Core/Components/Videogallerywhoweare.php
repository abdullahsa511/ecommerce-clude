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

class Videogallerywhoweare extends ComponentBase {
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
            'name' => 'videogallerywhoweare',
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
        // $results['section_title'] = 'Our principles';
        // $results = [
        //     [
        //         'src' => '//vimeo.com/112836958',
        //         'poster' => '/img/vimeo-video-poster.jpg',
        //         'thumb' => '/img/vimeo-video-poster.jpg',
        //         'subHtml' => '<h4>Nature</h4><p>Video by <a target="_blank" href="https://vimeo.com/charliekaye">Charlie Kaye</a></p>'
        //     ],
        //     [
        //         'src' => '//www.youtube.com/watch?v=EIUJfXk3_3w',
        //         'thumb' => 'https://img.youtube.com/vi/EIUJfXk3_3w/maxresdefault.jpg',
        //         'subHtml' => '<h4>Puffin Hunts Fish To Feed Puffling | Blue Planet II | BBC Earth</h4><p>This puffin parent must go out to sea to feed his chick, but he must evade other birds that would rob him.</p>'
        //     ],
        //     [
        //         'src' => 'img/image-1.avif',
        //         'thumb' => 'img/thumb1.avif',
        //         'subHtml' => '<div class="lightGallery-captions"><h4>Caption 1</h4><p>Description of the slide 1</p></div>'
        //     ],
        //     [
        //         'src' => 'img/image-2.avif',
        //         'thumb' => 'img/thumb2.avif',
        //         'subHtml' => '<div class="lightGallery-captions"><h4>Caption 2</h4><p>Description of the slide 2</p></div>'
        //     ]
        // ];

        // return $results;

        $component = $this->componentRepository->getComponentByName('videogallerywhoweare');
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
                                    // Check if it's a YouTube or Vimeo URL (starts with //)
                                    if (is_string($fieldValue) && strpos($fieldValue, '//') === 0) {
                                        // Keep YouTube/Vimeo URLs as they are
                                        $itemData['src'] = $fieldValue;
                                    } else {
                                        // For other URLs, extract objectURL from array structure
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
                                        // Extract objectURL from the image array structure
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
        $response['items'] = $results;
        $response['section_title'] = $component->section_title ?? '';
        $response['section_subtitle'] = $component->section_subtitle ?? '';
        $config = config('APP_ADMIN_URL');
        $response['component_link'] = $config."/components/{$component->component_id}/items";
        return $response;
    }
}
