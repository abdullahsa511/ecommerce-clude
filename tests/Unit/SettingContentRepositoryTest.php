<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Site\SettingContent;
use App\Core\Repositories\Site\SettingContentRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class SettingContentRepositoryTest extends TestCase
{
    private SettingContentRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newSettingContent = [
        "setting_content_id" => 1,
        "site_id" => 1,
        "language_id" => 1,
        "namespace" => "test_namespace",
        "key" => "test_key",
        "value" => "test_value"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(SettingContentRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newSettingContent);
        $this->assertInstanceOf(SettingContent::class, $result);
        $this->assertEquals($this->newSettingContent['site_id'], $result->site_id);
        $this->assertEquals($this->newSettingContent['language_id'], $result->language_id);
        $this->assertEquals($this->newSettingContent['namespace'], $result->namespace);
        $this->assertEquals($this->newSettingContent['key'], $result->key);
        $this->assertEquals($this->newSettingContent['value'], $result->value);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newSettingContent['setting_content_id']);
        $this->assertInstanceOf(SettingContent::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['value' => 'updated_value'];
        $result = $this->repository->update($this->newSettingContent['setting_content_id'], $updateData);
        $this->assertInstanceOf(SettingContent::class, $result);
        $this->assertEquals($updateData['value'], $result->value);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newSettingContent['setting_content_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 