<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductContentRevision;
use App\Core\Repositories\Product\ProductContentRevisionRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductContentRevisionRepositoryTest extends TestCase
{
    private ProductContentRevisionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newContentRevision = [
        "product_id" => 1,     // Assuming product ID 1 exists
        "language_id" => 1,    // Assuming language ID 1 exists
        "content" => "Test Content",
        "admin_id" => 1,       // Assuming admin ID 1 exists
        "created_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductContentRevisionRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newContentRevision);
        $this->assertInstanceOf(ProductContentRevision::class, $result);
        $this->assertEquals($this->newContentRevision['product_id'], $result->product_id);
        $this->assertEquals($this->newContentRevision['language_id'], $result->language_id);
        $this->assertEquals($this->newContentRevision['content'], $result->content);
        $this->assertEquals($this->newContentRevision['admin_id'], $result->admin_id);
        $this->assertEquals($this->newContentRevision['created_at'], $result->created_at);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newContentRevision['product_id'], $this->newContentRevision['language_id'], $this->newContentRevision['created_at'], $this->newContentRevision['admin_id']);
        $this->assertInstanceOf(ProductContentRevision::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['content' => 'Updated Content'];
        $result = $this->repository->update($this->newContentRevision['product_id'], $this->newContentRevision['language_id'], $this->newContentRevision['created_at'], $this->newContentRevision['admin_id'], $updateData);
        $this->assertInstanceOf(ProductContentRevision::class, $result);
        $this->assertEquals($updateData['content'], $result->content);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newContentRevision['product_id'], $this->newContentRevision['language_id'], $this->newContentRevision['created_at'], $this->newContentRevision['admin_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 