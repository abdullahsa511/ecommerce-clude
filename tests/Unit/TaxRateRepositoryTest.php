<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Tax\TaxRate;
use App\Core\Repositories\Tax\TaxRateRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class TaxRateRepositoryTest extends TestCase
{
    private TaxRateRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newTaxRate = [
        "tax_rate_id" => 4,
        "region_group_id" => 1,
        "name" => "Tax Rate 4",
        "rate" => 10,
        "type" => "P",
        "created_at" => "2024-02-01 00:00:00",
        "updated_at" => "2024-02-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxRateRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newTaxRate);
        $this->assertInstanceOf(TaxRate::class, $result);
        $this->assertEquals($this->newTaxRate['tax_rate_id'], $result->tax_rate_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newTaxRate['tax_rate_id']);
        $this->assertInstanceOf(TaxRate::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Tax Rate 4 Updated'];
        $result = $this->repository->update($this->newTaxRate['tax_rate_id'], $updateData);
        $this->assertInstanceOf(TaxRate::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newTaxRate['tax_rate_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 