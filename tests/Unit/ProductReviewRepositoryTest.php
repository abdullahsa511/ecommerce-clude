<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductReview;
use App\Core\Repositories\Product\ProductReviewRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductReviewRepositoryTest extends TestCase
{
    private ProductReviewRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newProductReview = [
        "product_review_id" => 6,
        "product_id" => 1,
        "user_id" => 1,
        "author" => "John Doe",
        "content" => "This is a test content",
        "rating" => 5,
        "status" => 1,
        "parent_id" => 1,
        "created_at" => "2021-01-01",
        "updated_at" => "2021-01-01"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductReviewRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newProductReview);
        $this->assertInstanceOf(ProductReview::class, $result);
        $this->assertEquals($this->newProductReview['product_review_id'], $result->product_review_id);
        $this->assertEquals($this->newProductReview['product_id'], $result->product_id);
        $this->assertEquals($this->newProductReview['user_id'], $result->user_id);
        
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newProductReview['product_review_id']);
        $this->assertInstanceOf(ProductReview::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = [
            'author' => 'Jane Doe updated',
            'content' => 'This is a test content updated',
            'rating' => 4,
        ];
        $result = $this->repository->update($this->newProductReview['product_review_id'], $updateData);
        $this->assertInstanceOf(ProductReview::class, $result);
        // $this->assertEquals($updateData['content'], $result->content);
        // $this->assertEquals($updateData['rating'], $result->rating);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newProductReview['product_review_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 