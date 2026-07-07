<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Post\PostStatusRepositoryInterface;
use Illuminate\Container\Container;

class PostStatusSeeder
{
    private PostStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["post_status_id" => 1, "language_id" => 1, "name" =>"Published"],
        ["post_status_id" => 2, "language_id" => 1, "name" =>"Future"],
        ["post_status_id" => 3, "language_id" => 1, "name" =>"Draft"],
        ["post_status_id" => 4, "language_id" => 1, "name" =>"Pending"],
        ["post_status_id" => 5, "language_id" => 1, "name" =>"Private"],
        ["post_status_id" => 6, "language_id" => 1, "name" =>"Trash"],
        ["post_status_id" => 7, "language_id" => 1, "name" =>"Auto-Draft"],
        ["post_status_id" => 8, "language_id" => 1, "name" =>"Approved"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(PostStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 