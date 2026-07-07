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

use App\Core\Models\User;
use App\Core\Services\AuthService;
use App\Core\System\Component\ComponentBase;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;


class Accountnavigation extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private TaxonomyItemRepositoryInterface $taxonomyRepository;
    private UserRepositoryInterface $userRepository;
    private AuthService $authService;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        TaxonomyItemRepositoryInterface $taxonomyRepository,
        UserRepositoryInterface $userRepository,
        AuthService $authService,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'accountnavigation',
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
        $user = $this->authService->getAuthUser();

        if (!$user instanceof User && !empty($this->options['user_id'])) {
            $user = $this->userRepository->find((int) $this->options['user_id']);
        }

        $profileName = '';
        $profileEmail = '';

        if ($user instanceof User) {
            $profileEmail = (string) ($user->email ?? '');
            $profileName = trim((string) ($user->display_name ?? ''));

            if ($profileName === '') {
                $profileName = trim(
                    trim((string) ($user->first_name ?? '')) . ' ' . trim((string) ($user->last_name ?? ''))
                );
            }

            if ($profileName === '' && $profileEmail !== '') {
                $profileName = $profileEmail;
            }
        }

        $results = [
            'profile' => [
                'name' => $profileName,
                'email' => $profileEmail,
                'description' => $profileEmail,
                'avatar' => '/img/account-dashboard/profile-pic.png'
            ],
            'navigation' => [
                [
                    'title' => 'Profile',
                    'icon' => 'fa-solid fa-user',
                    'url' => '/account/profile',
                    'active' => false
                ],
                [
                    'title' => 'Virtual Pinboards',
                    'icon' => 'fa-solid fa-clipboard-list',
                    'url' => '/account/virtual-pinboards',
                    'active' => false
                ],
                // [
                //     'title' => 'Recent Orders',
                //     'icon' => 'fas fa-shopping-cart',
                //     'url' => '/account/recent-orders',
                //     'active' => false
                // ],
                // [
                //     'title' => 'Active Quotes',
                //     'icon' => 'fa-solid fa-file',
                //     'url' => '/account/active-quotes',
                //     'active' => false
                // ],
                // [
                //     'title' => 'Scheduled Services',
                //     'icon' => 'fa-solid fa-user',
                //     'url' => '/account/delivery-install',
                //     'active' => false
                // ],
                [
                    'title' => 'Service Requests',
                    'icon' => 'fa-solid fa-gear',
                    'url' => '/account/create-request',
                    'active' => false
                ],
                // [
                //     'title' => 'Track Your Order',
                //     'icon' => 'fa-solid fa-truck',
                //     'url' => '/account/track-orders',
                //     'active' => false
                // ]
            ],
            'actions' => [
                [
                    'title' => 'Visit Our Showroom',
                    'url' => '/contact-us',
                    'icon' => 'fa-regular fa-arrow-up degree-60'
                ],
                [
                    'title' => 'Contact Sales',
                    'url' => '/contact-sales',
                    'icon' => 'fa-regular fa-arrow-up degree-60'
                ]
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
