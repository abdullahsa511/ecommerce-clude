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
use App\Core\Repositories\Post\PostRepositoryInterface;

class Bloggallery extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private PostRepositoryInterface $postRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PostRepositoryInterface $postRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->postRepository = $postRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'bloggallery',
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
        $component = $this->componentRepository->getComponentByName('bloggallery');
        if(isset($component->items[0])) {
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['item_count'] = $item['item_count']??14;
            $params['fields'] = $item['fields'];
            if(isset($item['related_models']) && is_array($item['related_models'])){
                foreach($item['related_models'] as $joinModel){
                    $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
                }
            }
        }
        $results = $this->postRepository->getBlogGalleryComponentData($params);

        // Generate alt text for blog gallery images based on filename in 'image' path if not explicitly set
	if (isset($results['items']) && is_array($results['items'])) {
		foreach ($results['items'] as $idx => $item) {
			// Only set alt_text if not already set or empty
			
            $imagePath = $item['image'];
            // Get filename (without extension)
            $imageName = pathinfo($imagePath, PATHINFO_FILENAME);

            // Remove any trailing _number (e.g. _1, _2)
            $imageName = preg_replace('/_\d+$/', '', $imageName);

            // Replace hyphens/underscores with spaces
            $altText = str_replace(['-', '_'], ' ', $imageName);

            // Optionally capitalize words
            $altText = ucwords($altText);

            if(isset($params['blog_title'])) $altText = $params['blog_title'] . " - " . $altText ;

            $results['items'][$idx]['alt_text'] = $altText;
		}
	}
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $results['options'] = $this->options;
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        return $results;

    }
}
