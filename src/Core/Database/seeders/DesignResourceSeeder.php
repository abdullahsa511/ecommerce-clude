<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;

use Illuminate\Container\Container;

class DesignResourceSeeder
{
    private DesignResourceRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $designResources = [
        [
            'design_resource_id' => 1,
            'img' => '{"objectURL": "/img/design-resources/finishes/access.png"}',
            'title' => 'Modern Office Design',
            'description' => 'Contemporary office design featuring open-plan layouts, natural lighting, and collaborative workspaces for enhanced productivity and employee satisfaction.'
        ],
        [
            'design_resource_id' => 2,
            'img' => '{"objectURL": "/img/design-resources/finishes/amethyst.png"}',
            'title' => 'Luxury Residential Interior',
            'description' => 'Premium residential interior design showcasing sophisticated color palettes, high-end materials, and elegant furnishings for discerning homeowners.'
        ],
        [
            'design_resource_id' => 3,
            'img' => '{"objectURL": "/img/design-resources/finishes/ass.png"}',
            'title' => 'Retail Space Layout',
            'description' => 'Strategic retail space design optimizing customer flow, product display, and shopping experience to maximize sales and customer engagement.'
        ],
        [
            'design_resource_id' => 4,
            'img' => '{"objectURL": "/img/design-resources/textiles/textile-1.png"}',
            'title' => 'Healthcare Facility Design',
            'description' => 'Patient-centered healthcare facility design prioritizing comfort, accessibility, and efficient medical workflows for optimal care delivery.'
        ],
        [
            'design_resource_id' => 5,
            'img' => '{"objectURL": "/img/design-resources/textiles/textile-2.png"}',
            'title' => 'Educational Campus Planning',
            'description' => 'Comprehensive educational campus design integrating modern learning environments, research facilities, and collaborative spaces for academic excellence.'
        ],
        [
            'design_resource_id' => 6,
            'img' => '{"objectURL": "/img/design-resources/textiles/textile-3.png"}',
            'title' => 'Industrial Warehouse Layout',
            'description' => 'Efficient industrial warehouse design optimizing storage capacity, logistics flow, and operational efficiency for manufacturing and distribution.'
        ]
    ]; 

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(DesignResourceRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertDesignResources(['designResources' => $this->designResources]);
    }
    

} 