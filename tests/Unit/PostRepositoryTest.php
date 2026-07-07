<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Post\Post;
use App\Core\ModelsFilters\PostFilter;
use App\Core\Repositories\Post\PostRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class PostRepositoryTest extends TestCase
{
    private PostRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Post $post;


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Post model
        $this->post = new Post();
        $this->post->setDb($this->db);
        $this->repository = $this->container->make(PostRepositoryInterface::class);
    }

    public function testFind(): void
    {
        $post = $this->repository->find(1);
        $this->assertNotNull($post);
        $this->assertInstanceOf(Post::class, $post);
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
            'admin_id' => 2,
            'status' => 'unpublish',
            'comment_status' => 'open',
            'comment_count' => 0,
            'type' => 'post',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->assertNotNull($post);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testUpdate(): void
    {
        $post = $this->repository->update(3, [
            'admin_id' => 1,
            'status' => 'publish',
            'comment_status' => 'open',
            'comment_count' => 0,
            'type' => 'post',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // $this->assertTrue($post);
        
        $post = $this->repository->get(3);
        $this->assertNotNull($post);
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('publish', $post->status);
    }

    public function testDelete(): void
    {
        $post = $this->repository->find(99);
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

    public function testInsertMultiple(): void
    {
        $posts = $this->repository->insertMultiple([
            [
                'admin_id' => 1,
                'status' => 'unpublish',
                'comment_status' => 'open',
                'comment_count' => 0,
                'type' => 'post',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'admin_id' => 2,
                'status' => 'publish',
                'comment_status' => 'open',
                'comment_count' => 0,
                'type' => 'post',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
        $this->assertTrue($posts);
    }

    public function testGetArchives(): void
    {
        $archives = $this->repository->getArchives(100, 0, 'month', 'post');

        $this->assertNotNull($archives);

        $this->assertInstanceOf(Collection::class, $archives['list']);

        $this->assertIsInt($archives['total']);
        $this->assertIsInt($archives['page']);
        $this->assertIsInt($archives['limit']);
    }


    public function testGetAll(): void
    {
        $result = $this->repository->getAll();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertIsArray($result['list']);
        $this->assertIsInt($result['total']);
    }


    public function testGet(): void
    {
        $post = $this->repository->get(71);
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