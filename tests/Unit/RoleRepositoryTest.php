<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Admin\Role;
use App\Core\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class RoleRepositoryTest extends TestCase
{
    private RoleRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newRole = [
        "name" => "Test Role",
        "display_name" => "Test Role Display",
        "permissions" => "[]"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(RoleRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newRole);
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($this->newRole['name'], $result->name);
        $this->assertEquals($this->newRole['display_name'], $result->display_name);
        $this->assertEquals($this->newRole['permissions'], $result->permissions);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1); // Assuming role_id 1 exists
        $this->assertInstanceOf(Role::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['display_name' => 'Updated Role Display'];
        $result = $this->repository->update(1, $updateData); // Assuming role_id 1 exists
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($updateData['display_name'], $result->display_name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1); // Assuming role_id 1 exists
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 