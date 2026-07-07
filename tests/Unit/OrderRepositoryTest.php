<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\Order;
use App\Core\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    private OrderRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newOrder = [
        "order_id" => 10,
        "invoice_no" => "123456",
        "customer_order_id" => "654321",
        "invoice_prefix" => "I-",
        "site_id" => 1,
        "site_name" => "Example Site",
        "user_id" => 1,
        "user_group_id" => 1,
        "first_name" => "John",
        "last_name" => "Doe",
        "email" => "johndoe@example.com",
        "phone_number" => "1234567890",
        "billing_first_name" => "John",
        "billing_last_name" => "Doe",
        "billing_company" => "Company Inc.",
        "billing_address_1" => "123 Main St",
        "billing_address_2" => "Apt 4B",
        "billing_city" => "Metropolis",
        "billing_post_code" => "12345",
        "billing_country_id" => 1,
        "billing_region" => "Region",
        "billing_region_id" => 1,
        "payment_method" => "Credit Card",
        "payment_data" => "{}",
        "payment_status_id" => 1,
        "shipping_first_name" => "John",
        "shipping_last_name" => "Doe",
        "shipping_company" => "Company Inc.",
        "shipping_address_1" => "123 Main St",
        "shipping_address_2" => "Apt 4B",
        "shipping_city" => "Metropolis",
        "shipping_post_code" => "12345",
        "shipping_country" => "Country",
        "shipping_country_id" => 1,
        "shipping_region" => "Region",
        "shipping_region_id" => 1,
        "shipping_method" => "Standard",
        "shipping_data" => "{}",
        "shipping_status_id" => 1,
        "total" => 100.00,
        "order_status_id" => 1,
        "language_id" => 1,
        "currency_id" => 1,
        "currency" => "USD",
        "currency_value" => 1.00000000,
        "notes" => "",
        "remote_ip" => "192.168.1.1",
        "forwarded_for_ip" => "192.168.1.1",
        "user_agent" => "Mozilla/5.0",
        "created_at" => "2023-01-01 00:00:00",
        "updated_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OrderRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newOrder);
        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($this->newOrder['invoice_no'], $result->invoice_no);
        $this->assertEquals($this->newOrder['email'], $result->email);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newOrder['order_id']);
        $this->assertInstanceOf(Order::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['email' => 'updated@example.com'];
        $result = $this->repository->update($this->newOrder['order_id'], $updateData);
        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($updateData['email'], $result->email);
    }

    // public function testDelete(): void
    // {
    //     $result = $this->repository->delete($this->newOrder['order_id']);
    //     $this->assertTrue($result);
    // }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 