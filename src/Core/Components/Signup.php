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
use App\Core\Repositories\Component\ComponentRepositoryInterface;

class Signup extends ComponentBase {
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
            'name' => 'signup',
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
        $results = [];
        // $results['image'] = "/img/login/login-img.png";
        // $results['section_title'] = "Sign up";
        // $results['section_description'] = "Create an account to access your account details, track the status of your orders and view saved specification lists.";
        // $results['name_input_label'] = "Full Name";
        // $results['name_input_placeholder'] = "Enter your name";
        // $results['email_input_label'] = "Enter Your Email";
        // $results['email_input_placeholder'] = "Enter your email";
        // $results['password_input_label'] = "Create A Password";
        // $results['password_input_placeholder'] = "Enter your password";
        // $results['submit_button_label'] = "Sign Up";
        // $results['form_fields'] = [
        //     [
        //         'type' => 'text',
        //         'label' => 'Full Name',
        //         'placeholder' => 'Enter your name'
        //     ],
        //     [
        //         'type' => 'email',
        //         'label' => 'Enter Your Email',
        //         'placeholder' => 'Enter your email'
        //     ],
        //     [
        //         'type' => 'password',
        //         'label' => 'Create A Password',
        //         'placeholder' => 'Enter your password'
        //     ],
        //     [
        //         'type' => 'submit',
        //         'label' => 'Sign Up'
        //     ]
        // ];

        $component = $this->componentRepository->getComponentByName('signup');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Extract data from component structure
        $results = [];
        
        // Map section_title to hero_title
        $results['section_title'] = $component->section_title ?? '';
        $results['section_description'] = $component->description ?? '';


        if (isset($component->image) && is_array($component->image) && isset($component->image[0]) && is_array($component->image[0])) {
            $imageData = $component->image[0];
            if (isset($imageData['objectURL'])) {
                $results['image'] = $imageData['objectURL'];
            } else {
                $results['image'] = '';
            }
        } else {
            $results['image'] = "/media/signup/signup.png";
        }
       

        return $results;


    }
}
