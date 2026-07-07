<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\OrderLog;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Repositories\Order\OrderLogRepositoryInterface;

class OrderLogRepositoryTest extends TestCase
{
    private OrderLogRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newOrderLog = [
        "order_log_id" => 1,
        "order_id" => 10,
        "order_status_id" => 1,
        "notify" => 1,
        "public" => 0,
        "note" => "",
        "created_at" => "2021-01-01 00:00:00"
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OrderLogRepositoryInterface::class);
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
        $result = $this->repository->create($this->newOrderLog);
        $this->assertInstanceOf(OrderLog::class, $result);
    }
    public function testFind(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(OrderLog::class, $result);
    }
    public function testFindAll(){
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }
    public function testUpdate(){
        $result = $this->repository->update($this->newOrderLog['order_log_id'], ['note' => 'Test']);
        $this->assertInstanceOf(OrderLog::class, $result);
        $this->assertEquals('Test', $result->note);
    }
    // public function testDelete(){
    //     $result = $this->repository->delete($this->newOrderLog['order_log_id']);
    //     $this->assertTrue($result);
    // }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 