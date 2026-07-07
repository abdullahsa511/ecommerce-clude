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
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;

use function App\Core\System\utils\env;

class Blogrelatedarticleslider extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds


    private ComponentRepositoryInterface $componentRepository;
    private PostRepositoryInterface $postRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PostRepositoryInterface $postRepository,
        array $options = []
    )
    {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->postRepository = $postRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'blogrelatedarticleslider',
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
        $component = $this->componentRepository->getComponentByName('blogrelatedarticleslider');
        $params['model'] = 'post';
        // $component['name'] = 'blogrelatedarticleslider';
        $results = [];
        $results['items'] = $this->postRepository->getRelatedArticlesSliderComponentData($params);
        $results['section_title'] = isset($component->section_title) ? $component->section_title :  "Related Articles";
        $results['section_subtitle'] = isset($component->section_subtitle) ? $component->section_subtitle : "Discover more articles.";
        $results['link_text'] = 'View All Articles'; 
        $results['link_url'] = env('APP_URL').'/blog';

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        return $results;
    }
}
