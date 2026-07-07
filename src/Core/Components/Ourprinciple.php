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

class Ourprinciple extends ComponentBase {
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
            'name' => 'ourprinciple',
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
        $component = $this->componentRepository->getComponentByName('ourprinciple');
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
        
        // Extract items array with title and image for each item
        $results['items'] = [];
        
        if (isset($component->items) && is_array($component->items)) {
            foreach ($component->items as $itemIndex => $item) {
                $itemData = [];
                
                if (is_array($item)) {
                    foreach ($item as $field) {
                        if (isset($field['name']) && isset($field['value'])) {
                            $fieldName = $field['name'];
                            $fieldValue = $field['value'];
                            
                            switch ($fieldName) {
                                case 'number':
                                    $itemData['number'] = $fieldValue;
                                    break;
                                case 'title':
                                    $itemData['title'] = $fieldValue;
                                    break;
                                case 'description':
                                    $itemData['description'] = self::normalizeDescriptionHtml($fieldValue);
                                    break;
                            }
                        }
                    }
                }
                
                // Only add item if it has both title and image
                if (isset($itemData['number']) && isset($itemData['title']) && isset($itemData['description'])) {
                    $results['items'][] = $itemData;
                }
            }
        }

        $config = config('APP_ADMIN_URL');
        $results['component_link'] = $config."/components/{$component->component_id}/items";
        return $results;
    }

    /**
     * CMS HTML often uses &nbsp; between words. That removes normal wrap points, so the browser
     * may break inside words. Use regular spaces so wrapping happens between words.
     */
    private static function normalizeDescriptionHtml($value): string
    {
        $s = (string) $value;
        $s = preg_replace('/(?:&nbsp;|&#0*160;|&#x0*A0;)/i', ' ', $s);
        $s = str_replace("\xc2\xa0", ' ', $s);
        $s = preg_replace('/[ \t]+/u', ' ', str_replace(["\r\n", "\r", "\n", "\t"], ' ', trim($s)));

        return $s;
    }
}
