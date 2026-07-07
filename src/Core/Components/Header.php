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

use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\Product\CategoryRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;
use function App\Core\System\utils\env;

class Header extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds


    protected $componentRepository;
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository, 
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        array $options = []
    )
    {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'header',
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
        $is_logged_in = isset($params['is_logged_in']) ? !!$params['is_logged_in'] : false;

        // echo '<pre>';
        // print_r($is_logged_in);
        // echo '</pre>';
        $baseUrl = env('APP_URL');

        $component = $this->componentRepository->getComponentByName('header');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results = [];

        $results = $this->categoryRepository->getHeaderMenu($is_logged_in);

        if (isset($component->items) && is_array($component->items) && count($component->items) > 0) {
            $items = $component->items[0]; // Get the first array of items
            
            foreach ($items as $item) {
                if (isset($item['name']) && isset($item['value'])) {
                    $fieldName = $item['name'];
                    $fieldValue = $item['value'];
                    
                    // Map specific field names to the expected format
                    switch ($fieldName) {
                        case 'section_title':
                            $results['section_title'] = $fieldValue;
                            break;
                        case 'hero_description':
                            $results['section_subtitle'] = $fieldValue;
                            break;
                        default:
                            // For any other fields, use the name as the key
                            $results[$fieldName] = $fieldValue;
                            break;
                    }
                }
            }
        }

        $results['topbar_message'] = $component->section_subtitle ?? '';
        $results['topbar_link'] = $baseUrl . '/catalogue';
        $results['topbar_link_text'] = $component->section_title ?? '';
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        
        // $results['top_header_middle_text'] = $component->top_header_middle_text ?? '';
        // $results['top_header_middle_text_link'] = $component->top_header_middle_text_link ?? '';
        // $results['top_header_right_text'] = $component->top_header_right_text ?? '';
        // $results['Top_header_right_text_link'] = $component->Top_header_right_text_link ?? '';

        // echo '<pre>';
        // print_r($results['Top_header_right_text_link']);
        // echo '</pre>';


        // 1. Make middle text and link - 
        //     top_header_middle_text 
        //     top_header_middle_text_link
        // 2. Make right text and link -
        //         top_header_right_text 
        // Top_header_right_text_link
    

        return $results;
        
    }
}
