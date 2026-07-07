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
use App\Core\System\Component\ComponentBase;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;

class Accountprofile extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0;

    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CustomerRepositoryInterface $customerRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    function cacheKey() {
        return false;
    }

    public static function getComponentMeta()
    {
        return [
            'name' => 'accountprofile',
            'class' => self::class,
            'validOptions' => [
                'component_id',
                'user_id',
            ],
            'filePath' => __FILE__,
            'cacheKey' => null,
            'data' => [],
            'designOnly' => false
        ];
    }

    function results($params = []) {
        $userId = (int) ($params['user_id'] ?? 0);
        if ($userId < 1) {
            return [];
        }

        $user = $this->userRepository->find($userId);
        if (!$user instanceof User) {
            return [];
        }

        $customer = $this->customerRepository->findByUserId($userId);

        $firstName = trim((string) ($user->first_name ?? ''));
        $lastName = trim((string) ($user->last_name ?? ''));
        $displayName = trim((string) ($user->display_name ?? ''));
        if ($displayName === '') {
            $displayName = trim($firstName . ' ' . $lastName);
        }

        $isVerified = (bool) ($user->is_verified ?? false);

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'display_name' => trim((string) ($user->display_name ?? '')),
            'display-name-hint' => $displayName !== '' ? $displayName : 'Add your name',
            'email' => (string) ($user->email ?? ''),
            'email-input' => (string) ($user->email ?? ''),
            'phone' => (string) ($user->phone_number ?? ''),
            'company' => (string) ($customer['company_name'] ?? $customer['billing_company'] ?? ''),
            'designation' => (string) ($user->designation ?? ''),
            'street' => (string) ($customer['billing_address_1'] ?? $customer['address'] ?? ''),
            'suburb' => (string) ($customer['billing_city'] ?? ''),
            'state' => (string) ($customer['billing_region'] ?? ''),
            'postcode' => (string) ($customer['billing_post_code'] ?? ''),
            'verified-badge' => $isVerified ? 'Verified' : '',
            'notify_orders' => (bool) ($user->notify_orders ?? false),
            'notify_quotes' => (bool) ($user->notify_quotes ?? false),
            'notify_product_news' => (bool) ($user->subscribe ?? false),
        ];

        return $data;
    }
}
