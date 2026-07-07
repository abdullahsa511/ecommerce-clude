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

use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Project\ProjectRepositoryInterface;

use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;
use function App\Core\System\utils\config;

class Featuredprojectslider extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds


    protected $componentRepository;
    protected $projectRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository, 
        ProjectRepositoryInterface $projectRepository,
        array $options = []
    )
    {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->projectRepository = $projectRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'featuredprojectslider',
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

// echo '<pre>';
// print_r($params);
// echo '</pre>';
// exit;
        $component = $this->componentRepository->getComponentByName('featuredprojectslider');
        if(!$component || !isset($component->component_id)){
            return [];
        }
        $projectId = isset($params['project_id']) ? $params['project_id'] : '';
        $params = [];
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
            $params['is_recent'] = $item['is_recent'];
            $params['is_featured'] = $item['is_featured'];
            $params['project_id'] =  $projectId;
        }
        $items = $this->projectRepository->getFeaturedProjectSliderComponentData($params);
        $component->items = array_map(function($item) {
            $imageObject = json_decode($item['image'], true);
            if(isset($imageObject[0]['objectURL'])) {
                $item['image'] = $imageObject[0]['objectURL'];
            }
            return $item;
        }, $items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        $config = config('APP_ADMIN_URL');
        $componentArray = $component->toArray();
        $componentArray['component_link'] = $config."/components/{$component->component_id}/items";
        
        return $componentArray;
    }
}
