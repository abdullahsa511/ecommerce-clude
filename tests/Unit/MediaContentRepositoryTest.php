<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Media\MediaContent;
use App\Core\Repositories\Media\MediaContentRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MediaContentRepositoryTest extends TestCase
{
    private MediaContentRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newMediaContent = [
        "media_id" => 1,
        "language_id" => 1,
        "name" => "Media Content 1",
        "caption" => "Caption 1",
        "description" => "Description 1"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(MediaContentRepositoryInterface::class);
    }


    // public function testCreate(): void
    // {
    //     $result = $this->repository->create($this->newMediaContent);
    //     $this->assertInstanceOf(MediaContent::class, $result);
    //     $this->assertEquals($this->newMediaContent['media_id'], $result->media_id);
    //     $this->assertEquals($this->newMediaContent['language_id'], $result->language_id);
    //     $this->assertEquals($this->newMediaContent['name'], $result->name);
    //     $this->assertEquals($this->newMediaContent['caption'], $result->caption);
    //     $this->assertEquals($this->newMediaContent['description'], $result->description);
    // }

    // public function testFind(): void
    // {
    //     $result = $this->repository->find($this->newMediaContent['media_id'], $this->newMediaContent['language_id']);
    //     $this->assertInstanceOf(MediaContent::class, $result);
    // }

    // public function testFind(): void
    // {
    //     $result = $this->repository->find($this->newMediaContent['media_id']);
    //     $this->assertInstanceOf(MediaContent::class, $result);
    // }

    // public function testFindAll(): void
    // {
    //     $result = $this->repository->findAll();
    //     $this->assertIsArray($result);
    // }

    // public function testUpdate(): void
    // {
    //     $updateData = ['name' => 'Updated Media Content'];
    //     $result = $this->repository->update(2, $updateData);
    //     $this->assertInstanceOf(MediaContent::class, $result);
    //     $this->assertEquals($updateData['name'], $result->name);
    // }

    // public function testDelete(): void
    // {
    //     $result = $this->repository->delete(2);
    //     $this->assertIsBool($result);
    // }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 