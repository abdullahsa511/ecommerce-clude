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

use function App\Core\System\utils\env;
use function App\Core\System\utils\publicUrlPath;
use function App\Core\System\utils\siteSettings;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;

class Productsustainablity extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ProductRepositoryInterface $productRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProductRepositoryInterface $productRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->productRepository = $productRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'productsustainablity',
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
        $product = $this->productRepository->getByProductCode($params['slug']);
        $component = $this->componentRepository->getComponentByName('productsustainablity');
        $component->items = array_map(function($item) {
            return $item['fields'];
        }, $component->items);

        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        // Extract data from component structure
        $results = [];
        
        // Map section_title and section_subtitle
        $results['section_title'] = $component->section_title ?? '';
        $results['section_subtitle'] = $component->section_subtitle ?? '';
                
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                
                if (is_array($item)) {
                    foreach ($item as $field) {
                        if (isset($field['name']) && isset($field['value'])) {
                            $fieldName = $field['name'];
                            $fieldValue = $field['value'];
                            
                            switch ($fieldName) {
                                case 'image1':
                                    $itemData['image1'] = $fieldValue;
                                    break;
                                case 'section_title':
                                    $itemData['section_title'] = $fieldValue;
                                    break;
                                case 'section_subtitle':
                                    $itemData['section_subtitle'] = $fieldValue;
                                    break;
                                case 'section_link_text':
                                    $itemData['section_link_text'] = $fieldValue;
                                    break;
                                case 'image2':
                                    $itemData['image2'] = $fieldValue;
                                    break;
                            }
                        }
                    }
                }
                
                // Only add item if it has both title and image
                if (isset($itemData['image1']) && isset($itemData['section_title']) && isset($itemData['section_subtitle']) && isset($itemData['section_link_text']) && isset($itemData['image2'])) {
                    $results['img'] = $this->getImagePath($itemData['image1']);
                    $results['img2'] = $this->getImagePath($itemData['image2']);
                    $results['title'] = $itemData['section_title'];
                    $results['subtitle'] = $itemData['section_subtitle'];
                    $results['linkText'] = $itemData['section_link_text'];
                    $results['ocean_plastic_used'] = isset($product->ocean_plastic_used) ? $product->ocean_plastic_used : 0;
                    $results['edit_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
                    $imageThumb = isset($product->image_thumb) && !empty($product->image_thumb) ? json_decode($product->image_thumb, true) : [];
                    if(isset($imageThumb[0])) {
                        $results['img2'] = $this->getImagePath($imageThumb[0]['objectURL']);
                    }
                }
            }
        }
        
        return $results;
    }

    private function getImagePath($raw) {
        $imagePath = $raw;
        if (is_string($raw)) {
            if (preg_match("/objectURL\\s*:\\s*'([^']+)'/", $raw, $m) || preg_match('/objectURL\\s*:\\s*\"([^\"]+)\"/', $raw, $m)) {
                $imagePath = $m[1];
            }
        }
        $imagePath = str_replace('\\', '/', $imagePath);
        $basePath = dirname($imagePath);
        $imagePath = $basePath . '/' . basename($imagePath);
        return $imagePath;
    }
}
