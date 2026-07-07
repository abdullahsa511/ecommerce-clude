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

class Oursectorscurosel extends ComponentBase {
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
            'name' => 'oursectorscurosel',
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
        // $results['sectionTitle'] = "Our Sectors";
        // $results['sectionSubtitle'] = "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.";
        // $results['menuItems'] = [
        //     [
        //         'name' => 'Corporate',
        //     ],
        //     [
        //         'name' => 'Education'
        //     ],
        //     [
        //         'name' => 'Government'
        //     ],
        //     [
        //         'name' => 'Workplace'
        //     ],
        //     [
        //         'name' => 'Hospitality'
        //     ],
        //     [
        //         'name' => 'Healthcare'
        //     ]
        // ];
        // $results['items'] = [
        //     [
        //         'imgSrc' => '/img/bg/home/home_cat_workstation.jpg',
        //         'title' => 'Corporate',
        //         'subtitle' => 'Transform your workplace with innovative solutions'
        //     ],
        //     [
        //         'imgSrc' => '/img/bg/home/home_cat_chair.jpg',
        //         'title' => 'Education',
        //         'subtitle' => 'Create inspiring learning environments'
        //     ],
        //     [
        //         'imgSrc' => '/img/bg/home/home_cat_seating.jpg',
        //         'title' => 'Government',
        //         'subtitle' => 'Equip facilities with functional solutions'
        //     ],
        //     [
        //         'imgSrc' => '/img/bg/home/home_cat_workstation.jpg',
        //         'title' => 'Workplace',
        //         'subtitle' => 'Design collaborative workspaces'
        //     ],
        //     [
        //         'imgSrc' => '/img/bg/home/home_cat_chair.jpg',
        //         'title' => 'Hospitality',
        //         'subtitle' => 'Enhance guest experiences'
        //     ],
        //     [
        //         'imgSrc' => '/img/bg/home/home_cat_seating.jpg',
        //         'title' => 'Healthcare',
        //         'subtitle' => 'Create healing environments'
        //     ]
        // ];

        $component = $this->componentRepository->getComponentByName('oursectorscurosel');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        return $component->toArray();
    }
}
