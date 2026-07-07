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

class Projectdetails extends ComponentBase {
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
            'name' => 'projectdetails',
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
        // $results['section_title'] = "Project Details";
        // $results['description'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vehicula libero eget magna fermentum, eget feugiat odio fermentum. Fusce euismod nulla sit amet vestibulum venenatis. Proin vel lacus at nisi elementum ullamcorper. Nam nec aliquam elit. Mauris viverra augue ac eros venenatis, a scelerisque ipsum luctus. Vivamus euismod sapien sit amet sapien efficitur, vel convallis sem molestie.";
        // $results['description-two'] = "Sed nec odio magna. Aliquam non libero ultricies, efficitur nulla id, ultricies urna. Nullam at placerat justo, non fermentum magna. Nullam nec libero odio. Sed eleifend euismod tellus, ut fermentum libero pellentesque a. Sed eget nulla et mi consectetur vestibulum. Phasellus at leo eu orci vestibulum tincidunt. Vestibulum fringilla mi id felis varius ultricies. Donec nec suscipit nulla. Vivamus vitae diam purus.";

        // return $results;


        $component = $this->componentRepository->getComponentByName('projectdetails');
        if(isset($component->items[0])){
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
        }

        $results = $this->projectRepository->getProjectDetailsComponentData($params);
       

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";

        return $results;
    }
}
