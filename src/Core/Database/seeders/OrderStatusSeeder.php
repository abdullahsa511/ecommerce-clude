<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Order\OrderStatusRepositoryInterface;
use Illuminate\Container\Container;

class OrderStatusSeeder
{
    private OrderStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["order_status_id" => 1, "language_id" => 1, "name" =>"pending"],
        ["order_status_id" => 2, "language_id" => 1, "name" =>"processing"],
        ["order_status_id" => 3, "language_id" => 1, "name" =>"processed"],
        ["order_status_id" => 4, "language_id" => 1, "name" =>"complete"],
        ["order_status_id" => 5, "language_id" => 1, "name" =>"canceled"],
        ["order_status_id" => 6, "language_id" => 1, "name" =>"archived"],
        ["order_status_id" => 7, "language_id" => 1, "name" =>"requires_action"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OrderStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 