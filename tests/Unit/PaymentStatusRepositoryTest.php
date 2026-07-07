<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\PaymentStatus;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Repositories\Status\PaymentStatusRepositoryInterface;

class PaymentStatusRepositoryTest extends TestCase
{
    private PaymentStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newPaymentStatus = [
        "payment_status_id" => 1,
        "name" => "Test",
        "language_id" => 1
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(PaymentStatusRepositoryInterface::class);
    }

    public function testCreate(){
        $result = $this->repository->create($this->newPaymentStatus);
        $this->assertInstanceOf(PaymentStatus::class, $result);
    }
    public function testFind(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(PaymentStatus::class, $result);
    }
    public function testFindAll(){
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }
    public function testUpdate(){
        $result = $this->repository->update($this->newPaymentStatus['payment_status_id'], ['name' => 'Test Updated']);
        $this->assertInstanceOf(PaymentStatus::class, $result);
        $this->assertEquals('Test Updated', $result->name);
    }
    public function testDelete(){
        $result = $this->repository->delete($this->newPaymentStatus['payment_status_id']);
        $this->assertTrue($result);
    }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 