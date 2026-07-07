<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\StockStatus;
use App\Core\Repositories\Product\StockStatusRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class StockStatusRepositoryTest extends TestCase
{
    private StockStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newStockStatus = [
        "stock_status_id" => 5,
        "language_id" => 1,
        "name" => "Stock Status 5",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(StockStatusRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newStockStatus);
        $this->assertInstanceOf(StockStatus::class, $result);
        $this->assertEquals($this->newStockStatus['name'], $result->name);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newStockStatus['stock_status_id']);
        $this->assertInstanceOf(StockStatus::class, $result);
    }

    // public function testFindAll(): void
    // {
    //     $result = $this->repository->findAll();
    //     $this->assertIsArray($result);
    // }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Stock Status 4 updated'];
        $result = $this->repository->update($this->newStockStatus['stock_status_id'], $updateData);
        $this->assertInstanceOf(StockStatus::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newStockStatus['stock_status_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 