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

use App\Core\Repositories\Instagram\ProductInstagramRepositoryInterface;
use App\Core\System\Component\ComponentBase;

class Productinstagram extends ComponentBase
{
    public static $defaultOptions = [
        'site_id' => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0;

    private ProductInstagramRepositoryInterface $productInstagramRepository;

    public function __construct(
        ProductInstagramRepositoryInterface $productInstagramRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->productInstagramRepository = $productInstagramRepository;
    }

    public function cacheKey()
    {
        return false;
    }

    public static function getComponentMeta()
    {
        return [
            'name' => 'productinstagram',
            'class' => self::class,
            'validOptions' => [
                'component_id',
            ],
            'filePath' => __FILE__,
            'cacheKey' => null,
            'data' => [],
            'designOnly' => false,
        ];
    }

    public function results($params = [])
    {
        $slug = (string) ($params['slug'] ?? '');

        if ($slug === '') {
            return ['title' => '', 'items' => []];
        }

        return $this->productInstagramRepository->getPostsForProduct($slug);
    }
}
