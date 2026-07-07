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
use App\Core\Repositories\Service\ServiceRequestRepositoryInterface;
use function App\Core\System\utils\env;


class Catalogueconfirmation extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ServiceRequestRepositoryInterface $serviceRequestRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ServiceRequestRepositoryInterface $serviceRequestRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->serviceRequestRepository = $serviceRequestRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'catalogueconfirmation',
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


        $component = $this->componentRepository->getComponentByName('catalogueconfirmation');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        $results = [];

        // buttons 
        $results['buttons'] = [];
        if (isset($component->buttons) && is_array($component->buttons) && count($component->buttons) > 0) {
            foreach ($component->buttons as $key => $button) {
                $results['buttons'][$key] = [
                    'title'  => $button['title'] ?? null,
                    'icon'   => $button['icon'] ?? null,
                    'link'   => $button['url'] ?? null,
                    'target' => $button['target'] ?? null,
                    'anchor_class'  => $key == 0 ? 'th-btn text-capitalize' : 'th-btn-outline text-capitalize',
                    'div_class'  => $key == 0 ? 'position-relative pb-3' : 'position-relative',
                ];
            }
        }

        $uuid = $params['uuid'] ?? '';
        // $component = $this->componentRepository->getComponentByName('catalogueconfirmation');
        $results = $this->serviceRequestRepository->getServiceRequestByUuid($uuid);


        // Map section_subtitle to hero_subtitle
        $results['hero_subtitle'] = $component->section_subtitle ?? '';
        $results['confirm_left_image'] = '';
        if (isset($component->image) && is_array($component->image) && count($component->image) > 0 && isset($component->image[0]) && is_array($component->image[0])) {
            $imageData = $component->image[0];
            if (isset($imageData['objectURL'])) {
                $results['confirm_left_image'] = $imageData['objectURL'];
            } else {
                $results['confirm_left_image'] = '';
            }
        }
        $results['hero_title'] = '';
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/overview";

        return $results;
    }
}
