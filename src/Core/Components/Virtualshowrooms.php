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
class Virtualshowrooms extends ComponentBase {
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
            'name' => 'virtualshowrooms',
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
        // $results['sectionTitle'] = 'Explore Virtually';
        // $results['sectionSubtitle'] = "Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.";
        // $results['items'] = [
        //     [
        //         'showroomImage' => '/img/contact-us/explore-1.png',
        //         'showroomTitle' => 'Sydney Showroom',
        //         'showroomBookBtn' => 'Book a Virtual Tour',
        //         'showroomViewBtn' => 'View Tour'
        //     ],
        //     [
        //         'showroomImage' => '/img/contact-us/explore-2.png',
        //         'showroomTitle' => 'Melbourne Showroom',
        //         'showroomBookBtn' => 'Book a Virtual Tour',
        //         'showroomViewBtn' => 'View Tour'
        //     ]
        // ];

        $component = $this->componentRepository->getComponentByName('virtualshowrooms');
        $results = [];
        $results['section_title'] = $component->section_title;
        $results['section_subtitle'] = $component->section_subtitle;
        foreach($component->items as $item){
            $itemData = [];
            if(isset($item['fields'])){
                foreach($item['fields'] as $field){
                    if(isset($field['name']) && isset($field['value'])){
                        if(isset($field['type']['type']['type']) && $field['type']['type']['type'] == 'JSON'){
                            $itemData[$field['name']] = json_decode($field['value'], true);
                            continue;
                        }
                        if(isset($field['type']['type']['type']) && $field['type']['type']['type'] == 'FileUpload'){
                            if(isset($field['value'][0]['objectURL'])){
                                $itemData[$field['name']] = $field['value'][0]['objectURL'];
                            }
                            continue;
                        }
                        $itemData[$field['name']] = $field['value'];
                    }
                }
            }
            $results['items'][] = $itemData;
        }

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        return $results;
    }
}
