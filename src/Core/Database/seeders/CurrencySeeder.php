<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Localisation\CurrencyRepositoryInterface;
use Illuminate\Container\Container;

class CurrencySeeder
{
    private CurrencyRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        ["currency_id" => 1,"name" => "US Dollar","code" => "USD", "value" => 1.00000000,"sign_start" =>"$","sign_end" => "","decimal_place" => 2,"status" => 1,"updated_at" => "2023-01-10 10:40:59"],
        ["currency_id" => 2,"name" => "Euro","code" => "EUR", "value" => 0.93492895,"sign_start" =>"","sign_end" => "€","decimal_place" => 2,"status" => 1,"updated_at" => "2023-01-10 10:40:59"],
        ["currency_id" => 3,"name" => "Chinese Yuan Renminbi","code" => "CNY", "value" => 6.78253553,"sign_start" =>"¥","sign_end" => "","decimal_place" => 2,"status" => 0,"updated_at" => "2023-01-10 10:40:59"],
        ["currency_id" => 4,"name" => "Indian Rupee","code" => "INR", "value" => 82.34667165,"sign_start" =>"₹","sign_end" => "","decimal_place" => 2,"status" => 0,"updated_at" => "2023-01-10 10:40:59"],
        ["currency_id" => 5,"name" => "Russian Ruble","code" => "RUB", "value" => 56.40360000,"sign_start" =>"","sign_end" => "₽","decimal_place" => 2,"status" => 0,"updated_at" => "2023-01-10 10:40:58"],
        ["currency_id" => 6,"name" => "Romanian Leu","code" => "RON", "value" => 5.00000000,"sign_start" =>"","sign_end" => "RON","decimal_place" => 2,"status" => 0,"updated_at" => "2023-01-10 10:40:58"],
        ["currency_id" => 7,"name" => "Pound Sterling","code" => "GBP", "value" => 0.82318624,"sign_start" =>"£","sign_end" => "","decimal_place" => 2,"status" => 0,"updated_at" => "2023-01-10 10:40:59"],
        ["currency_id" => 8,"name" => "Australian Dollar","code" => "AUD", "value" => 1.44409125,"sign_start" =>"$","sign_end" => "","decimal_place" => 2,"status" => 0,"updated_at" => "2023-01-10 10:40:59"]
        

    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(CurrencyRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
    

} 