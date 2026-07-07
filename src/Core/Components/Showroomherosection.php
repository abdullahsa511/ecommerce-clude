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
 * @Company: SA Technology
 * @Date: 04-10-2025
 * @Develop by: Mohammad Ali Abdullah
 */

namespace App\Core\Components;

use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use App\Core\Utilities\Debug;

use function App\Core\System\utils\env;

class Showroomherosection extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ShowroomRepositoryInterface $showroomRepository;
    private ComponentRepositoryInterface $componentRepository;

    public function __construct(
        ShowroomRepositoryInterface $showroomRepository,
        ComponentRepositoryInterface $componentRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->showroomRepository = $showroomRepository;
        $this->componentRepository = $componentRepository;
    }
    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'showroomherosection',
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

    function results($params = [])
    {
        // echo '<pre>';
        // print_r($params);
        // echo '</pre>';
        $results = [];

        $component = $this->componentRepository->getComponentByName('showroomherosection');
        if(!$component || !isset($component->component_id)){
            return [];
        }
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
        }

        $results = $this->showroomRepository->getShowroomComponentData($params);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['edit_link'] = env('APP_ADMIN_URL')."/showrooms/edit/{$results['showrooms_id']}/general";
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        $results['buttons'] = $component->buttons;

        // $results['banner_image'] = env('app.url')($results['banner_image']);
        return $results;
    }
}
