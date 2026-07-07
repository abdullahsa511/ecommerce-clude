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
use function App\Core\System\utils\config;

class Herohome extends ComponentBase {
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
            'name' => 'herohome',
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
        // $results = [];
        
        // $results['loadBtn'] = "Load More";

        // $results['hero_title'] = "2026 <span class=''>Catalogue</span> <span class='line-break'>Sent Out</span>";
        // $results['hero_description'] = "Krost is a leading manufacturer of high-quality kitchen and bathroom products. Our products are designed to meet the needs of modern living, with a focus on style, durability, and functionality.";
        // $results['hero_button_label_white'] = "Visit Our Showroom";
        // $results['hero_button_label_outline'] = "Contact Sales";
        // $results['hero_image'] = "/img/bg/home/hero_home.jpg";

        $component = $this->componentRepository->getComponentByName('herohome');
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;
        $config = config('APP_ADMIN_URL');
        $componentArray = $component->toArray();
        $componentArray['component_link'] = $config."/components/{$component->component_id}/items";
        
        return $componentArray;
    }
}
