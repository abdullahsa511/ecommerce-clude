<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Subscription\SubscriptionStatus;
use App\Core\Repositories\Subscription\SubscriptionStatusRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class SubscriptionStatusRepositoryTest extends TestCase
{
    private SubscriptionStatusRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newSubscriptionStatus = [
        "subscription_status_id" => 4,
        "language_id" => 1,
        "name" => "Subscription Status 4",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(SubscriptionStatusRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newSubscriptionStatus);
        $this->assertInstanceOf(SubscriptionStatus::class, $result);
        $this->assertEquals($this->newSubscriptionStatus['subscription_status_id'], $result->subscription_status_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newSubscriptionStatus['subscription_status_id']);
        $this->assertInstanceOf(SubscriptionStatus::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Subscription Status 4 Updated'];
        $result = $this->repository->update($this->newSubscriptionStatus['subscription_status_id'], $updateData);
        $this->assertInstanceOf(SubscriptionStatus::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newSubscriptionStatus['subscription_status_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 