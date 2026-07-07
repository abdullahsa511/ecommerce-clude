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
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;


class Accountvirtualpinboard extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private TaxonomyItemRepositoryInterface $taxonomyRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;


    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        TaxonomyItemRepositoryInterface $taxonomyRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'accountvirtualpinboard',
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
        
        // $component = $this->componentRepository->getComponentByName('accountpinboard');
        // if(isset($component->items[0])){
        //     $item = $component->items[0];
        //     $params['model'] = $item['model'];
        //     $params['fields'] = $item['fields'];
        //     $params['item_count'] = $item['item_count'];
        //     foreach($item['related_models'] as $joinModel){
        //         $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
        //     }
        // }

        $results = $this->pinboardItemRepository->getPinboard(null,$params['pinboard_id']); 

        // Results has pinboard_items array
        // Need to add photo property to each item
        $imageFieldMap = [
            'post' => ['post_image', 'post_title'],
            'product' => ['product_image', 'product_image'],
            'project' => ['project_image','project_title'],
            'media' => ['media_image','media_title'],
            'comment' => ['comment_image','comment_title'],
        ];

        // Process each item to set photo based on model_type
        if (isset($results['pinboard_items']) && is_array($results['pinboard_items'])) {
            $results['pinboard_items'] = array_map(function($item) use ($imageFieldMap) {
                // Check if item.model exists or model_type is at top level
                $modelType = $item['model']['model_type'] ?? $item['model_type'] ?? null;
                
                if ($modelType && isset($imageFieldMap[$modelType])) {
                    $fields = $imageFieldMap[$modelType];
                    foreach($fields as $imageField){
                        if(str_contains($imageField, 'image')){
                            $imageArray = $item[$imageField] ?? $item['model'][$imageField] ?? null;
                            
                            // If image array exists and has at least one element
                            if (is_array($imageArray) && count($imageArray) > 0) {
                                $firstImage = $imageArray[0];
                                // Use objectURL if available, otherwise fallback to other image properties
                                if (isset($firstImage['objectURL'])) {
                                    $item['photo'] = $firstImage['objectURL'];
                                }
                            }
                        }
                        if(str_contains($imageField, 'title')){
                            $item['title'] = $item['model'][$imageField] ?? null;
                        }
                    }
                }
                
                return $item;
            }, $results['pinboard_items']);
        }
        
        return $results;

        // $component = $this->componentRepository->getComponentByName('categoriesmasonry');
        // $component->items = $this->taxonomyRepository->getTaxonomyItems(1,['taxonomy_item.taxonomy_item_id','taxonomy_item.image','taxonomy_item_content.name', 'taxonomy_item_content.content as description']);
        // list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        // $component->options = $this->options;
        // return $component->toArray();
    }
}
