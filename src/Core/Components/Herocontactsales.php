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
use function App\Core\System\utils\env;


class Herocontactsales extends ComponentBase {
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
            'name' => 'herocontactsales',
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

    function results() {
        // $results = [];
        
        // $results['title'] = "Contact Us";
        // $results['subtitle'] = "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.";
        // $results['image'] = "/img/about/about-hero.png";
        // $results['button_label_white'] = "Contact Sales";
        // $results['button_link'] = "/contact-sales";
        
        
        $component = $this->componentRepository->getComponentByName('herocontactsales');
       

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        
        // Extract data from component structure
        $results = [];
        
        // Map section_title to hero_title
        $results['title'] = $component->section_title ?? '';
        $results['subtitle'] = $component->section_subtitle ?? '';
        $results['banner_way_points'] = isset($component->banner_way_points) ? $component->banner_way_points : [];
        $results['buttons'] = isset($component->buttons) ? $component->buttons : [];
        
        $results['image'] = '';
        if (!empty($component->image)) {
            if (is_array($component->image)) {
                if (isset($component->image[0]['objectURL'])) {
                    $results['image'] = $component->image[0]['objectURL'];
                }
            } elseif (is_string($component->image)) {
                $results['image'] = $component->image;
            }
        }
        
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        return $results;
    }
}
