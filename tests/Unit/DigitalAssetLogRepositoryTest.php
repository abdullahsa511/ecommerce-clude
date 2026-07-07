<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User\DigitalAssetLog;
use App\Core\Repositories\User\DigitalAssetLogRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class DigitalAssetLogRepositoryTest extends TestCase
{
    private DigitalAssetLogRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newDigitalAssetLog = [
        "digital_asset_log_id" => 3,
        "digital_asset_id" => 1,
        "user_id" => 1,
        "site_id" => 1,
        "ip" => "127.0.0.1",
        "country" => "Ph",
        "created_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(DigitalAssetLogRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newDigitalAssetLog);
        $this->assertInstanceOf(DigitalAssetLog::class, $result);
        $this->assertEquals($this->newDigitalAssetLog['digital_asset_log_id'], $result->digital_asset_log_id);
        $this->assertEquals($this->newDigitalAssetLog['ip'], $result->ip);
        $this->assertEquals($this->newDigitalAssetLog['country'], $result->country);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newDigitalAssetLog['digital_asset_log_id']);
        $this->assertInstanceOf(DigitalAssetLog::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = [
            'ip' => '127.0.0.2',
            'country' => 'Un'
        ];
        $result = $this->repository->update($this->newDigitalAssetLog['digital_asset_log_id'], $updateData);
        $this->assertInstanceOf(DigitalAssetLog::class, $result);
        $this->assertEquals($updateData['ip'], $result->ip);
        $this->assertEquals($updateData['country'], $result->country);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newDigitalAssetLog['digital_asset_log_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 