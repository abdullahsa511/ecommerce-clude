<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Order\ShippingStatusRepositoryInterface;
use Illuminate\Container\Container;

class ShippingStatusSeeder
{
    private ShippingStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["shipping_status_id" => 1, "language_id" => 1, "name" => "not_fulfilled"],
        ["shipping_status_id" => 2, "language_id" => 1, "name" => "fulfilled"],
        ["shipping_status_id" => 3, "language_id" => 1, "name" => "partially_fulfilled"],
        ["shipping_status_id" => 4, "language_id" => 1, "name" => "shipped"],
        ["shipping_status_id" => 5, "language_id" => 1, "name" => "partially_shipped"],
        ["shipping_status_id" => 6, "language_id" => 1, "name" => "returned"],
        ["shipping_status_id" => 7, "language_id" => 1, "name" => "partially_returned"],
        ["shipping_status_id" => 8, "language_id" => 1, "name" => "delivered"],
        ["shipping_status_id" => 9, "language_id" => 1, "name" => "canceled"],
        ["shipping_status_id" => 10, "language_id" => 1, "name" => "requires_action"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ShippingStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 