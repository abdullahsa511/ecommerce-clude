<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Subscription\SubscriptionPlan;
use App\Core\Repositories\Subscription\SubscriptionPlanRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class SubscriptionPlanRepositoryTest extends TestCase
{
    private SubscriptionPlanRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newSubscriptionPlan = [
        "subscription_plan_id" => 5,
        "period" => "month",
        "length" => 12,
        "cycle" => 1,
        "trial_period" => "day",
        "trial_length" => 7,
        "trial_cycle" => 7,
        "trial_status" => 1,
        "status" => 1,
        "sort_order" => 0
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(SubscriptionPlanRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newSubscriptionPlan);
        $this->assertInstanceOf(SubscriptionPlan::class, $result);
        $this->assertEquals($this->newSubscriptionPlan['subscription_plan_id'], $result->subscription_plan_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newSubscriptionPlan['subscription_plan_id']);
        $this->assertInstanceOf(SubscriptionPlan::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['trial_period' => 'week'];
        $result = $this->repository->update($this->newSubscriptionPlan['subscription_plan_id'], $updateData);
        $this->assertInstanceOf(SubscriptionPlan::class, $result);
        $this->assertEquals($updateData['trial_period'], $result->trial_period);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newSubscriptionPlan['subscription_plan_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 