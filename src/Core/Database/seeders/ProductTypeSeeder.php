<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\ProductReviewRepositoryInterface;
use App\Core\Repositories\Product\ProductTypeRepositoryInterface;
use Illuminate\Container\Container;

class ProductTypeSeeder
{
    private ProductTypeRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $productTypes = [
      [
        'name' => 'Item',
        'type' => 'Item',
        'plural' => "Items"
      ],
      [
        'name' => 'Component',
        'type' => 'Component',
        'plural' => "Components"
      ],
      [
        'name' => 'Kit',
        'type' => 'Kit',
        'plural' => "Kits"
      ],
      [
        'name' => 'Service',
        'type' => 'Service',
        'plural' => "Services"
      ],
      [
        'name' => 'Accessory',
        'type' => 'Accessory',
        'plural' => "Accessories"
      ],
      [
        'name' => 'Other',
        'type' => 'Other',
        'plural' => "Others"
      ]
    ];


    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductTypeRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->productTypes);
    }
    

} 