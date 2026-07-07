<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\Manufacturer;
use App\Core\Repositories\Product\ManufacturerRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ManufacturerRepositoryTest extends TestCase
{
    private ManufacturerRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newManufacturer = [
        "manufacturer_id" => 4,
        "name" => "Manufacturer 4",
        "slug" => "manufacturer-4",
        "image" => "image.jpg",
        "sort_order" => 4
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ManufacturerRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newManufacturer);
        $this->assertInstanceOf(Manufacturer::class, $result);
        $this->assertEquals($this->newManufacturer['name'], $result->name);
        $this->assertEquals($this->newManufacturer['slug'], $result->slug);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newManufacturer['manufacturer_id']);
        $this->assertInstanceOf(Manufacturer::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Updated Manufacturer'];
        $result = $this->repository->update($this->newManufacturer['manufacturer_id'], $updateData);
        $this->assertInstanceOf(Manufacturer::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newManufacturer['manufacturer_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 