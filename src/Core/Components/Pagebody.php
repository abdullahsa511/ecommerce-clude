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
use App\Core\Repositories\Page\PageRepositoryInterface;
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;

use function App\Core\System\utils\env;

class Pagebody extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];
    public $cacheExpire = 0; //seconds

    private PostRepositoryInterface $postRepository;
    private PageRepositoryInterface $pageRepository;
    private ComponentRepositoryInterface $componentRepository;



    public function __construct(
        PostRepositoryInterface $postRepository,
        PageRepositoryInterface $pageRepository,
        ComponentRepositoryInterface $componentRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->postRepository = $postRepository;
        $this->pageRepository = $pageRepository;
        $this->componentRepository = $componentRepository;
    }
    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'pagebody',
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

    function results($params = [])
    {
        $results = [];
        
        $component = $this->componentRepository->getComponentByName('pagebody');
        if(isset($component->items[0])){
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
            $params['item_count'] = $item['item_count'];
            foreach($item['related_models'] as $joinModel){
                $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
            }
        }

        $results = $this->pageRepository->getPageBodyForComponent($params); 
        
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['excerpt_title'] = "Details";
        return $results;
    }
}
