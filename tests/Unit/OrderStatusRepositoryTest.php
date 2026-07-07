<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\OrderStatus;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Repositories\Order\OrderStatusRepositoryInterface;

class OrderStatusRepositoryTest extends TestCase
{
    private OrderStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newOrderStatus = [
        "order_status_id" => 8,
        "name" => "Test",
        "language_id" => 1
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OrderStatusRepositoryInterface::class);
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
        $result = $this->repository->create($this->newOrderStatus);
        $this->assertInstanceOf(OrderStatus::class, $result);
    }
    public function testFind(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(OrderStatus::class, $result);
    }
    public function testFindAll(){
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }
    public function testUpdate(){
        $result = $this->repository->update($this->newOrderStatus['order_status_id'], ['name' => 'Test Updated']);
        $this->assertInstanceOf(OrderStatus::class, $result);
        $this->assertEquals('Test Updated', $result->name);
    }
    public function testDelete(){
        $result = $this->repository->delete($this->newOrderStatus['order_status_id']);
        $this->assertTrue($result);
    }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 