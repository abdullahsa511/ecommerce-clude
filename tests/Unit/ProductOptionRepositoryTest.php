<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductOption;
use App\Core\Repositories\Product\ProductOptionRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductOptionRepositoryTest extends TestCase
{
    private ProductOptionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newProductOption = [
        "product_id" => 1,     // Assuming product ID 1 exists
        "option_id" => 1,      // Assuming option ID 1 exists
        "value" => "Test Value",
        "required" => 1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductOptionRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newProductOption);
        $this->assertInstanceOf(ProductOption::class, $result);
        $this->assertEquals($this->newProductOption['product_id'], $result->product_id);
        $this->assertEquals($this->newProductOption['option_id'], $result->option_id);
        $this->assertEquals($this->newProductOption['value'], $result->value);
        $this->assertEquals($this->newProductOption['required'], $result->required);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1); // Assuming product_option_id 1 exists
        $this->assertInstanceOf(ProductOption::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['value' => 'Updated Value'];
        $result = $this->repository->update(1, $updateData); // Assuming product_option_id 1 exists
        $this->assertInstanceOf(ProductOption::class, $result);
        $this->assertEquals($updateData['value'], $result->value);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1); // Assuming product_option_id 1 exists
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 