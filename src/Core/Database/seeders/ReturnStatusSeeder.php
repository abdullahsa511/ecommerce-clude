<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Order\ReturnStatusRepositoryInterface;
use Illuminate\Container\Container;

class ReturnStatusSeeder
{
    private ReturnStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["payment_status_id" => 1, "language_id" => 1, "name" => "not_paid"],
        ["payment_status_id" => 2, "language_id" => 1, "name" => "awaiting"],
        ["payment_status_id" => 3, "language_id" => 1, "name" => "captured"],
        ["payment_status_id" => 4, "language_id" => 1, "name" => "paid"],
        ["payment_status_id" => 5, "language_id" => 1, "name" => "canceled"],
        ["payment_status_id" => 6, "language_id" => 1, "name" => "refunded"],
        ["payment_status_id" => 7, "language_id" => 1, "name" => "partially_refunded"],
        ["payment_status_id" => 8, "language_id" => 1, "name" => "chargeback"],
        ["payment_status_id" => 9, "language_id" => 1, "name" => "requires_action"],
        ["payment_status_id" => 10, "language_id" => 1, "name" => "fraud"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ReturnStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 