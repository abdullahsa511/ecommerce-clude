<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Menu\Menu;
use App\Core\Repositories\Menu\MenuRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class MenuRepositoryTest extends TestCase
{
    private MenuRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newMenu = [
        "menu_id" => 4,
        "name" => "Menu 4",
        "slug" => "menu-4",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(MenuRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newMenu);
        $this->assertInstanceOf(Menu::class, $result);
        $this->assertEquals($this->newMenu['name'], $result->name);
        $this->assertEquals($this->newMenu['slug'], $result->slug);
    }

    public function testFind(): void
    {
        $result = $this->repository->find($this->newMenu['menu_id']);
        $this->assertInstanceOf(Menu::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Updated Menu'];
        $result = $this->repository->update($this->newMenu['menu_id'], $updateData);
        $this->assertInstanceOf(Menu::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete($this->newMenu['menu_id']);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 