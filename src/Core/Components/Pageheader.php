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
use App\Core\Repositories\Page\PageRepositoryInterface;
use App\Core\System\Component\ComponentBase;
use App\Core\System\Event;

use function App\Core\System\utils\env;


class Pageheader extends ComponentBase
{
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    private ComponentRepositoryInterface $componentRepository;
    private PageRepositoryInterface $pageRepository;

    // protected $componentRepository;
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        PageRepositoryInterface $pageRepository,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->pageRepository = $pageRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    function cacheKey()
    {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'pageheader',
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

    function results($params = [])
    {
        // $results = [];
        // $results = $this->categoryRepository->getHeaderMenu();
        // return $results;

        // $results = [
        //     'desktop_menu' => [
        //         [
        //             'title' => 'Products',
        //             'href' => '#',
        //             'class' => 'menu-item-has-children mega-menu-wrap desktop-menu-item',
        //             'has_children' => 1,
        //             'mega_menu' => 1,
        //             'rows' => [
        //                 [
        //                     [
        //                         'title' => 'Workstations',
        //                         'class' => 'sub-title',
        //                         'links' => [
        //                             [
        //                                 'title' => 'Workstations',
        //                                 'href' => '/categories/workstations',
        //                             ],
        //                             [
        //                                 'title' => 'Fixed Height Workstations',
        //                                 'href' => '/products/fixed-height-workstations',
        //                             ],
        //                             [
        //                                 'title' => 'Height Adjustable Workstations',
        //                                 'href' => '/products/height-adjustable-workstations',
        //                             ],
        //                         ],
        //                     ],
        //                     [
        //                         'title' => 'Desks',
        //                         'class' => 'sub-title',
        //                         'links' => [
        //                             [
        //                                 'title' => 'Desks',
        //                                 'href' => '/categories/desks',
        //                             ],
        //                             [
        //                                 'title' => 'Fixed Height Desks',
        //                                 'href' => '/products/desks-fixed-height',
        //                             ],
        //                             [
        //                                 'title' => 'Height Adjustable Desks',
        //                                 'href' => '/products/desks-height-adjustable',
        //                             ],
        //                             [
        //                                 'title' => 'Modesty Panels',
        //                                 'href' => '/products/desks-modesty-panels',
        //                             ],
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //             'view_all_text' => 'View all product Categories',
        //             'sidebar_images' => [
        //                 [
        //                     'src' => '/img/navbar-img/book-metting.png',
        //                     'alt' => 'boot-meetting',
        //                 ],
        //                 [
        //                     'src' => '/img/navbar-img/contact-sells.png',
        //                     'alt' => 'contact-sells',
        //                 ],
        //                 [
        //                     'src' => '/img/navbar-img/request-catalog.png',
        //                     'alt' => 'request-catalog',
        //                 ],
        //             ],
        //         ],
        //         [
        //             'title' => 'Projects',
        //             'href' => '/projects',
        //             'class' => 'desktop-menu-item',
        //             'has_children' => '',
        //         ],
        //         [
        //             'title' => 'Blog',
        //             'href' => '/blog',
        //             'class' => 'desktop-menu-item',
        //             'has_children' => '',
        //         ],
        //         [
        //             'title' => 'About',
        //             'href' => '/about',
        //             'class' => 'desktop-menu-item',
        //             'has_children' => '',
        //         ],
        //         [
        //             'title' => 'About',
        //             'href' => '/about',
        //             'class' => 'desktop-menu-item',
        //             'has_children' => '',
        //         ],
        //     ],
        // ];

        $results = [];
        $component = $this->componentRepository->getComponentByName('pageheader');
        if(isset($component->items[0])){
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
            $params['item_count'] = $item['item_count'];
            foreach($item['related_models'] as $joinModel){
                $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
            }
        }

        $results = $this->pageRepository->getPageHeaderForComponent($params);
        
        $results['component_link'] = env('APP_ADMIN_URL')."/components/{$component->component_id}/items";
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        // $results = $component->toArray();
        $results['options'] = $this->options;
        return $results;
    }
}
