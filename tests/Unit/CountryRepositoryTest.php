<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Geoip\Country;
use App\Core\Repositories\Geoip\CountryRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class CountryRepositoryTest extends TestCase
{
    private CountryRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Country model
        $this->country = new Country();
        $this->country->setDb($this->db);
        $this->repository = $this->container->make(CountryRepositoryInterface::class);
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
        $country = $this->repository->get(1);
        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals(1, $country->country_id);
    }

    public function testGetWithFilters(): void
    {
        $result = $this->repository->getAll(
            status: 1,
            search: 'ladesh',
            start: 0,
            limit: 10
        );
        
        $this->assertInstanceOf(Collection::class, $result['items']);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('total_pages', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertArrayHasKey('per_page', $result);
    }

    public function testGetNonExistent(): void
    {
        $country = $this->repository->get(999999);
        $this->assertNull($country);
    }


    public function testFind(): void
    {
        $country = $this->repository->find(1);
        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
    }

    public function testFindAll(): void
    {
        $countries = $this->repository->findAll();
        $this->assertNotNull($countries);
        $this->assertIsArray($countries);
        $this->assertNotEmpty($countries);
    }

    public function testCreate(): void
    {
        $country = $this->repository->create([
            'name' => 'test 2',
            'iso_code_2' => 'TT',
            'iso_code_3' => 'TTO',
            'status' => 1,
        ]);
        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
    }

    public function testUpdate(): void
    {
        $country = $this->repository->update(2, [
            'name' => 'test updated',
            'status' => 1,
        ]);

        // $this->assertTrue($post);
        
        $country = $this->repository->get(2);
        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test updated', $country->name);
    }

    public function testInsertMultiple(): void
    {
        $posts = $this->repository->insertMultiple([
            [
                'name' => 'test 2',
                'iso_code_2' => 'TT',
                'iso_code_3' => 'TTO',
                'status' => 1,
            ],
            [
                'name' => 'test 3',
                'iso_code_2' => 'TT',
                'iso_code_3' => 'TTO',
                'status' => 1,
            ]
        ]);
        $this->assertTrue($posts);
    }

    public function testDelete(): void
    {
        $country = $this->repository->find(6);
        $this->assertNotNull($country);
        $country_id = $country->country_id;
        $deleted = $this->repository->delete($country_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([5, 7]);
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