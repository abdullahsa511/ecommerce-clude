<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User\DigitalAsset;
use App\Core\Repositories\User\DigitalAssetRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class DigitalAssetRepositoryTest extends TestCase
{
    private DigitalAssetRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newDigitalAsset = [
        "digital_asset_id" => 3,
        "file" => "dummy_file.txt",
        "public" => 1,
        "created_at" => "2023-01-01 00:00:00"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(DigitalAssetRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newDigitalAsset);
        $this->assertInstanceOf(DigitalAsset::class, $result);
        $this->assertEquals($this->newDigitalAsset['invoice_no'], $result->invoice_no);
        $this->assertEquals($this->newDigitalAsset['email'], $result->email);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newDigitalAsset['digital_asset_id']);
        $this->assertInstanceOf(DigitalAsset::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['file' => 'updated_file.txt'];
        $result = $this->repository->update($this->newDigitalAsset['digital_asset_id'], $updateData);
        $this->assertInstanceOf(DigitalAsset::class, $result);
        $this->assertEquals($updateData['file'], $result->file);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newDigitalAsset['digital_asset_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 