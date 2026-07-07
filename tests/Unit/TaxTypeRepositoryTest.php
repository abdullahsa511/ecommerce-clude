<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Tax\TaxType;
use App\Core\Repositories\Tax\TaxTypeRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class TaxTypeRepositoryTest extends TestCase
{
    private TaxTypeRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newTaxType = [
        "tax_type_id" => 4,
        "name" => "Tax Type 4",
        "content" => "Tax Type 4",
        "created_at" => "2024-02-01 00:00:00",
        "updated_at" => "2024-02-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxTypeRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newTaxType);
        $this->assertInstanceOf(TaxType::class, $result);
        $this->assertEquals($this->newTaxType['tax_type_id'], $result->tax_type_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newTaxType['tax_type_id']);
        $this->assertInstanceOf(TaxType::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Tax Type 1 Updated'];
        $result = $this->repository->update($this->newTaxType['tax_type_id'], $updateData);
        $this->assertInstanceOf(TaxType::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newTaxType['tax_type_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 