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

class Projectdetailpenetrating extends ComponentBase {
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
            'name' => 'projectdetailpenetrating',
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
        $component = $this->componentRepository->getComponentByName('projectdetailpenetrating');
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
            if(isset($item['related_models']) && is_array($item['related_models'])){
                foreach($item['related_models'] as $joinModel){
                    $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
                }
            }
        }

        // echo "<pre>";
        // print_r($params);
        // echo "</pre>";
        
        $results = $this->projectRepository->getProjectDetailPenetratingComponentData($params);
        if(isset($results['main_image_one'])){
            $imageOne = json_decode($results['main_image_one'], true);
            if(isset($imageOne[0]['objectURL'])){
                $results['main_image_one'] = $imageOne[0]['objectURL'];
            }
        }
        if(isset($results['main_image_two'])){
            $imageTwo = json_decode($results['main_image_two'], true);
            if(isset($imageTwo[0]['objectURL'])){
                $results['main_image_two'] = $imageTwo[0]['objectURL'];
            }
        }
        $results['title'] = $component->section_title;
        $results['project_title'] = isset($params['title']) ? $params['title'] : '';
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;

        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        
        return $results;
    }
}
