<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\Vendor;
use App\Core\Repositories\Product\VendorRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class VendorRepositoryTest extends TestCase
{
    private VendorRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newVendor = [
        "vendor_id" => 2,
        "admin_id" => 1,
        "name" => "Vendor 2",
        "slug" => "vendor-2",
        "image" => "vendor-2.jpg",
        "sort_order" => 2
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(VendorRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newVendor);
        $this->assertInstanceOf(Vendor::class, $result);
        $this->assertEquals($this->newVendor['vendor_id'], $result->vendor_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newVendor['vendor_id']);
        $this->assertInstanceOf(Vendor::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Vendor 2 Updated'];
        $result = $this->repository->update($this->newVendor['vendor_id'], $updateData);
        $this->assertInstanceOf(Vendor::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newVendor['vendor_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 