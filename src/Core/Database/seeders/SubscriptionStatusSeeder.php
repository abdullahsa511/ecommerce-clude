<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Status\SubscriptionStatusRepositoryInterface;
use Illuminate\Container\Container;

class SubscriptionStatusSeeder
{
    private SubscriptionStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["subscription_status_id" => 1, "language_id" => 1, "name" => "Pending"],
        ["subscription_status_id" => 1, "language_id" => 2, "name" => "Pending"],
        ["subscription_status_id" => 2, "language_id" => 1, "name" => "Active"],
        ["subscription_status_id" => 2, "language_id" => 2, "name" => "Active"],
        ["subscription_status_id" => 3, "language_id" => 1, "name" => "Failed"],
        ["subscription_status_id" => 3, "language_id" => 2, "name" => "Failed"],
        ["subscription_status_id" => 4, "language_id" => 1, "name" => "Cancelled"],
        ["subscription_status_id" => 4, "language_id" => 2, "name" => "Cancelled"],
        ["subscription_status_id" => 5, "language_id" => 1, "name" => "Denied"],
        ["subscription_status_id" => 5, "language_id" => 2, "name" => "Denied"],
        ["subscription_status_id" => 6, "language_id" => 1, "name" => "Expired"],
        ["subscription_status_id" => 6, "language_id" => 2, "name" => "Expired"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(SubscriptionStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 