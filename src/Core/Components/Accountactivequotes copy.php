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
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;


class Accountactivequotes extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private TaxonomyItemRepositoryInterface $taxonomyRepository;


    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        TaxonomyItemRepositoryInterface $taxonomyRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'accountactivequotes',
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
        $results = [
            'page_title' => 'Active Quotes',
            'quote_summary' => [
                'title' => 'Quote Title',
                'id' => '#907526',
                'description' => 'Lorem ipsum dolor sit amet will nec consectetur. Quis ut consectetur sed nec cursus orci,',
                'account' => 'Org',
                'amount' => '$260',
                'created_date' => 'June 11th, 2024',
                'actions' => [
                    [
                        'text' => 'Accept Quote',
                        'url' => 'contact.html',
                        'class' => 'th-btn-gray text-capitalize'
                    ],
                    [
                        'text' => 'Accept Quote',
                        'url' => 'contact.html',
                        'class' => 'th-btn-primary text-capitalize'
                    ]
                ]
            ],
            'table' => [
                'headers' => ['Item', 'Description', 'QTY', 'Unit Price', 'm Total', 'Section Total'],
                'section_title' => 'Items',
                'section_total' => 0,
                'items' => [
                    [
                        'image' => '/img/datatable/miro.png',
                        'alt' => 'Miro Task Chair Black Fabric Seat / Black Mesh Black',
                        'description' => 'Miro Task Chair Black Fabric Seat / Black Mesh Black',
                        'quantity' => '2',
                        'unit_price' => '$143.21',
                        'item_total' => '$286.42',
                        'comment_icon' => '/img/datatable/comment-icon.png'
                    ],
                    [
                        'image' => '/img/datatable/keywork-spine.png',
                        'alt' => 'Black Fabric Seat / Black Mesh Black',
                        'description' => 'Black Fabric Seat / Black Mesh Black',
                        'quantity' => '2',
                        'unit_price' => '$143.21',
                        'item_total' => '$286.42',
                        'comment_icon' => '/img/datatable/comment-icon.png'
                    ],
                    [
                        'image' => '/img/datatable/keywork.png',
                        'alt' => 'Black Fabric Seat / Black Mesh Black',
                        'description' => 'Black Fabric Seat / Black Mesh Black',
                        'quantity' => '2',
                        'unit_price' => '$143.21',
                        'item_total' => '$286.42',
                        'comment_icon' => '/img/datatable/comment-icon.png'
                    ]
                ]
            ],
            'footer' => [
                'sub_total' => 0,
                'gst' => '$987.00',
                'total_inc_gst' => '$9,821.00'
            ],
            'team_managers' => [
                'title' => 'Team Managers',
                'members' => [
                    [
                        'image' => '/img/contact/member-1.png',
                        'name' => 'Devon Lane',
                        'position' => 'Software Developer',
                        'phone_icon' => 'fa-solid fa-phone',
                        'email_icon' => 'fa-solid fa-envelope'
                    ],
                    [
                        'image' => '/img/contact/member-avatar.png',
                        'name' => 'Devon Lane',
                        'position' => 'Software Developer',
                        'phone_icon' => 'fa-solid fa-phone',
                        'email_icon' => 'fa-solid fa-envelope'
                    ]
                ]
            ],
            'quote_card' => [
                'title' => 'Quote Title',
                'id' => '#907526',
                'description' => 'Lorem ipsum dolor sit amet will nec consectetur. Quis ut consectetur sed nec cursus orci,',
                'account' => 'org',
                'amount' => '$260',
                'created_date' => 'June 11th, 2024',
                'add_comment_url' => 'contact.html',
                'view_quote_url' => 'contact.html',
                'accept_quote_url' => 'contact.html'
            ]
        ];
        return $results;

        // $component = $this->componentRepository->getComponentByName(categoriesmasonry');
        // $component->items = $this->taxonomyRepository->getTaxonomyItems(1,[taxonomy_item.taxonomy_item_id','taxonomy_item.image','taxonomy_item_content.name', 'taxonomy_item_content.content as description']);
        // list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        // $component->options = $this->options;
        // return $component->toArray();
    }
}
