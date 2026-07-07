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
use App\Core\Repositories\Project\ProjectRepositoryInterface;

use function App\Core\System\utils\env;

class Heroproject extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private ProjectRepositoryInterface $projectRepository;
    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ProjectRepositoryInterface $projectRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->projectRepository = $projectRepository;
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'heroproject',
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
        
        // $results['loadBtn'] = "Load More";

        // $results['hero_title'] = "2026 <span class=''>Catalogue</span> <span class='line-break'>Sent Out</span>";
        // $results['hero_description'] = "Krost is a leading manufacturer of high-quality kitchen and bathroom products. Our products are designed to meet the needs of modern living, with a focus on style, durability, and functionality.";
        // $results['hero_button_label_white'] = "Visit Our Showroom";
        // $results['hero_button_label_outline'] = "Contact Sales";
        // $results['hero_image'] = "/img/bg/home/hero_home.jpg";

        // return $results;
        $component = $this->componentRepository->getComponentByName('heroproject');
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        $component->options = $this->options;

        $projectId = $params['slug']?? null;
        if($projectId){
            $project = $this->projectRepository->getBySlug($projectId);
            if($project){
                $results['project_id'] = $project->project_id;
                $results['section_title'] = $project->title;
                $results['section_description'] = $project->description;
                $images = json_decode($project->image, true);
                $objectURL = $images[0]['objectURL'] ?? '';
                $results['image'] = json_decode($project->image, true);
                $results['objectURL'] = $objectURL;
                $results['keyline_quote'] = isset($project->keyline_quote) ? $project->keyline_quote : '';
                $results['preview_text'] = isset($project->preview_text) ? $project->preview_text : '';
                $results['way_points'] = $project->way_points;
                $results['project_link'] = "/projects"."/".$project->slug;
                $results['breadcrumbs'] = [
                    [
                        'title' => 'Home',
                        'link' => '/',
                    ],
                    [
                        'title' => 'Projects',
                        'link' => '/projects',
                    ],
                    [
                        'title' => $project->title
                    ],
                ];
            }
            // echo '<pre>';
            // print_r($results);
            // echo '</pre>';
            // exit;
            list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
            $component->options = $this->options;
            
            $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
            $results['edit_link'] = env('APP_ADMIN_URL')."/ecommerce/projects/edit/{$project->project_id}/general";

            return $results;
        }

    
        return null;
    }
}
