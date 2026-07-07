<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Subscription\Subscription;
use App\Core\Repositories\Subscription\SubscriptionRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class SubscriptionRepositoryTest extends TestCase
{
    private SubscriptionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newSubscription = [
        "subscription_id" => 4,
        "order_id" => 1,
        "order_product_id" => 1,
        "site_id" => 1,
        "user_id" => 1,
        // "payment_address_id" => 1,
        "payment_method" => "credit_card",
        // "shipping_address_id" => 1,
        "shipping_method" => "standard",
        "product_id" => 1,
        "quantity" => 1,
        // "subscription_plan_id" => 1,
        "price" => 99.99,
        "period" => "month",
        "cycle" => 1,
        "length" => 12,
        "left" => 1,
        "trial_price" => 10.00,
        "trial_period" => "day",
        "trial_cycle" => 7,
        "trial_length" => 7,
        "trial_left" => 7,
        "trial_status" => 1,
        "date_next" => "2024-03-01",
        "subscription_status_id" => 1,
        "notes" => "Test subscription",
        "created_at" => "2024-02-01 00:00:00",
        "updated_at" => "2024-02-01 00:00:00",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(SubscriptionRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newSubscription);
        $this->assertInstanceOf(Subscription::class, $result);
        $this->assertEquals($this->newSubscription['subscription_id'], $result->subscription_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newSubscription['subscription_id']);
        $this->assertInstanceOf(Subscription::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['period' => 'week'];
        $result = $this->repository->update($this->newSubscription['subscription_id'], $updateData);
        $this->assertInstanceOf(Subscription::class, $result);
        $this->assertEquals($updateData['period'], $result->period);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newSubscription['subscription_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 