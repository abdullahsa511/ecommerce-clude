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

use function App\Core\System\utils\env;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use function App\Core\System\utils\config;

class Showrooms extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ShowroomRepositoryInterface $showroomRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ShowroomRepositoryInterface $showroomRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->showroomRepository = $showroomRepository;
    }

    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'showrooms',
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
        $component = $this->componentRepository->getComponentByName('showrooms');
        $results = [];
        if($component && isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }

        $links =  isset($component->links) ? $component->links : [];
        $results['items'] = $this->showroomRepository->getShowroomsComponentData($params, $links);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;

        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";
        $results['section_title'] = $component->section_title;
        $results['section_subtitle'] = $component->section_subtitle;
        $results['section_link'] = $component->section_link;

        
        return $results;
    }
}
