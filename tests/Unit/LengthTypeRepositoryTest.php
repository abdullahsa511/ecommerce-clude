<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\LengthType;
use App\Core\Repositories\Product\LengthTypeRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class LengthTypeRepositoryTest extends TestCase
{
    private LengthTypeRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newLengthType = [
        "length_type_id" => 4,
        "value" => 10.00,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(LengthTypeRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newLengthType);
        $this->assertInstanceOf(LengthType::class, $result);
        $this->assertEquals($this->newLengthType['value'], $result->value);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newLengthType['length_type_id']);
        $this->assertInstanceOf(LengthType::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['value' => 11.00];
        $result = $this->repository->update($this->newLengthType['length_type_id'], $updateData);
        $this->assertInstanceOf(LengthType::class, $result);
        $this->assertEquals($updateData['value'], $result->value);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newLengthType['length_type_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 