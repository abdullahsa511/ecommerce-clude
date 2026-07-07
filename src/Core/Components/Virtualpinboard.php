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
use App\Core\Repositories\Component\ComponentRepositoryInterface;

class Virtualpinboard extends ComponentBase {
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
            'name' => 'virtualpinboard',
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
        $results['items'] = [
            [
                'image' => '/img/pinboard/pinboard img 1.png',
                'type' => 'Product - 1',
                'name' => 'Miro Task Chair - Black Fabric Seat / Black Mesh Black',
                'options' => [
                    [ 'src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1' ],
                    [ 'src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2' ],
                    [ 'src' => '/img/product-detail/third circle.png',  'alt' => 'Option 3' ],
                    [ 'src' => '/img/product-detail/first circle.png',  'alt' => 'Option 4' ]
                ],
                'comment_placeholder' => 'Add A Comment'
            ],
            [
                'image' => '/img/pinboard/pinboard img 2.png',
                'type' => 'Project - 1',
                'name' => 'Fiorelli Packing',
                'description' => 'Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Do Eiusmod Tempor Incididunt Ut Labore Et Dolore Magna Aliqua.',
                'options' => [],
                'comment_placeholder' => 'Add A Comment'
            ],
            [
                'image' => '/img/pinboard/pinboard img 3.png',
                'type' => 'Product - 2',
                'name' => 'Arc Screen - Black Fabric Seat / Black Mesh Black',
                'options' => [
                    [ 'src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1' ],
                    [ 'src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2' ],
                    [ 'src' => '/img/product-detail/third circle.png',  'alt' => 'Option 3' ],
                    [ 'src' => '/img/product-detail/first circle.png',  'alt' => 'Option 4' ]
                ],
                'comment_placeholder' => 'Add A Comment'
            ]
        ];

        return $results;

        // $component = $this->componentRepository->getComponentByName('categoriesmasonry');
        // $component->items = $this->taxonomyRepository->getTaxonomyItems(1,['taxonomy_item.taxonomy_item_id','taxonomy_item.image','taxonomy_item_content.name', 'taxonomy_item_content.content as description']);
        // list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        // $component->options = $this->options;
        // return $component->toArray();
    }
}
