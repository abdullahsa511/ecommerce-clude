<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Geoip\Region;
use App\Core\Repositories\Geoip\RegionRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class RegionRepositoryTest extends TestCase
{
    private RegionRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Region model
        $this->region = new Region();
        $this->region->setDb($this->db);
        $this->repository = $this->container->make(RegionRepositoryInterface::class);
    }

    public function testGetAll(): void
    {
        $result = $this->repository->getAll();
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
        $region = $this->repository->get(1);
        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals(1, $region->region_id);
    }


    public function testFind(): void
    {
        $region = $this->repository->find(1);
        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
    }

    public function testFindAll(): void
    {
        $regions = $this->repository->findAll();
        $this->assertNotNull($regions);
        $this->assertIsArray($regions);
        $this->assertNotEmpty($regions);
    }

    public function testCreate(): void
    {
        $region = $this->repository->create([
            'name' => 'test region 2',
            'country_id' => 1,
            'code' => 'TR',
            'status' => 1,
        ]);
        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
    }

    public function testUpdate(): void
    {
        $region = $this->repository->update(3, [
            'country_id' => 1,
            'name' => 'test updated',
            'code' => 'TR',
            'status' => 1,
        ]);

        $region = $this->repository->get(3);
        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals('test updated', $region->name);
    }

    public function testInsertMultiple(): void
    {
        $regions = $this->repository->insertMultiple([
            [
                'name' => 'test region 1',
                'country_id' => 1,
                'code' => 'TR1',
                'status' => 1,
            ],
            [
                'name' => 'test region 2',
                'country_id' => 1,
                'code' => 'TR2',
                'status' => 1,
            ]
        ]);
        $this->assertTrue($regions);
    }

    public function testDelete(): void
    {
        $region = $this->repository->find(6);
        $this->assertNotNull($region);
        $region_id = $region->region_id;
        $deleted = $this->repository->delete($region_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([5, 8]);
        $this->assertIsInt($deleted);
        $this->assertEquals(2, $deleted);
    }

    protected function tearDown(): void
    {
        // Close the database connection
        $this->db = null;
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 