<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Tax\TaxTypeRepositoryInterface;
use Illuminate\Container\Container;

class TaxTypeSeeder
{
    private TaxTypeRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["tax_type_id" => 1,"name" => "Taxable Goods","content" => "Taxed goods","created_at"=>"2022-05-01 00:00:00","updated_at" => "2022-05-01 00:00:00"],
        ["tax_type_id" => 2,"name" => "Downloadable Products","content" => "Downloadable","created_at"=>"2022-05-01 00:00:00","updated_at" => "2022-05-01 00:00:00"],
        ["tax_type_id" => 3,"name" => "Test tax class","content" => "Downloadable","created_at"=>"2022-05-01 00:00:00","updated_at" => "2022-05-01 00:00:00"]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxTypeRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 