<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Post\Comment;
use App\Core\ModelsFilters\PostFilter;
use App\Core\Repositories\Post\CommentRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class CommentRepositoryTest extends TestCase
{
    private CommentRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Comment $comment;


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Post model
        $this->comment = new Comment();
        $this->comment->setDb($this->db);
        $this->repository = $this->container->make(CommentRepositoryInterface::class);
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

    public function testFind(): void
    {
        $comment = $this->repository->find(1);
        $this->assertNotNull($comment);
        $this->assertInstanceOf(Comment::class, $comment);
    }

    public function testFindAll(): void
    {
        $comments = $this->repository->findAll();
        $this->assertNotNull($comments);
        $this->assertIsArray($comments);
        $this->assertNotEmpty($comments);
    }

    public function testCreate(): void
    {
        $comment = $this->repository->create([
            'post_id' => 3,
            'user_id' => 1,
            'author' => 'test author',
            'email' => 'test@test.com',
            'url' => 'https://test.com',
            'ip' => '127.0.0.1',
            'content' => 'test comment',
            'status' => 1,
            'votes' => 0,
            'type' => 'comment',
            'parent_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->assertNotNull($comment);
        $this->assertInstanceOf(Comment::class, $comment);
    }

    public function testUpdate(): void
    {
        $comment = $this->repository->update(2, [
            'content' => 'test',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // $this->assertTrue($post);
        
        $comment = $this->repository->get(2);
        $this->assertNotNull($comment);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('test', $comment->content);
    }

    public function testDelete(): void
    {
        $comment = $this->repository->find(4);
        $this->assertNotNull($comment);
        $comment_id = $comment->comment_id;
        $deleted = $this->repository->delete($comment_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([2, 3]);
        $this->assertIsInt($deleted);
        $this->assertEquals(2, $deleted);
    }

    public function testInsertMultiple(): void
    {
        $posts = $this->repository->insertMultiple([
            [
                'post_id' => 3,
                'user_id' => 1,
                'author' => 'test author',
                'email' => 'test@test.com',
                'url' => 'https://test.com',
                'ip' => '127.0.0.1',
                'content' => 'test comment',
                'status' => 1,
                'votes' => 0,
                'type' => 'comment',
                'parent_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'post_id' => 4,
                'user_id' => 1,
                'author' => 'test author',
                'email' => 'test2@test.com',
                'url' => 'https://test.com',
                'ip' => '127.0.0.1',
                'content' => 'test comment',
                'status' => 1,
                'votes' => 1,
                'type' => 'comment',
                'parent_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
        $this->assertTrue($posts);
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