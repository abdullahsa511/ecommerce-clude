<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\PostCategory\Taxonomy;
use App\Core\Repositories\PostCategory\TaxonomyRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PDO;

class TaxonomyRepositoryTest extends TestCase
{
    private TaxonomyRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newTaxonomy = [
        "taxonomy_id" => 6,
        "name" => "Test Taxonomy",
        "post_type" => "post",
        "type" => "categories",
        "site_id" => 1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxonomyRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newTaxonomy);
        $this->assertInstanceOf(Taxonomy::class, $result);
        $this->assertEquals($this->newTaxonomy['name'], $result->name);
        $this->assertEquals($this->newTaxonomy['post_type'], $result->post_type);
        $this->assertEquals($this->newTaxonomy['type'], $result->type);
        $this->assertEquals($this->newTaxonomy['site_id'], $result->site_id);
    }
    public function testGetAll(): void
    {
        $result = $this->repository->getAll(1, 1, 1);
        [$list, $total] = $result;

        $this->assertIsArray($result);
        $this->assertInstanceOf(Collection::class, $list);
        $this->assertInstanceOf(int::class, $total);
    }
    
    public function testAddTaxonomy(){
        $taxonomy = $this->repository->create([
            'name' => 'test', 
            'post_type' => 'post', 
            'type' => 'category', 
            'site_id' => 1]
        );
        $this->assertNotNull($taxonomy);
        $this->assertInstanceOf(Taxonomy::class, $taxonomy);
        $this->assertEquals('test', $taxonomy->name);
    }
    
  

    public function testEditTaxonomy(){
        $success = $this->repository->update(1, ['name' => 'Taxonomy Test']);
        $this->assertTrue($success);
        
        $taxonomy = $this->repository->getTaxonomy(1);
        $this->assertNotNull($taxonomy);
        $this->assertInstanceOf(Taxonomy::class, $taxonomy);
        $this->assertEquals('Taxonomy Test', $taxonomy->name);
    }
    
   

    public function testFind(): void
    {
        $result = $this->repository->find(1);
        $this->assertInstanceOf(Taxonomy::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['name' => 'Updated Taxonomy'];
        $result = $this->repository->update(1, $updateData);
        $this->assertInstanceOf(Taxonomy::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1);
        $this->assertTrue($result);
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