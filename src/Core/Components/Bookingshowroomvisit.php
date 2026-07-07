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

class Bookingshowroomvisit extends ComponentBase {
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
            'name' => 'bookingshowroomvisit',
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

        $results = [];
        $results['section_title'] = 'Book now';
        $results['image'] = "/img/contact/member-avatar.png";
        $results['name'] = "Devon Lane";
        $results['member'] = "Superman";
        $results['location'] = "Melbourne, Australia";
        $results['tour_type'] = "Virtual Tour";
        $results['calendar_title'] = "Time Zone";
        $pinboardItems = $this->pinboardRepository->getPinboardFinalBookingComponent($params['id'], 'showroom_visit');
        $results['pinboard'] = $pinboardItems;

        return $results;
    }
}
