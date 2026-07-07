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
use App\Core\Repositories\Product\CategoryRepositoryInterface;
use function App\Core\System\utils\config;

class Blockquote extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private CategoryRepositoryInterface $categoryRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        CategoryRepositoryInterface $categoryRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->categoryRepository = $categoryRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'blockquote',
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
        $component = $this->componentRepository->getComponentByName('blockquote');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        


        $data['image'] = isset($component->image) && is_array($component->image) && isset($component->image[0]) && is_array($component->image[0]) ? $component->image[0]['objectURL'] : '';

        $config = config('APP_ADMIN_URL');
        $data['component_link'] = $config."/components/{$component->component_id}/overview";

        $data['description'] = $component->description ?? '';
        
        return $data;
    }
}
