<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductReviewMedia;
use App\Core\Repositories\Product\ProductReviewMediaRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductReviewMediaRepositoryTest extends TestCase
{
    private ProductReviewMediaRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newProductReviewMedia = [
        "product_review_id" => 1, // Assuming product review ID 1 exists
        "product_id" => 1,        // Assuming product ID 1 exists
        "user_id" => 1,           // Assuming user ID 1 exists
        "image" => "test_image.jpg",
        "sort_order" => 0
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductReviewMediaRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newProductReviewMedia);
        $this->assertInstanceOf(ProductReviewMedia::class, $result);
        $this->assertEquals($this->newProductReviewMedia['product_review_id'], $result->product_review_id);
        $this->assertEquals($this->newProductReviewMedia['product_id'], $result->product_id);
        $this->assertEquals($this->newProductReviewMedia['user_id'], $result->user_id);
        $this->assertEquals($this->newProductReviewMedia['image'], $result->image);
        $this->assertEquals($this->newProductReviewMedia['sort_order'], $result->sort_order);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1); // Assuming product_review_media_id 1 exists
        $this->assertInstanceOf(ProductReviewMedia::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['image' => 'updated_image.jpg'];
        $result = $this->repository->update(1, $updateData); // Assuming product_review_media_id 1 exists
        $this->assertInstanceOf(ProductReviewMedia::class, $result);
        $this->assertEquals($updateData['image'], $result->image);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1); // Assuming product_review_media_id 1 exists
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 