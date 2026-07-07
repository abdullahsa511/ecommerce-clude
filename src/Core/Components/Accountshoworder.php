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
use App\Core\Repositories\Order\OrderRepositoryInterface;


class Accountshoworder extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private TaxonomyItemRepositoryInterface $taxonomyRepository;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        TaxonomyItemRepositoryInterface $taxonomyRepository,
        OrderRepositoryInterface $orderRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->orderRepository = $orderRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'accountshoworder',
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

        // get order with details
        $results = $this->orderRepository->showOrder($params['uuid']);

        if(isset($results)){
            $results['team_managers'] = [
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
            ];
            $results['order_card'] = [
                'title' => $results['show_order_summary']['title'],
                'id' =>  $results['show_order_summary']['id'],
                'description' => $results['show_order_summary']['description'],
                'account' => $results['show_order_summary']['account'],
                'amount' => $results['show_order_summary']['amount'],
                'created_date' => $results['show_order_summary']['created_date'],
                'add_comment_url' => 'contact.html',
                'view_quote_url' => 'contact.html',
                'accept_quote_url' => 'contact.html'
            ];
        }

        return $results;
    }
}
