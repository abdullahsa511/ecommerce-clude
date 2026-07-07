<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\VendorRepositoryInterface;
use Illuminate\Container\Container;

class VendorSeeder
{
    private VendorRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data =
    [
        [
            'vendor_id' => 2,
            'admin_id' => 2,
            'name' => 'Global Innovations Co.',
            'slug' => 'global-innovations',
            'image' => 'global-innovations.jpg',
            'sort_order' => 2
        ],
        [
            'vendor_id' => 3,
            'admin_id' => 3,
            'name' => 'Eastern Manufacturing Ltd.',
            'slug' => 'eastern-manufacturing',
            'image' => 'eastern-mfg.jpg',
            'sort_order' => 3
        ],
        [
            'vendor_id' => 4,
            'admin_id' => 4,
            'name' => 'Nordic Solutions',
            'slug' => 'nordic-solutions',
            'image' => 'nordic-solutions.jpg',
            'sort_order' => 4
        ],
        [
            'vendor_id' => 5,
            'admin_id' => 5,
            'name' => 'Innovative Dynamics',
            'slug' => 'innovative-dynamics',
            'image' => 'innovative-dynamics.jpg',
            'sort_order' => 5
        ]
    ];
    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(VendorRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
} 