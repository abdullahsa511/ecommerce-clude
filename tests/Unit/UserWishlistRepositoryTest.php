<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User\UserWishlist;
use App\Core\Repositories\User\UserWishlistRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class UserWishlistRepositoryTest extends TestCase
{
    private UserWishlistRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newWishlistItem = [
        "user_id" => 1,
        "product_id" => 1,
        "created_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(UserWishlistRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newWishlistItem);
        $this->assertInstanceOf(UserWishlist::class, $result);
        $this->assertEquals($this->newWishlistItem['user_id'], $result->user_id);
        $this->assertEquals($this->newWishlistItem['product_id'], $result->product_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(['user_id' => 1, 'product_id' => 1]);
        $this->assertInstanceOf(UserWishlist::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['created_at' => '2023-01-02 00:00:00'];
        $result = $this->repository->update(['user_id' => 1, 'product_id' => 1], $updateData);
        $this->assertInstanceOf(UserWishlist::class, $result);
        $this->assertEquals($updateData['created_at'], $result->created_at);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(['user_id' => 1, 'product_id' => 1]);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 