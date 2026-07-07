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
use App\Core\Repositories\Admin\AdminRepositoryInterface;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;

class Salesteammelbourne extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private AdminRepositoryInterface $adminRepository;
    private ShowroomRepositoryInterface $showroomRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        AdminRepositoryInterface $adminRepository,
        ShowroomRepositoryInterface $showroomRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->adminRepository = $adminRepository;
        $this->showroomRepository = $showroomRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'salesteammelbourne',
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
        $component = $this->componentRepository->getComponentByName('salesteammelbourne');
        // $results['items'] = $this->componentRepository->getComponentItems($component);
        $results['items'] = $this->showroomRepository->getShowroomContactForComponent(2);
        $results['section_title'] = $component->section_title;
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;

        // echo '<pre>';
        // print_r($results);
        // echo '</pre>';
        // exit;   
 
        return $results;
        // $results['sectionTitle'] = 'Melbourne';
        // $results['items'] = [
        //     [
        //         'member_image' => '/img/contact/member-4.jpg',
        //         'member_name' => 'Devon Lane',
        //         'member_position' => 'Project Manager'
        //     ],
        //     [
        //         'member_image' => '/img/contact/member-5.jpg',
        //         'member_name' => 'Jane Doe',
        //         'member_position' => 'Project Manager'
        //     ],
        //     [
        //         'member_image' => '/img/contact/member-6.jpg',
        //         'member_name' => 'Devon Lane',
        //         'member_position' => 'Sales Executive'
        //     ],
        //     [
        //         'member_image' => '/img/contact/member-7.jpg',
        //         'member_name' => 'Jane Doe',
        //         'member_position' => 'Sales Executive'
        //     ]
        // ];
        
    }
}
