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

use function App\Core\System\utils\config;
use function App\Core\System\utils\env;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\Repositories\Post\PostBlogSliderRepositoryInterface;

class Blogslider extends ComponentBase {
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
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->postBlogSliderRepository = $postBlogSliderRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'blogslider',
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
        $component = $this->componentRepository->getComponentByName('blogslider');
        $fields = $component->items[0]['fields'];
        // if (is_array($fields) && !in_array('name', $fields)) {
        //     $fields[] = 'name';
        // }
        $items = $this->postBlogSliderRepository->getBlogSlider(1, $fields);
        $component->items = array_map(function($item) {
            if(isset($item['feature_image_thumb'])){
                $imageObject = json_decode($item['feature_image_thumb'], true);
                    if(isset($imageObject[0]['objectURL'])) {
                        $item['image'] = $imageObject[0]['objectURL'];
                    }else if(isset($imageObject['objectURL'])) {
                        $item['image'] = $imageObject['objectURL'];
                    }
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
