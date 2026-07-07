<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\ShippingStatus;
use App\Core\Repositories\Order\ShippingStatusRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ShippingStatusRepositoryTest extends TestCase
{
    private ShippingStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newShippingStatus = [
        "shipping_status_id" => 4,
        "language_id" => 1,
        "name" => "Shipping Status 4",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ShippingStatusRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newShippingStatus);
        $this->assertInstanceOf(ShippingStatus::class, $result);
        $this->assertEquals($this->newShippingStatus['name'], $result->name);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newShippingStatus['shipping_status_id']);
        $this->assertInstanceOf(ShippingStatus::class, $result);
    }

    // public function testFindAll(): void
    // {
    //     $result = $this->repository->findAll();
    //     $this->assertIsArray($result);
    // }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Shipping Status 4 updated'];
        $result = $this->repository->update($this->newShippingStatus['shipping_status_id'], $updateData);
        $this->assertInstanceOf(ShippingStatus::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newShippingStatus['shipping_status_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 