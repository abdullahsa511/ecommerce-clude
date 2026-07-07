<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\Product;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newProduct = [
        "product_id" => 3,
        "admin_id" => 1,
        "parent_id" => 0,
        "model" => "Model-3",
        "sku" => "SKU-3",
        "upc" => "UPC-3",
        "ean" => "EAN-3",
        "jan" => "JAN-3",
        "isbn" => "ISBN-3",
        "mpn" => "MPN-3",
        "barcode" => "BARCODE-3",
        "hs_code" => "HS-3",
        "origin_country" => "Bangladesh",
        "mid_code" => "MID-3",
        "location" => "Location-3",
        "stock_quantity" => 3,
        "stock_status_id" => 3,
        "image" => "Image-3",
        "manufacturer_id" => 3,
        "vendor_id" => 3,
        "requires_shipping" => 1,
        "price" => 1,
        "points" => 1,
        "tax_type_id" => 1,
        "material" => "Material-1",
        "weight" => 1,
        "weight_type_id" => 1,
        "length" => 1,
        "width" => 1,
        "height" => 1,
        "length_type_id" => 1,
        // "date_available" => "2023-01-01",
        "type" => "product",
        "template" => "template-1",
        "views" => 1,
        "subtract_stock" => 1,
        "minimum_quantity" => 1,
        "status" => 1,
        "sort_order" => 1,
        "created_at" => "2023-01-01 00:00:00",
        "updated_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newProduct);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($this->newProduct['product_id'], $result->product_id);
        $this->assertEquals($this->newProduct['admin_id'], $result->admin_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newProduct['product_id']);
        $this->assertInstanceOf(Product::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['model' => 'updated-model'];
        $result = $this->repository->update($this->newProduct['product_id'], $updateData);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($updateData['model'], $result->model);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newProduct['product_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 