<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\ReturnReason;
use App\Core\Repositories\Order\ReturnReasonRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ReturnReasonRepositoryTest extends TestCase
{
    private ReturnReasonRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newReturnReason = [
        "return_reason_id" => 1, 
        "language_id" => 1, // Assuming language ID 1 exists
        "name" => "Test Reason"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ReturnReasonRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newReturnReason);
        $this->assertInstanceOf(ReturnReason::class, $result);
        $this->assertEquals($this->newReturnReason['language_id'], $result->language_id);
        $this->assertEquals($this->newReturnReason['name'], $result->name);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newReturnReason['return_reason_id']); 
        $this->assertInstanceOf(ReturnReason::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Updated Reason'];
        $result = $this->repository->update($this->newReturnReason['return_reason_id'], $updateData); // Assuming return_reason_id 1 and language_id 1 exist
        $this->assertInstanceOf(ReturnReason::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newReturnReason['return_reason_id']); // Assuming return_reason_id 1 and language_id 1 exist
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 