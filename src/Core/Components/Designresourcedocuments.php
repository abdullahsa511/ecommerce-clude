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
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;

class Designresourcedocuments extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private DesignResourceRepositoryInterface $designResourceRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        DesignResourceRepositoryInterface $designResourceRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->designResourceRepository = $designResourceRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'designresourcedocuments',
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
        // $results['total_result'] = 'Documents: 6 Results';
        // $results['items'] = [
        //     [
        //         'name' => 'Hugs',
        //         'image' => '/img/design-resources/vira.png',
        //         'alt' => 'archi-chair',
        //         'formats' => [
        //             ['text' => 'Spec Sheet (PDF)'],
        //             ['text' => 'User Guide (PDF)'],
        //             ['text' => 'Care & Maintenance (PDF)'],
        //             ['text' => 'Installation Guide (PDF)'],
        //             ['text' => 'Warranty (PDF)']
        //         ],
        //         'link_text' => 'Download All'
        //     ],
        //     [
        //         'name' => 'Ted',
        //         'image' => '/img/design-resources/archi.png',
        //         'alt' => 'archi-chair',
        //         'formats' => [
        //             ['text' => 'Spec Sheet (PDF) 2'],
        //             ['text' => 'User Guide (PDF) 2'],
        //             ['text' => 'Care & Maintenance (PDF) 2'],
        //             ['text' => 'Installation Guide (PDF) 2'],
        //             ['text' => 'Warranty (PDF) 2']
        //         ],
        //         'link_text' => 'Download All'
        //     ],
        //     [
        //         'name' => 'Franki',
        //         'image' => '/img/design-resources/vira.png',
        //         'alt' => 'archi-chair',
        //         'formats' => [
        //             ['text' => 'Spec Sheet (PDF) 3'],
        //             ['text' => 'User Guide (PDF) 3'],
        //             ['text' => 'Care & Maintenance (PDF) 3'],
        //             ['text' => 'Installation Guide (PDF) 3'],
        //             ['text' => 'Warranty (PDF) 3']
        //         ],
        //         'link_text' => 'Download All'
        //     ],
        //     [
        //         'name' => 'Hugs',
        //         'image' => '/img/design-resources/vira.png',
        //         'alt' => 'archi-chair',
        //         'formats' => [
        //             ['text' => 'Spec Sheet (PDF)'],
        //             ['text' => 'User Guide (PDF)'],
        //             ['text' => 'Care & Maintenance (PDF)'],
        //             ['text' => 'Installation Guide (PDF)'],
        //             ['text' => 'Warranty (PDF)']
        //         ],
        //         'link_text' => 'Download All'
        //     ],
        //     [
        //         'name' => 'Ted',
        //         'image' => '/img/design-resources/archi.png',
        //         'alt' => 'archi-chair',
        //         'formats' => [
        //             ['text' => 'Spec Sheet (PDF) 2'],
        //             ['text' => 'User Guide (PDF) 2'],
        //             ['text' => 'Care & Maintenance (PDF) 2'],
        //             ['text' => 'Installation Guide (PDF) 2'],
        //             ['text' => 'Warranty (PDF) 2']
        //         ],
        //         'link_text' => 'Download All'
        //     ],
        //     [
        //         'name' => 'Franki',
        //         'image' => '/img/design-resources/vira.png',
        //         'alt' => 'archi-chair',
        //         'formats' => [
        //             ['text' => 'Spec Sheet (PDF) 3'],
        //             ['text' => 'User Guide (PDF) 3'],
        //             ['text' => 'Care & Maintenance (PDF) 3'],
        //             ['text' => 'Installation Guide (PDF) 3'],
        //             ['text' => 'Warranty (PDF) 3']
        //         ],
        //         'link_text' => 'Download All'
        //     ]
        // ];

        // return $results;

        $component = $this->componentRepository->getComponentByName('designresourcedocuments');
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }

        $params['type'] = 'documents';
        $results = $this->designResourceRepository->getDesignResourceDocumentsComponentData($params);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $totalLoaded = count($results['items']) + $params['offset'];
        $results['pagination'] = [
            'per_page' => $params['per_page'], 
            'current_page' => $params['current_page'], 
            'offset' => $totalLoaded,
            'context' => $params['context'],
            'category' => $params['category'],
            'model_id' => $params['model_id'],
            'model_name' => $params['model_name'],
            'total' => $results['total']
        ];
        return $results;

    }
}
