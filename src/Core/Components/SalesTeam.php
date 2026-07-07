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

class Salesteam extends ComponentBase {
    private ComponentRepositoryInterface $componentRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
    }
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'salesteam',
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
        $results['sectionTitle'] = 'Connect with our sales team';
        $results['sectionSubtitle'] = "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.";
        $results['items'] = [
            [
                "itemName" => "Sydney",
                "teamData" => [
                    [
                        'memberImage' => '/img/contact/member-0.jpg',
                        'memberName' => 'Devon Lane',
                        'memberPosition' => 'Director'
                    ],
                    [
                        'memberImage' => '/img/contact/member-1.jpg',
                        'memberName' => 'Jane Doe',
                        'memberPosition' => 'Senior Sales Executive'
                    ],
                    [
                        'memberImage' => '/img/contact/member-2.jpg',
                        'memberName' => 'Devon Lane',
                        'memberPosition' => 'Sales Executive'
                    ],
                    [
                        'memberImage' => '/img/contact/member-3.jpeg',
                        'memberName' => 'Jane Doe',
                        'memberPosition' => 'Sales Executive'
                    ]
                ]
            ],
            [
                "itemName" => "Melbourne",
                "teamData" => [
                    [
                        'memberImage' => '/img/contact/member-4.jpg',
                        'memberName' => 'Devon Lane',
                        'memberPosition' => 'Project Manager'
                    ],
                    [
                        'memberImage' => '/img/contact/member-5.jpg',
                        'memberName' => 'Jane Doe',
                        'memberPosition' => 'Project Manager'
                    ],
                    [
                        'memberImage' => '/img/contact/member-6.jpg',
                        'memberName' => 'Devon Lane',
                        'memberPosition' => 'Sales Executive'
                    ],
                    [
                        'memberImage' => '/img/contact/member-7.jpg',
                        'memberName' => 'Jane Doe',
                        'memberPosition' => 'Sales Executive'
                    ]
                ]
            ]
        ];

        list($results) = Event::trigger(__CLASS__,__FUNCTION__, $results);

        return array_merge($results, $this->options);
    }
}
