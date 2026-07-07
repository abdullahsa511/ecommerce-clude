<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Site\Setting;
use App\Core\Repositories\Site\SettingRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class SettingRepositoryTest extends TestCase
{
    private SettingRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newSetting = [
        "site_id" => 1,
        "namespace" => "test_namespace",
        "key" => "test_key",
        "value" => "test_value"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(SettingRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newSetting);
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals($this->newSetting['site_id'], $result->site_id);
        $this->assertEquals($this->newSetting['namespace'], $result->namespace);
        $this->assertEquals($this->newSetting['key'], $result->key);
        $this->assertEquals($this->newSetting['value'], $result->value);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newSetting['site_id']);
        $this->assertInstanceOf(Setting::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['value' => 'updated_value'];
        $result = $this->repository->update($this->newSetting['site_id'], $updateData);
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals($updateData['value'], $result->value);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newSetting['site_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 