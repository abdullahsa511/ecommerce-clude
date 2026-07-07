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
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;

class Bookingemail extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        PinboardRepositoryInterface $pinboardRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->pinboardRepository = $pinboardRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'bookingemail',
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
    
        $pinboardItems = $this->pinboardRepository->getPinboardFinalBookingComponent($params['uuid'], 'email');
        // get comment photo from pinboard_id
        $results = [];
        $results['section_title'] = 'Book an Email';
        $results['image'] = "/img/contact/member-avatar.png";
        $results['name'] = "Devon Lane";
        $results['member'] = "Superman";
        $results['location'] = "Melbourne, Australia";
        $results['tour_type'] = "Virtual Tour";
        $results['calendar_title'] = "Time Zone";
        $results['pinboard_items'] = $pinboardItems;

        // echo '<pre>';
        // print_r($results['pinboard_items']['service_request']);
        // echo '</pre>';

        return $results;


       
        // return $results;


        // $component = $this->componentRepository->getComponentByName('categoriesmasonry');
        // $component->items = $this->taxonomyRepository->getTaxonomyItems(1,['taxonomy_item.taxonomy_item_id','taxonomy_item.image','taxonomy_item_content.name', 'taxonomy_item_content.content as description']);
        // list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        // $component->options = $this->options;
        // return $component->toArray();
    }
}
