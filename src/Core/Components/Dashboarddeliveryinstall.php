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
use App\Core\Repositories\Logistic\LogisticRepositoryInterface;

class Dashboarddeliveryinstall extends ComponentBase {

    private LogisticRepositoryInterface $logisticRepository;
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private PostBlogSliderRepositoryInterface $postBlogSliderRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PostBlogSliderRepositoryInterface $postBlogSliderRepository,
        LogisticRepositoryInterface $logisticRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->postBlogSliderRepository = $postBlogSliderRepository;
        $this->logisticRepository = $logisticRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'dashboarddeliveryinstall',
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
     
        $component = $this->componentRepository->getComponentByName('dashboarddeliveryinstall');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        $results = $this->logisticRepository->getCustomerLogisticDatesForComponent($params);

        return $results;
    }
}
