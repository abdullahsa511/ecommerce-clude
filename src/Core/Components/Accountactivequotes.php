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
use App\Core\Repositories\Order\OrderRepositoryInterface;
use App\Core\Repositories\Quote\QuoteRepositoryInterface;


class Accountactivequotes extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private TaxonomyItemRepositoryInterface $taxonomyRepository;
    private OrderRepositoryInterface $orderRepository;
    private QuoteRepositoryInterface $quoteRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        TaxonomyItemRepositoryInterface $taxonomyRepository,
        OrderRepositoryInterface $orderRepository,
        QuoteRepositoryInterface $quoteRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'accountactivequotes',
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
    
        $component = $this->componentRepository->getComponentByName('accountrecentorders');
        if(isset($component->items[0])){
            $item = $component->items[0];
            $params['model'] = $item['model'];
            $params['fields'] = $item['fields'];
            $params['item_count'] = $item['item_count'];
            if(isset($item['related_models'])){
                foreach($item['related_models'] as $joinModel){
                    $params['joins'][] = [ $joinModel['type'], $joinModel['source'], ' = ' ,$joinModel['model']];
                }
            }
        }

        $results = $this->quoteRepository->getCustomerQuotesForComponent($params); 
        // echo '<pre>';
        // print_r($results);
        // echo '</pre>';
        // exit;
        $results['component_link'] = "/components/{$component->component_id}/items";
        $results['active_quotes_link'] = "/account/active-quotes/";
        $results['options'] = $this->options;
        return $results;
    }
}
