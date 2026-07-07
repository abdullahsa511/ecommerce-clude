<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User;
use App\Core\Repositories\UserRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newUser = [
        "user_id" => 9,
        "user_group_id" => 1,
        "site_id" => 1,
        "username" => "testuser",
        "first_name" => "Test",
        "last_name" => "User",
        "password" => "password",
        "email" => "testuser@example.com",
        "phone_number" => "1234567890",
        "url" => "http://example.com",
        "status" => 1,
        "display_name" => "Test User",
        "avatar" => "avatar.jpg",
        "bio" => "This is a test user.",
        "token" => "token123",
        "subscribe" => 1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(UserRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newUser);
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->newUser['username'], $result->username);
        $this->assertEquals($this->newUser['email'], $result->email);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1);
        $this->assertInstanceOf(User::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['email' => 'updateduser@example.com'];
        $result = $this->repository->update(1, $updateData);
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($updateData['email'], $result->email);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 