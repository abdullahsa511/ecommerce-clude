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
use App\Core\Repositories\Media\MediaRepositoryInterface;

class Imagemasonrygallery extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        MediaRepositoryInterface $mediaRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->mediaRepository = $mediaRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'imagemasonrygallery',
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
        // $results['sectionTitle'] = 'Image Masonry Gallery';
        // $results['items'] = [
        //     [
        //         'id' => 1,
        //         'file' => 'https://www.instagram.com/archi_furniture/',
        //         'type' => 'image',
        //         'meta' => 'caption-1'
        //     ],
        //     [
        //         'id' => 2,
        //         'file' => 'https://www.instagram.com/archi_furniture/',
        //         'type' => 'image',
        //         'meta' => 'caption-2'
        //     ],
        //     [
        //         'id' => 3,
        //         'file' => 'https://www.instagram.com/archi_furniture/',
        //         'type' => 'image',
        //         'meta' => 'caption-3'
        //     ],
        //     [
        //         'id' => 4,
        //         'file' => 'https://www.instagram.com/archi_furniture/',
        //         'type' => 'image',
        //         'meta' => 'caption-4'
        //     ]
        // ];

        $component = $this->componentRepository->getComponentByName('imagemasonrygallery');
        $params = [];
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }
        $component->items = $this->mediaRepository->getImageMasonryGallery($params);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        return $component->toArray();
    }
}
