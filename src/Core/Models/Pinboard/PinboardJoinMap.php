<?php

declare(strict_types=1);

namespace App\Core\Models\Pinboard;

final class PinboardJoinMap
{
    public const TABLE_MAP = [
        'post' => [
            'table' => 'post',
            'pk' => 'post_id',
        ],
        'product' => [
            'table' => 'product',
            'pk' => 'product_id',
        ],

        'project' => [
            'table' => 'project',
            'pk' => 'project_id',
        ],
        'showrooms' => [
            'table' => 'showrooms',
            'pk' => 'showrooms_id',
        ],
    ];
}
