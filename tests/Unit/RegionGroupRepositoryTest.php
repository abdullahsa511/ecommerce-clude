<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Geoip\RegionGroup;
use App\Core\Repositories\Geoip\RegionGroupRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class RegionGroupRepositoryTest extends TestCase
{
    private RegionGroupRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private RegionGroup $regionGroup;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize RegionGroup model
        $this->regionGroup = new RegionGroup();
        $this->regionGroup->setDb($this->db);
        $this->repository = $this->container->make(RegionGroupRepositoryInterface::class);
    }

    public function testGet(): void
    {
        $regionGroup = $this->repository->get(1);
        $this->assertNotNull($regionGroup);
        $this->assertInstanceOf(RegionGroup::class, $regionGroup);
        $this->assertEquals(1, $regionGroup->region_group_id);
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

    public function testGetRegions(): void
    {
        $result = $this->repository->getRegions(
            regionGroupId: 1,
            countryId: 1,
            start: 0,
            limit: 10
        );
        
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertIsArray($result['items']);
        $this->assertIsInt($result['total']);
    }

    // public function testIsRegion(): void
    // {
    //     $result = $this->repository->isRegion(
    //         regionGroupId: 1,
    //         countryId: 1,
    //         regionId: 1,
    //         start: 0,
    //         limit: 10
    //     );
        
    //     $this->assertArrayHasKey('items', $result);
    //     $this->assertArrayHasKey('total', $result);
    //     $this->assertIsArray($result['items']);
    //     $this->assertIsInt($result['total']);
    // }

    // public function testAddRegions(): void
    // {
    //     $data = [
    //         [
    //             'region_id' => 1,
    //             'country_id' => 1
    //         ],
    //         [
    //             'region_id' => 2,
    //             'country_id' => 1
    //         ]
    //     ];
        
    //     $result = $this->repository->addRegions($data, 1);
    //     $this->assertTrue($result);
    // }

    // public function testGetNonExistent(): void
    // {
    //     $regionGroup = $this->repository->get(999999);
    //     $this->assertNull($regionGroup);
    // }

    public function testFind(): void
    {
        $regionGroup = $this->repository->find(1);
        $this->assertNotNull($regionGroup);
        $this->assertInstanceOf(RegionGroup::class, $regionGroup);
    }

    public function testFindAll(): void
    {
        $regionGroups = $this->repository->findAll();
        $this->assertNotNull($regionGroups);
        $this->assertIsArray($regionGroups);
        $this->assertNotEmpty($regionGroups);
    }

    public function testCreate(): void
    {
        $regionGroup = $this->repository->create([
            'name' => 'test 2 region group',
            'content' => 'test content',
        ]);
        $this->assertNotNull($regionGroup);
        $this->assertInstanceOf(RegionGroup::class, $regionGroup);
    }

    public function testUpdate(): void
    {
        $regionGroup = $this->repository->update(2, [
            'name' => 'test updated',
        ]);

        $regionGroup = $this->repository->get(2);
        $this->assertNotNull($regionGroup);
        $this->assertInstanceOf(RegionGroup::class, $regionGroup);
        $this->assertEquals('test updated', $regionGroup->name);
    }

    public function testInsertMultiple(): void
    {
        $regionGroups = $this->repository->insertMultiple([
            [
                'name' => 'test region group 1',
            ],
            [
                'name' => 'test region group 2',
            ]
        ]);
        $this->assertTrue($regionGroups);
    }

    public function testDelete(): void
    {
        $regionGroup = $this->repository->find(5);
        $this->assertNotNull($regionGroup);
        $region_group_id = $regionGroup->region_group_id;
        $deleted = $this->repository->delete($region_group_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([3, 4]);
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