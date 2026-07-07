<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Media\Media;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MediaRepositoryTest extends TestCase
{
    private MediaRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newMedia = [
        "media_id" => 4,
        "file" => "test.jpg",
        "type" => "Image",
        "meta" => "Caption 1"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(MediaRepositoryInterface::class);
    }

    

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newMedia);
        $this->assertInstanceOf(Media::class, $result);
        $this->assertEquals($this->newMedia['media_id'], $result->media_id);
        $this->assertEquals($this->newMedia['file'], $result->file);
        $this->assertEquals($this->newMedia['type'], $result->type);
        $this->assertEquals($this->newMedia['meta'], $result->meta);
    }

    public function testGetAll(): void
    {
        $result = $this->repository->getAll();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertInstanceOf(Collection::class, $result['items']);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('total_pages', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertArrayHasKey('per_page', $result);
        $this->assertIsInt($result['total']);
        $this->assertIsInt($result['total_pages']);
        $this->assertIsInt($result['current_page']);
        $this->assertIsInt($result['per_page']);
    }

    public function testGet(): void
    {
        $result = $this->repository->get($this->newMedia['media_id']);
        $this->assertInstanceOf(Media::class, $result);
        $this->assertEquals($this->newMedia['media_id'], $result->media_id);
    }

    public function testUpdate(): void
    {
        $updateData = ['file' => 'Updated Media'];
        $result = $this->repository->update($this->newMedia['media_id'], $updateData);
        $this->assertInstanceOf(Media::class, $result);
        $this->assertEquals($updateData['file'], $result->file);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newMedia['media_id']);
        $this->assertIsBool($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 