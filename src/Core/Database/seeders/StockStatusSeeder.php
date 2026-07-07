<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\StockStatusRepositoryInterface;
use Illuminate\Container\Container;

class StockStatusSeeder
{
    private StockStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["stock_status_id" => 1, "language_id" => 1, "name" =>  "In Stock"],
        ["stock_status_id" => 2, "language_id" => 1, "name" =>  "Pre-Order"],
        ["stock_status_id" => 3, "language_id" => 1, "name" =>  "Out Of Stock"],
        ["stock_status_id" => 4, "language_id" => 1, "name" =>  "2-3 Days"],
        ["stock_status_id" => 5, "language_id" => 1, "name" =>  "1 Week"],
        ["stock_status_id" => 6, "language_id" => 1, "name" =>  "1 Month"],
        ["stock_status_id" => 7, "language_id" => 1, "name" =>  "1 Year"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(StockStatusRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 