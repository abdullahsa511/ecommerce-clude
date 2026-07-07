<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Localisation\Currency;
use App\Core\Repositories\Localisation\CurrencyRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;


class CurrencyRepositoryTest extends TestCase
{
    private CurrencyRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newCurrency = [
        "currency_id" => 9,
        "name" => "Test Currency",
        "code" => "TES", 
        "value" => 1.00000000,
        "sign_start" =>"$",
        "sign_end" => "",
        "decimal_place" => 2,
        "status" => 1,
        "updated_at" => "2023-01-10 10:40:59"
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(CurrencyRepositoryInterface::class);
    }
    // public function testGetAll(): void
    // {
    //     $result = $this->repository->getAll();
        
    //     $this->assertIsArray($result);
    //     $this->assertInstanceOf(Collection::class, $result['items']);
    //     $this->assertIsInt($result['total']);
    //     $this->assertIsInt($result['total_pages']);
    //     $this->assertIsInt($result['current_page']);
    //     $this->assertIsInt($result['per_page']);
    // }
    // public function testFind(){
    //     $result = $this->repository->find(1);
    //     $this->assertInstanceOf(Currency::class, $result);
    // }
    // public function testFindAll(){
    //     $result = $this->repository->findAll();
    //     $this->assertIsArray($result);
    // }
    // public function testCreate(){
    //     $result = $this->repository->create($this->newCurrency);
    //     $this->assertInstanceOf(Currency::class, $result);
    // }
    // public function testUpdate(){
    //     $result = $this->repository->update($this->newCurrency['currency_id'], ['name' => 'TE']);
    //     $this->assertInstanceOf(Currency::class, $result);
    //     $this->assertEquals('TE', $result->name);
    // }
    public function testDelete(){
        $result = $this->repository->delete($this->newCurrency['currency_id']);
        $this->assertTrue($result);
    }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 