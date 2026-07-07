<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Post\PostContentMeta;
use App\Core\ModelsFilters\PostFilter;
use App\Core\Repositories\Post\PostContentMetaRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class PostContentMetaRepositoryTest extends TestCase
{
    private PostContentMetaRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private PostContentMeta $postContentMeta;


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize PostContentMeta model
        $this->postContentMeta = new PostContentMeta();
        $this->postContentMeta->setDb($this->db);
        $this->repository = $this->container->make(PostContentMetaRepositoryInterface::class);
    }


    public function testFind(): void
    {
        $post = $this->repository->find(3);
        $this->assertNotNull($post);
        $this->assertInstanceOf(PostContentMeta::class, $post);
    }

    public function testFindAll(): void
    {
        $posts = $this->repository->findAll();
        $this->assertNotNull($posts);
        $this->assertIsArray($posts);
        $this->assertNotEmpty($posts);
    }

    public function testCreate(): void
    {
        $post = $this->repository->create([
            'post_id' => 7,
            'language_id' => 1,
            'namespace' => 'meta-test',
            'key' => 'mt',
            'value' => 'sssssss',
        ]);
        $this->assertNotNull($post);
        $this->assertInstanceOf(PostContentMeta::class, $post);
    }

    public function testUpdate(): void
    {
        $post = $this->repository->update(3, [
            'language_id' => 1,
            'namespace' => 'meta-test-update',
            'value' => 'aaaa',
        ]);

        // $this->assertTrue($post);
        
        $post = $this->repository->get(3);
        $this->assertNotNull($post);
        $this->assertInstanceOf(PostContentMeta::class, $post);
        $this->assertEquals('aaaa', $post->value);
    }

    public function testDelete(): void
    {
        $post = $this->repository->find(5);
        $this->assertNotNull($post);
        $post_id = $post->post_id;
        $deleted = $this->repository->delete($post_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([95, 97]);
        $this->assertIsInt($deleted);
        $this->assertEquals(2, $deleted);
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
        $post = $this->repository->get(3);
        $this->assertNotNull($post);
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