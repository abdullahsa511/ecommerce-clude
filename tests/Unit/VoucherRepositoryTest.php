<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Checkout\Voucher;
use App\Core\Repositories\Checkout\VoucherRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class VoucherRepositoryTest extends TestCase
{
    private VoucherRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newVoucher = [
        "voucher_id" => 2,
        "order_id" => 1,
        "code" => "VOUCHER4",
        "from_name" => "Voucher 4",
        "from_email" => "voucher4@example.com",
        "to_name" => "Voucher 4",
        "to_email" => "voucher4@example.com",
        "message" => "Voucher 4",
        "credit" => 100,
        "status" => 1,
        "created_at" => "2024-02-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(VoucherRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newVoucher);
        $this->assertInstanceOf(Voucher::class, $result);
        $this->assertEquals($this->newVoucher['voucher_id'], $result->voucher_id);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newVoucher['voucher_id']);
        $this->assertInstanceOf(Voucher::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['to_name' => 'Voucher 2 Updated'];
        $result = $this->repository->update($this->newVoucher['voucher_id'], $updateData);
        $this->assertInstanceOf(Voucher::class, $result);
        $this->assertEquals($updateData['to_name'], $result->to_name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newVoucher['voucher_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 