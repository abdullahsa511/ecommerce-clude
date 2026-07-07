<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductRelated;

use Illuminate\Container\Container;

class ProductRelatedSeeder
{
    private ProductRelated $model;
    private KernelCli $kernel;
    private Container $container;
    private $productRelateds = [
        // Product 1 related to products 2, 3, 4
        [
            'product_id' => 1,
            'product_related_id' => 2
        ],
        [
            'product_id' => 1,
            'product_related_id' => 3
        ],
        // [
        //     'product_id' => 1,
        //     'product_related_id' => 4
        // ],
        // Product 2 related to products 1, 3, 5
        [
            'product_id' => 2,
            'product_related_id' => 1
        ],
        [
            'product_id' => 2,
            'product_related_id' => 3
        ],
        [
            'product_id' => 2,
            'product_related_id' => 5
        ],
        // Product 3 related to products 1, 2, 6
        [
            'product_id' => 3,
            'product_related_id' => 1
        ],
        [
            'product_id' => 3,
            'product_related_id' => 2
        ],
        [
            'product_id' => 3,
            'product_related_id' => 6
        ],
        // Product 4 related to products 1, 5, 7
        // [
        //     'product_id' => 4,
        //     'product_related_id' => 1
        // ],
        // [
        //     'product_id' => 4,
        //     'product_related_id' => 5
        // ],
        // [
        //     'product_id' => 4,
        //     'product_related_id' => 7
        // ],
        // Product 5 related to products 2, 4, 8
        [
            'product_id' => 5,
            'product_related_id' => 2
        ],
        // [
        //     'product_id' => 5,
        //     'product_related_id' => 4
        // ],
        [
            'product_id' => 5,
            'product_related_id' => 8
        ],
        // Product 6 related to products 3, 7, 9
        [
            'product_id' => 6,
            'product_related_id' => 3
        ],
        [
            'product_id' => 6,
            'product_related_id' => 7
        ],
        [
            'product_id' => 6,
            'product_related_id' => 9
        ],
        // Product 7 related to products 4, 6, 10
        // [
        //     'product_id' => 7,
        //     'product_related_id' => 4
        // ],
        [
            'product_id' => 7,
            'product_related_id' => 6
        ],
        [
            'product_id' => 7,
            'product_related_id' => 10
        ],
        // Product 8 related to products 5, 9, 11
        [
            'product_id' => 8,
            'product_related_id' => 5
        ],
        [
            'product_id' => 8,
            'product_related_id' => 9
        ],
        [
            'product_id' => 8,
            'product_related_id' => 11
        ],
        // Product 9 related to products 6, 8, 12
        [
            'product_id' => 9,
            'product_related_id' => 6
        ],
        [
            'product_id' => 9,
            'product_related_id' => 8
        ],
        [
            'product_id' => 9,
            'product_related_id' => 12
        ],
        // Product 10 related to products 7, 11, 1
        [
            'product_id' => 10,
            'product_related_id' => 7
        ],
        [
            'product_id' => 10,
            'product_related_id' => 11
        ],
        [
            'product_id' => 10,
            'product_related_id' => 1
        ],
        // Product 11 related to products 8, 10, 2
        [
            'product_id' => 11,
            'product_related_id' => 8
        ],
        [
            'product_id' => 11,
            'product_related_id' => 10
        ],
        [
            'product_id' => 11,
            'product_related_id' => 2
        ],
        // Product 12 related to products 9, 1, 3
        [
            'product_id' => 12,
            'product_related_id' => 9
        ],
        [
            'product_id' => 12,
            'product_related_id' => 1
        ],
        [
            'product_id' => 12,
            'product_related_id' => 3
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->model = new ProductRelated();
        $this->model->setDb($this->container->make(\PDO::class));
    }

    public function seed(): void
    {
        $this->model->insert($this->productRelateds);
    }
    

} 