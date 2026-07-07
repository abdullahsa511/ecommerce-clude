<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\ProductQuestion;
use App\Core\Repositories\Product\ProductQuestionRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ProductQuestionRepositoryTest extends TestCase
{
    private ProductQuestionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newProductQuestion = [
        "product_id" => 1,     // Assuming product ID 1 exists
        "user_id" => 1,        // Assuming user ID 1 exists
        "author" => "Test Author",
        "content" => "Test Content",
        "status" => 1,
        "parent_id" => 0
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductQuestionRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newProductQuestion);
        $this->assertInstanceOf(ProductQuestion::class, $result);
        $this->assertEquals($this->newProductQuestion['product_id'], $result->product_id);
        $this->assertEquals($this->newProductQuestion['user_id'], $result->user_id);
        $this->assertEquals($this->newProductQuestion['author'], $result->author);
        $this->assertEquals($this->newProductQuestion['content'], $result->content);
        $this->assertEquals($this->newProductQuestion['status'], $result->status);
        $this->assertEquals($this->newProductQuestion['parent_id'], $result->parent_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1); // Assuming product_question_id 1 exists
        $this->assertInstanceOf(ProductQuestion::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['content' => 'Updated Content'];
        $result = $this->repository->update(1, $updateData); // Assuming product_question_id 1 exists
        $this->assertInstanceOf(ProductQuestion::class, $result);
        $this->assertEquals($updateData['content'], $result->content);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1); // Assuming product_question_id 1 exists
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 