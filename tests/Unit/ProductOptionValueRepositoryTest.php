<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductOptionValue;
use App\Core\Repositories\Product\ProductOptionValueRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductOptionValueRepositoryTest extends TestCase
{
    private ProductOptionValueRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newProductOptionValue = [
        "product_option_value_id" => 5,
        "product_option_id" => 1,
        "product_id" => 1,
        "option_id" => 1,
        "option_value_id" => 1,
        "quantity" => 1,
        "subtract" => 1,
        "price_operator" => "=",
        "price" => 10,
        "points_operator" => "=",
        "points" => 1,
        "weight_operator" => "=",
        "weight" => 1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductOptionValueRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newProductOptionValue);
        $this->assertInstanceOf(ProductOptionValue::class, $result);
        $this->assertEquals($this->newProductOptionValue['product_option_value_id'], $result->product_option_value_id);
        // $this->assertEquals($this->newProductOptionValue['product_option_id'], $result->product_option_id);
        $this->assertEquals($this->newProductOptionValue['product_id'], $result->product_id);
        $this->assertEquals($this->newProductOptionValue['option_id'], $result->option_id);
        $this->assertEquals($this->newProductOptionValue['option_value_id'], $result->option_value_id);
        $this->assertEquals($this->newProductOptionValue['quantity'], $result->quantity);
        $this->assertEquals($this->newProductOptionValue['subtract'], $result->subtract);
        
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newProductOptionValue['product_option_value_id']);
        $this->assertInstanceOf(ProductOptionValue::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = [
            'quantity' => 2,
            'subtract' => 2,
            'price' => 20,
            'points' => 2,
            'weight' => 2
        ];
        $result = $this->repository->update($this->newProductOptionValue['product_option_value_id'], $updateData);
        $this->assertInstanceOf(ProductOptionValue::class, $result);
        $this->assertEquals($updateData['quantity'], $result->quantity);
        $this->assertEquals($updateData['subtract'], $result->subtract);
        $this->assertEquals($updateData['price'], $result->price);
        $this->assertEquals($updateData['points'], $result->points);
        $this->assertEquals($updateData['weight'], $result->weight);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newProductOptionValue['product_option_value_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 