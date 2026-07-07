<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User\UserGroup;
use App\Core\Repositories\User\UserGroupRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class UserGroupRepositoryTest extends TestCase
{
    private UserGroupRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newUserGroup = [
        "user_group_id" => 1,
        "status" => 1,
        "sort_order" => 0
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(UserGroupRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newUserGroup);
        $this->assertInstanceOf(UserGroup::class, $result);
        $this->assertEquals($this->newUserGroup['status'], $result->status);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newUserGroup['user_group_id']);
        $this->assertInstanceOf(UserGroup::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['status' => 0];
        $result = $this->repository->update($this->newUserGroup['user_group_id'], $updateData);
        $this->assertInstanceOf(UserGroup::class, $result);
        $this->assertEquals($updateData['status'], $result->status);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newUserGroup['user_group_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 