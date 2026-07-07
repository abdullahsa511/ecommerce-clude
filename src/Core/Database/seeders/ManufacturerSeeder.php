<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\ManufacturerRepositoryInterface;
use Illuminate\Container\Container;

class ManufacturerSeeder
{
    private ManufacturerRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data =
    [
        [
            'manufacturer_id' => 2,
            'name' => 'Global Innovations Co.',
            'slug' => 'global-innovations',
            'image' => 'global-innovations.jpg',
            'sort_order' => 2
        ],
        [
            'manufacturer_id' => 3,
            'name' => 'Eastern Manufacturing Ltd.',
            'slug' => 'eastern-manufacturing',
            'image' => 'eastern-mfg.jpg',
            'sort_order' => 3
        ],
        [
            'manufacturer_id' => 4,
            'name' => 'Nordic Solutions',
            'slug' => 'nordic-solutions',
            'image' => 'nordic-solutions.jpg',
            'sort_order' => 4
        ],
        [
            'manufacturer_id' => 5,
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
        $this->repository = $this->container->make(ManufacturerRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
} 