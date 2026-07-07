<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductImage;
use Illuminate\Container\Container;

class ProductImageSeeder
{
    private ProductImage $model;
    private KernelCli $kernel;
    private Container $container;
    private $productImages = [
        // Product 1 images
        [
            'product_id' => 1,
            'image' => '{"objectURL": "/img/products/miro/miro-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 1,
            'image' => '{"objectURL": "/img/products/miro/miro-side.jpg"}',
            'sort_order' => 2
        ],
        // Product 2 images
        [
            'product_id' => 2,
            'image' => '{"objectURL": "/img/products/miro-s/miro-s-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 2,
            'image' => '{"objectURL": "/img/products/miro-s/miro-s-detail.jpg"}',
            'sort_order' => 2
        ],
        // Product 3 images
        [
            'product_id' => 3,
            'image' => '{"objectURL": "/img/products/kove/kove-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 3,
            'image' => '{"objectURL": "/img/products/kove/kove-back.jpg"}',
            'sort_order' => 2
        ],
        // Product 4 images
        // [
        //     'product_id' => 4,
        //     'image' => '{"objectURL": "/img/products/hana/hana-main.jpg"}',
        //     'sort_order' => 1
        // ],
        // [
        //     'product_id' => 4,
        //     'image' => '{"objectURL": "/img/products/hana/hana-closeup.jpg"}',
        //     'sort_order' => 2
        // ],
        // Product 5 images
        [
            'product_id' => 5,
            'image' => '{"objectURL": "/img/products/zak/zak-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 5,
            'image' => '{"objectURL": "/img/products/zak/zak-detail.jpg"}',
            'sort_order' => 2
        ],
        // Product 6 images
        [
            'product_id' => 6,
            'image' => '{"objectURL": "/img/products/sonic-task/sonic-task-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 6,
            'image' => '{"objectURL": "/img/products/sonic-task/sonic-task-armrest.jpg"}',
            'sort_order' => 2
        ],
        // Product 7 images
        [
            'product_id' => 7,
            'image' => '{"objectURL": "/img/products/sonic/sonic-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 7,
            'image' => '{"objectURL": "/img/products/sonic/sonic-back.jpg"}',
            'sort_order' => 2
        ],
        // Product 8 images
        [
            'product_id' => 8,
            'image' => '{"objectURL": "/img/products/space/space-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 8,
            'image' => '{"objectURL": "/img/products/space/space-detail.jpg"}',
            'sort_order' => 2
        ],
        // Product 9 images
        [
            'product_id' => 9,
            'image' => '{"objectURL": "/img/products/zed/zed-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 9,
            'image' => '{"objectURL": "/img/products/zed/zed-side.jpg"}',
            'sort_order' => 2
        ],
        // Product 10 images
        [
            'product_id' => 10,
            'image' => '{"objectURL": "/img/products/alex/alex-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 10,
            'image' => '{"objectURL": "/img/products/alex/alex-detail.jpg"}',
            'sort_order' => 2
        ],
        // Product 11 images
        [
            'product_id' => 11,
            'image' => '{"objectURL": "/img/products/leo/leo-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 11,
            'image' => '{"objectURL": "/img/products/leo/leo-armrest.jpg"}',
            'sort_order' => 2
        ],
        // Product 12 images
        [
            'product_id' => 12,
            'image' => '{"objectURL": "/img/products/aria/aria-main.jpg"}',
            'sort_order' => 1
        ],
        [
            'product_id' => 12,
            'image' => '{"objectURL": "/img/products/aria/aria-detail.jpg"}',
            'sort_order' => 2
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->model = new ProductImage();
        $this->model->setDb($this->container->make(\PDO::class));
    }

    public function seed(): void
    {
        $this->model->insert($this->productImages);
    }
    

} 