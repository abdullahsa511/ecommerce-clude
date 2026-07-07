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
use App\Core\Repositories\Project\ProjectRepositoryInterface;

class Allprojects extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProjectRepositoryInterface $projectRepository,
        array $options = []
    ) {
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
            'name' => 'allprojects',
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
        $component = $this->componentRepository->getComponentByName('allprojects');

        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count']??60;
            $params['fields'] = $item['fields'];
             
        }
        $projects = $this->projectRepository->getAllProjects($params);
        $component->items = isset($projects['data']) ? $projects['data'] : [];
  
        $component->items = array_map(function($item) {
            if(isset($item['image']) && !empty($item['image']) && $item['image'] !== null){
                $image = json_decode($item['image'], true);
                if(is_array($image) && isset($image[0]['objectURL'])){
                    $item['image'] = $image[0]['objectURL'];
                }
            }
            if(isset($item['image_thumb']) && !empty($item['image_thumb']) && $item['image_thumb'] !== null){
                $image = json_decode($item['image_thumb'], true);
                if(is_array($image) && isset($image[0]['objectURL'])){
                    $item['image_thumb'] = $image[0]['objectURL'];
                }
            }
            $item['edit_link'] = env('APP_ADMIN_URL')."/ecommerce/projects/edit/{$item['project_id']}/general";
            return $item;
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);

        // $component->options = $this->options;
        $result = $component->toArray();
        $result['total'] = isset($projects['total']) ? $projects['total'] : 0;
        $result['show_total_pages'] = isset($projects['show_total_pages']) ? $projects['show_total_pages'] : 0;
        $result['current_page'] = isset($params['current_page']) ? $params['current_page'] : 0;
        $result['per_page'] = isset($params['per_page']) ? $params['per_page'] : 0;
        $result['options'] = $this->options;
        $result['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";

        return $result;
    }
}
