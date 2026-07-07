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

class Ourdesignprocess extends ComponentBase {
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
            'name' => 'ourdesignprocess',
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
        // $results['sectionTitle'] = 'Our design process';
        // $results['sectionSubtitle'] = "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.";
        // $results['extraLink'] = "https://www.google.com";
        // $results['items'] = [
        //     [
        //         'image' => '/img/contact-us/design-process-1.jpg',
        //         'step_number' => '01',
        //         'step_title' => 'Planning',
        //         'step_description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
        //     ],
        //     [
        //         'image' => '/img/contact-us/design-process-2.jpg',
        //         'step_number' => '02',
        //         'step_title' => 'Proposal',
        //         'step_description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
        //     ],
        //     [
        //         'image' => '/img/contact-us/design-process-3.jpg',
        //         'step_number' => '03',
        //         'step_title' => 'Installation',
        //         'step_description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
        //     ]
        // ];

        // return $results;


        $component = $this->componentRepository->getComponentByName('ourdesignprocess');
        $component->items = $this->componentRepository->getComponentItems($component);

        $results['section_title'] = $component->section_title ?? '';
        $results['section_subtitle'] = $component->section_subtitle ?? '';
        $results['items'] = [];
        
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                
                // Extract the item data based on the component structure
                if (is_array($item)) {
                    $itemData['step_number'] = $item['step_number'] ?? '';
                    $itemData['step_title'] = $item['step_title'] ?? '';
                    $itemData['step_description'] = $item['step_description'] ?? '';
                    $itemData['class'] = $item['class'] ?? '';
                    
                    // Handle image field - it might be a string URL or an array
                    if (isset($item['image'])) {
                        if (is_array($item['image']) && isset($item['image'][0]) && is_array($item['image'][0])) {
                            // If image is an array with objectURL structure
                            $imageData = $item['image'][0];
                            $itemData['image'] = $imageData['objectURL'] ?? '';
                        } else {
                            // If image is already a string URL
                            $itemData['image'] = $item['image'];
                        }
                    } else {
                        $itemData['image'] = '';
                    }
                    
                    // Only add item if it has the required fields
                    if (!empty($itemData['step_title']) && !empty($itemData['image'])) {
                        $results['items'][] = $itemData;
                    }
                }
            }
        }
        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";

        return $results;

        
    }
}
