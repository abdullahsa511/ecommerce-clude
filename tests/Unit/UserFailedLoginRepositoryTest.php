<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User\UserFailedLogin;
use App\Core\Repositories\User\UserFailedLoginRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class UserFailedLoginRepositoryTest extends TestCase
{
    private UserFailedLoginRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newFailedLogin = [
        "user_failed_login_id" => 1,
        "user_id" => 1,
        "count" => 1,
        "last_ip" => "192.168.1.1",
        "updated_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(UserFailedLoginRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newFailedLogin);
        $this->assertInstanceOf(UserFailedLogin::class, $result);
        $this->assertEquals($this->newFailedLogin['user_failed_login_id'], $result->user_failed_login_id);
        $this->assertEquals($this->newFailedLogin['user_id'], $result->user_id);
        $this->assertEquals($this->newFailedLogin['last_ip'], $result->last_ip);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newFailedLogin['user_failed_login_id']);
        $this->assertInstanceOf(UserFailedLogin::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['count' => 2];
        $result = $this->repository->update($this->newFailedLogin['user_failed_login_id'], $updateData);
        $this->assertInstanceOf(UserFailedLogin::class, $result);
        $this->assertEquals($updateData['count'], $result->count);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newFailedLogin['user_failed_login_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 