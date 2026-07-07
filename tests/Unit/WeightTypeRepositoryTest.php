<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\WeightType;
use App\Core\Repositories\Product\WeightTypeRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class WeightTypeRepositoryTest extends TestCase
{
    private WeightTypeRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newWeightType = [
        "weight_type_id" => 3,
        "value" => 10.00,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(WeightTypeRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newWeightType);
        $this->assertInstanceOf(WeightType::class, $result);
        $this->assertEquals($this->newWeightType['value'], $result->value);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newWeightType['weight_type_id']);
        $this->assertInstanceOf(WeightType::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['value' => 11.00];
        $result = $this->repository->update($this->newWeightType['weight_type_id'], $updateData);
        $this->assertInstanceOf(WeightType::class, $result);
        $this->assertEquals($updateData['value'], $result->value);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newWeightType['weight_type_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 