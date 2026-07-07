<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Project\ProjectStatusRepositoryInterface;
use Illuminate\Container\Container;

class ProjectStatusSeeder
{
    private ProjectStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["project_status_id" => 1, "language_id" => 1, "name" =>"Pending"],
        ["project_status_id" => 2, "language_id" => 1, "name" =>"Processing"],
        ["project_status_id" => 3, "language_id" => 1, "name" =>"Processed"],
        ["project_status_id" => 4, "language_id" => 1, "name" =>"Completed"],
        ["project_status_id" => 5, "language_id" => 1, "name" =>"Canceled"],
        ["project_status_id" => 6, "language_id" => 1, "name" =>"Archived"],
        ["project_status_id" => 7, "language_id" => 1, "name" =>"Requires Action"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProjectStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 