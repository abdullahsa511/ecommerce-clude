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
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Models\Pinboard\PinboardResponse;

class Bookingphonecall extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    private ProductRepositoryInterface $productRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        PinboardRepositoryInterface $pinboardRepository,
        ProductRepositoryInterface $productRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->pinboardRepository = $pinboardRepository;
        $this->productRepository = $productRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'bookingphonecall',
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
        $errors = isset($params['errors']) && is_array($params['errors']) ? $params['errors'] : [];

        // $results['section_title'] = 'Book a Phone Call';
        // $results['image'] = "/img/contact/member-avatar.png";
        // $results['name'] = "Devon Lane";
        // $results['member'] = "Superman";
        // $results['location'] = "Melbourne, Australia";
        // $results['tour_type'] = "Virtual Tour";
        // $results['calendar_title'] = "Time Zone";
        // $pinboardItems = $this->pinboardRepository->getPinboardFinalBookingComponent($params['uuid']);
        // $results['pinboard_items'] = $pinboardItems;
        // $results['teams'] = $pinboardItems['team_members'];


        $pinboardId = $this->pinboardRepository->getPinboardIdByUuid($params['uuid'] ?? '');
        if ($pinboardId == 0) {
            $results['pinboard_uuid'] = $params['uuid'] ?? '';
            $results['nonce'] = $params['nonce'] ?? '';
            $results['errors'] = $errors;
            $results['feedback_text'] = $this->flattenBookingPhoneCallErrors($errors);
            return $results;
        }

        $component = $this->componentRepository->getComponentByName('bookingphonecall');
        if($component && isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count'];
            $params['fields'] = $item['fields'];
        }

        $pinboard = $this->pinboardRepository->showPinboard($pinboardId);
        $pinboard = new PinboardResponse($pinboard->data);
        if (isset($pinboard->productIds) && count($pinboard->productIds) > 0) {
            $productTitles = $this->productRepository->getProductTitlesByProductIds($pinboard->productIds);
            foreach ($pinboard->pinboardItems as $index => $item) {
                if (($item['type'] ?? '') === 'product' && isset($item['model_id'])) {
                    $modelId = (int) $item['model_id'];
                    if (isset($productTitles[$modelId])) {
                        $pinboard->pinboardItems[$index]['title'] = $productTitles[$modelId];
                    }
                }
            }
        }

        $results['section_title'] = $component->section_title;
        $results['section_subtitle'] = $component->section_subtitle;
        $results['pinboard_name'] = $pinboard->pinboard_name;
        $results['pinboard_description'] = $pinboard->pinboard_description;
        if (!empty($pinboard->created_at)) {
            $timestamp = strtotime($pinboard->created_at);
            $results['pinboard_created_at'] = date('d M Y', $timestamp);
            $results['pinboard_created_at_time'] = date('h:i A', $timestamp);
        } else {
            $results['pinboard_created_at'] = '';
            $results['pinboard_created_at_time'] = '';
        }
   
        $results['pinboard_updated_at'] = $pinboard->updated_at;
        $results['pinboard_item_count'] = count($pinboard->pinboardItems ?? []);
        $results['pinboard_items'] = $pinboard->pinboardItems ?? [];
        $results['pinboard_uuid'] = $pinboard->uuid;
        $results['nonce'] = isset($params['nonce']) ? $params['nonce'] : '';
        $results['errors'] = $errors;
        $results['feedback_text'] = $this->flattenBookingPhoneCallErrors($errors);
        return $results;

    }

    private function flattenBookingPhoneCallErrors(array $errors): string
    {
        $parts = [];
        foreach (['general', 'pinboard_uuid', 'nonce'] as $key) {
            if (empty($errors[$key]) || !is_array($errors[$key])) {
                continue;
            }
            foreach ($errors[$key] as $msg) {
                if (is_string($msg) && $msg !== '') {
                    $parts[] = $msg;
                }
            }
        }
        return implode(' ', $parts);
    }
}
