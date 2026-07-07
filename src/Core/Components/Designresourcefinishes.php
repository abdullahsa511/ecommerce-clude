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
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\Repositories\Post\PostBlogSliderRepositoryInterface;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;

class Designresourcefinishes extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private PostBlogSliderRepositoryInterface $postBlogSliderRepository;
    private DesignResourceRepositoryInterface $designResourceRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PostBlogSliderRepositoryInterface $postBlogSliderRepository,
        DesignResourceRepositoryInterface $designResourceRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->postBlogSliderRepository = $postBlogSliderRepository;
        $this->designResourceRepository = $designResourceRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'designresourcefinishes',
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
        // $results['total_result'] = 'Finishes: 6 Results';
        // $results['items'] = [
        //     [
        //         'image' => '/img/design-resources/finishes/access.png',
        //         'image_alt' => 'Access Mesh',
        //         'title' => 'Access Mesh',
        //         'link_text' => 'Download All',
        //         'grade' => 'Grade L1'
        //     ],
        //     [
        //         'image' => '/img/design-resources/finishes/amethyst.png',
        //         'image_alt' => 'archi-chair',
        //         'title' => 'Amethyst',
        //         'link_text' => 'Download All',
        //         'grade' => 'Grade P3'
        //     ],
        //     [
        //         'image' => '/img/design-resources/finishes/ass.png',
        //         'image_alt' => 'archi-chair',
        //         'title' => 'Ash',
        //         'link_text' => 'Download All',
        //         'grade' => 'Grade L2'
        //     ],
        //     [
        //         'image' => '/img/design-resources/finishes/access.png',
        //         'image_alt' => 'Access Mesh',
        //         'title' => 'Access Mesh',
        //         'link_text' => 'Download All',
        //         'grade' => 'Grade L1'
        //     ],
        //     [
        //         'image' => '/img/design-resources/finishes/amethyst.png',
        //         'image_alt' => 'archi-chair',
        //         'title' => 'Amethyst',
        //         'link_text' => 'Download All',
        //         'grade' => 'Grade P3'
        //     ],
        //     [
        //         'image' => '/img/design-resources/finishes/ass.png',
        //         'image_alt' => 'archi-chair',
        //         'title' => 'Ash',
        //         'link_text' => 'Download All',
        //         'grade' => 'Grade L2'
        //     ]
        // ];

        // return $results;


        $component = $this->componentRepository->getComponentByName('designresourcefinishes');
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count']??$params['item_count'];
            $params['fields'] = $item['fields'];
        }
        $params['type'] = 'finishes';
        $results = $this->designResourceRepository->getDesignResourceFinishesComponentData($params);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        return $results;

    }
}
