<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class TaxonomyItemRepositoryTest extends TestCase
{
    private TaxonomyItemRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newTaxonomyItem = [
        "taxonomy_item_id" => 88,
        "taxonomy_id" => 1,
        "image" => "test_image.jpg",
        "template" => "default",
        "parent_id" => 0,
        "item_id" => null,
        "sort_order" => 0,
        "status" => 1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxonomyItemRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newTaxonomyItem);
        $this->assertInstanceOf(TaxonomyItem::class, $result);
        $this->assertEquals($this->newTaxonomyItem['taxonomy_id'], $result->taxonomy_id);
        $this->assertEquals($this->newTaxonomyItem['image'], $result->image);
        $this->assertEquals($this->newTaxonomyItem['template'], $result->template);
        $this->assertEquals($this->newTaxonomyItem['parent_id'], $result->parent_id);
        $this->assertEquals($this->newTaxonomyItem['item_id'], $result->item_id);
        $this->assertEquals($this->newTaxonomyItem['sort_order'], $result->sort_order);
        $this->assertEquals($this->newTaxonomyItem['status'], $result->status);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1);
        $this->assertInstanceOf(TaxonomyItem::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['image' => 'updated_image.jpg'];
        $result = $this->repository->update(1, $updateData);
        $this->assertInstanceOf(TaxonomyItem::class, $result);
        $this->assertEquals($updateData['image'], $result->image);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 