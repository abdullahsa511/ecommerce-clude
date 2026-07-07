<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\ReturnStatus;
use App\Core\Repositories\Order\ReturnStatusRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ReturnStatusRepositoryTest extends TestCase
{
    private ReturnStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newReturnStatus = [
        "return_status_id" => 4,
        "language_id" => 1,
        "name" => "Return Status 4",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ReturnStatusRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newReturnStatus);
        $this->assertInstanceOf(ReturnStatus::class, $result);
        $this->assertEquals($this->newReturnStatus['name'], $result->name);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newReturnStatus['return_status_id']);
        $this->assertInstanceOf(ReturnStatus::class, $result);
    }

    // public function testFindAll(): void
    // {
    //     $result = $this->repository->findAll();
    //     $this->assertIsArray($result);
    // }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Return Status 3 updated'];
        $result = $this->repository->update($this->newReturnStatus['return_status_id'], $updateData);
        $this->assertInstanceOf(ReturnStatus::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newReturnStatus['return_status_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 