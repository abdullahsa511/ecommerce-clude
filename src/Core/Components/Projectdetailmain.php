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

class Projectdetailmain extends ComponentBase {
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
            'name' => 'projectdetailmain',
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

    // {
    //     "image": "/img/project-detail/main-banner-resize.png",
    //     "subtitle": "Pellentesque sodales lacinia diam, vel lacinia lectus ultrices id. Maecenas viverra justo nec mauris pulvinar dignissim. Proin quis arcu eu velit vehicula fermentum. Ut pharetra, nunc sed fringilla fermentum, nulla eros rhoncus felis, ac varius ligula quam nec nisl. Proin ac tellus sed dui ultrices tempus. Duis ut arcu et diam lacinia cursus nec non justo. "
    // },
    // {
    //     "image": "/img/project-detail/location.png",
    //     "subtitle": "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque."
    // }

    function results($params = []) {
        // $results = [];
        // $results['sectionSubtitle'] = "Sed nec odio magna. Aliquam non libero ultricies, efficitur nulla id, ultricies urna. Nullam at placerat justo, non fermentum magna. Nullam nec libero odio. Sed eleifend euismod tellus, ut fermentum libero pellentesque a. Sed eget nulla et mi consectetur vestibulum. Phasellus at leo eu orci vestibulum tincidunt. Vestibulum fringilla mi id felis varius ultricies. Donec nec suscipit nulla. Vivamus vitae diam purus.";
        // $results['image'] = "/img/project-detail/main-banner-resize.png";
        // $results['title'] = "Project Detail";
        // $results['subtitle'] = "Pellentesque sodales lacinia diam, vel lacinia lectus ultrices id. Maecenas viverra justo nec mauris pulvinar dignissim. Proin quis arcu eu velit vehicula fermentum. Ut pharetra, nunc sed fringilla fermentum, nulla eros rhoncus felis, ac varius ligula quam nec nisl. Proin ac tellus sed dui ultrices tempus. Duis ut arcu et diam lacinia cursus nec non justo. ";
        // $results['image2'] = "/img/project-detail/location.png";
        // $results['subtitle2'] = "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.";

        // return $results;

        $component = $this->componentRepository->getComponentByName('projectdetailmain');
        $results = [];
        if($component && isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }
        $project = $this->componentRepository->getProjectDetailMainComponentData($params);
        $results['sectionSubtitle'] = $project->sectionSubtitle;
        $results['title'] = $project->title;
        $results['subtitle'] = $project->subtitle;
        $results['subtitle2'] = $project->subtitle2;
        foreach($component->images as $key => $image){
            $results['image'.($key+1)] = $image[0]['objectURL'];
        }
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        return $results;
    }
}
