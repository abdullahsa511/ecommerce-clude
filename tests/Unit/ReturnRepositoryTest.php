<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\ReturnObject;
use App\Core\Repositories\Order\ReturnRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;


class ReturnRepositoryTest extends TestCase
{
    private ReturnRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newReturn = [
        "return_id" => 1,
        "order_id" => 1,
        "customer_id" => 1,
        "product_id" => 1,
        "model" => "Test",
        "quantity" => 1,
        "opened" => 1,
        "date_ordered" => "2021-01-01 00:00:00",
        "date_added" => "2021-01-01 00:00:00"
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ReturnRepositoryInterface::class);
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
    public function testCreate(){
        $result = $this->repository->create($this->newReturn);
        $this->assertInstanceOf(ReturnObject::class, $result);
    }
    public function testFind(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(ReturnObject::class, $result);
    }
    public function testFindAll(){
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }
    public function testUpdate(){
        $result = $this->repository->update($this->newReturn['return_id'], ['quantity' => 2]);
        $this->assertInstanceOf(ReturnObject::class, $result);
        $this->assertEquals(2, $result->quantity);
    }
    public function testDelete(){
        $result = $this->repository->delete($this->newReturn['return_id']);
        $this->assertTrue($result);
    }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 