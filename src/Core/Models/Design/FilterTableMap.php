<?php

declare(strict_types=1);

namespace App\Core\Models\Design;

final class FilterTableMap
{
    public const TABLE_MAP = [
        'post' => [
            'model' => 'post',
            'table' => 'post',
            'parent_key' => 'post_id',
            'child_key' => 'post_id',
            'media' => ['media', 'media.media_id', '=', 'post.media_id'],
            'join' => [
                ['post_to_taxonomy_item', 'post_to_taxonomy_item.post_id', '=', 'post.post_id'],
            ],
            'where' => ['post_to_taxonomy_item.taxonomy_item_id'],
            'select' => ['media.*'],
        ],
        'product' => [
            'model' => 'productImage',
            'table' => 'product_image',
            'parent_key' => 'product_id',
            'child_key' => 'product_image_id',
            'media' => ['media', 'media.media_id', '=', 'product_image.media_id'],
            // conditional join
            'join' => [
                ['product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product_image.product_id'],
            ],
            // conditional 
            'where' => ['product_to_taxonomy_item.taxonomy_item_id'],
            'select' => ['media.*'],
        ],
        'project' => [
            'model' => 'projectImage',
            'table' => 'project_image',
            'parent_key' => 'project_id',
            'child_key' => 'project_image_id',
            'media' => ['media', 'media.media_id', '=', 'project_image.media_id'],
            'join' => [
                ['project_to_taxonomy_item', 'project_to_taxonomy_item.project_id', '=', 'project.project_id'],
            ],
            'where' => ['project_to_taxonomy_item.taxonomy_item_id'],
            'select' => ['media.*'],
        ],
        'showrooms' => [
            'model' => 'showroom',
            'table' => 'showrooms',
            'parent_key' => 'showrooms_id',
            'child_key' => 'showrooms_id',
            'media' => ['media', 'media.media_id', '=', 'showrooms.media_id'],
            'select' => ['media.*'],
        ],
        'design_resource' => [ // NOT PERFECT, NEED TO BE FIXED
            'model' => 'design_resource',
            'table' => 'design_resource',
            'parent_key' => 'design_resource_id',
            'child_key' => 'design_resource_id',
            'media' => ['media', 'media.media_id', '=', 'design_resource.media_id'],
            'join' => [
                ['design_resource_to_taxonomy_item', 'design_resource_to_taxonomy_item.design_resource_id', '=', 'design_resource.design_resource_id'],
            ],
            'join_where' => ['design_resource_to_taxonomy_item.taxonomy_item_id'],
            'select' => ['media.*'],
        ],
    ];
}

//  design_resource_to_taxonomy_item
// 	post_to_taxonomy_item
// 	product_to_taxonomy_item
// 	project_to_taxonomy_item