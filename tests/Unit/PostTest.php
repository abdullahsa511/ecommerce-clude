<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Post\Post;
use App\Core\Models\Admin\Admin;
use App\Core\ModelsFilters\PostFilter;
use App\Core\Repositories\Post\PostRepository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use PDO;

class PostTest extends TestCase
{
    private PostRepository $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Post $post;

    // Test data properties
    private array $postData;
    private array $postContentData;
    private array $postDataToDelete;
    private array $currentPostData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize test data
        $this->postData = [
            'status' => 'publish',
            'type' => 'post',
            'admin_id' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->postContentData = [
            [
                'language_id' => 1,
                'name' => 'Test Post',
                'content' => 'Test Content',
                'meta_keywords' => 'Test Meta Title',
                'meta_description' => 'Test Meta Description'
            ]
        ];

        $this->postDataToDelete = [
            'status' => 'publish',
            'type' => 'post',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->currentPostData = [
            'status' => 'draft',
            'type' => 'post',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Post model
        $this->post = new Post();
        $this->post->setDb($this->db);
    }

    private function cleanupTestData(): void
    {
        // Delete test posts by title
        $testTitles = [
            $this->postContentData[0]['name'],
            'Post To Delete',
            'Current Post'
        ];
        
        foreach ($testTitles as $title) {
            $filter = new PostFilter();
            $filter->search = $title;
            $posts = $this->repository->getAll($filter);
            foreach ($posts['items'] as $post) {
                $this->repository->deleteMultiple([$post->post_id]);
            }
        }
    }
    public function testHasOne(): void
    {
        $foundPost = $this->post->with(['admin'])->find(1);

        $query = $foundPost->getQuery();
        echo "\n";
        echo "\n";
        echo $query;

        // Assert: Check that the post is found and has the associated admin
        $this->assertNotEmpty($foundPost);
    }

    public function testHasMany(): void
    {
        $foundPosts = $this->post->with(['postContent'])->find(1);

        $query = $foundPosts->getQuery();
        echo "\n";
        echo "\n";
        echo $query;
        // Assert: Check that the post is found and has the associated admin
        $this->assertNotEmpty($query);
    }

    public function testBelongsToMany(): void 
    {
        $foundPosts = $this->post->with(['sites'])->find(1);

        $query = $foundPosts->getQuery();
        echo "\n";
        echo "\n";
        echo $query;
        // Assert: Check that the post is found and has the associated admin
        $this->assertNotEmpty($query);
    }

    public function testCreate(): void
    {
        $post = $this->post->create($this->postData);
        $query = $this->post->getQueryString();
        echo $query;
        $this->assertInstanceOf(Post::class, $post);
        $this->assertNotNull($post);
    }

    public function testInstert(): void
    {
        $this->postData = [
            [
                'status' => 'publish',
                'type' => 'post',
                'admin_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'status' => 'draft',
                'type' => 'post',
                'admin_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $result = $this->post->insert($this->postData);
        $query = $this->post->getQueryString();
        echo $query;
        $this->assertTrue($result);
    }

    public function testUpdate(): void
    {
        $update = [
            'status' => 'publish',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $result = $this->post->update(40, $update);
        $query = $this->post->getQueryString();
        echo $query;
        $this->assertTrue($result);
    }

    public function testDelete(): void
    {
        $result = $this->post->delete(39);
        $query = $this->post->getQueryString();   
        echo $query;
        $this->assertTrue($result);
    }
    public function testUpsert(): void
    {
        $data = [
            ['post_id' => 10, 'status' => 'private'],
            ['post_id' => 11, 'status' => 'private'],
            ['post_id' => 12, 'status' => 'private'],
        ];
        $result = $this->post->upsert($data, ['post_id']);
        $query = $this->post->getQueryString();
        echo $query;
        $this->assertTrue($result);
    }

    public function testDeleteMultiple(): void
    {
        $result = $this->post->deleteMultiple([10, 11, 12]);
        $query = $this->post->getQueryString();
        echo $query;
        $this->assertTrue($result);
    }

    public function testOrderBy(): void
    {
        $this->post->orderBy('status', 'DESC');
        $query = $this->post->getQuery();
        echo $query;
        $this->assertStringContainsString('ORDER BY post.status DESC', $query);
    }

    public function testLimit(): void
    {
        $this->post->limit(10);
        $query = $this->post->getQuery();
        echo $query;
        $this->assertStringContainsString('LIMIT 10', $query);
    }

    public function testOffset(): void
    {
        $this->post->offset(10);
        $query = $this->post->getQuery();
        echo $query;
        $this->assertStringContainsString('OFFSET 10', $query);
    }
    public function testGetPrimaryKey(): void
    {
        $this->assertEquals('post_id', $this->post->getPrimaryKey());
    }
    public function testGetTable(): void
    {
        $this->assertEquals('post', $this->post->getTable());
    }
    public function testGetTableAlias(): void
    {
        $this->post->setTableAlias('p');
        $this->assertEquals('p', $this->post->getTableAlias());
    }
    public function testGetPrepareKeys(): void
    {
        $result = $this->post->prepareKeys(Admin::class, 'admin_id', 'id');
        echo $result;
        $this->assertIsArray($result);
        $this->assertCount(6, $result);
    }
    public function testGetPrepareBelongKeys(): void
    {
        $result = $this->post->prepareBelongKeys(Admin::class, 'admin_id', 'id');
        echo $result;
        $this->assertIsArray($result);
        $this->assertCount(6, $result);
    }
    public function testWhere(): void
    {
        $selectedPosts = $this->post->select(['post_id', 'status']);

        $query = $this->post->getQuery();
        echo $query;

        // Assert: Check that the selected posts are returned
        $this->assertNotEmpty($selectedPosts);
    }



    public function testFind(): void
    {
        $post = $this->post->find(1);
        $query = $this->post->getQuery();
        echo $query;
        $this->assertInstanceOf(Post::class, $post);
        $this->assertNotNull($post);
        $this->assertEquals(1, $post->post_id);
    }

    public function testFindAll(): void
    {
        $post = $this->post->findAll();

        $query = $this->post->getQuery();
        echo $query;
    }

    public function testFindBy(): void
    {
        // Act: Find the post by status
        $posts = $this->post->findBy(['status' => 'publish']);

        $query = $this->post->getQuery();

        echo $query;
        // Assert: Check that the post is found
        $this->assertNotEmpty($posts);
        $this->assertEquals($posts['post_id'], $posts[0]->post_id);

    }

    public function testFindOrFail(): void
    {
        $foundPost = $this->post->findOrFail(2);

        $query = $this->post->getQuery();

        echo $query;

        $this->assertNotNull($foundPost);
        $this->assertEquals(2, $foundPost->post_id);
    }

    // public function testWhere(): void
    // {
    //     $this->post->where('status', '=', 'publish');
    //     $query = $this->post->getQuery();
    //     echo $query;
    //     $this->assertStringContainsString('WHERE status = :status', $query);
    // }

    public function testOrWhere(): void
    {
        // Act: Use orWhere to find posts
        $foundPosts = $this->post->where('status', '=', 'publish')
                                    ->orWhere('admin_id', '=', 1);

        $query = $this->post->getQuery();
        echo $query;

        // Assert: Check that both posts are found
        $this->assertCount(2, json_decode($query));

    }

    public function testWhereIn(): void
    {
        // Act: Use whereIn to find posts
        $foundPosts = $this->post->whereIn('status', ['publish', 'draft']);

        $query = $this->post->getQuery();
        echo $query;
        // Assert: Check that both posts are found
        // $this->assertCount(2, json_decode($foundPosts));

    }

    public function testWhereLike(): void
    {
        $foundPosts = $this->post->whereLike('title', 'Test');

        $query = $this->post->getQuery();
        echo $query;

        // Assert: Check that the post is found
        $this->assertNotEmpty($query);

    }

    public function testWhereNull(): void
    {
        $foundPosts = $this->post->whereNull('deleted_at')->getQuery();

        echo $foundPosts;
        // Assert: Check that the post is found
        $this->assertNotEmpty($foundPosts);
    }

    public function testGroupBy(): void
    {
        $groupedPosts = $this->post->groupBy('status');

        $query = $this->post->getQuery();

        echo $query;

        // Assert: Check that the posts are grouped correctly
        $this->assertNotEmpty($groupedPosts);
        
        // Check the count of posts in each group
        $this->assertCount(2, json_decode($query));

    }

    public function testJoin(): void
    {
        // Act: Use join to retrieve posts with their associated admin
        $foundPosts = $this->post->join('admins', 'posts.admin_id', '=', 'admins.admin_id')->getQuery();

        echo $foundPosts;

        // Assert: Check that the post is found and has the associated admin
        $this->assertNotEmpty($foundPosts);
    }

    public function testWith(): void 
    {
        $foundPosts = $this->post->with(['admin'])->find(1);

        $query = $this->post->getQuery();
        echo "\n";
        echo "\n";
        echo $query;

        // Call convertJsonObjectToModel to decode the JSON data
        $result = $foundPosts->convertJsonObjectToModel();

        echo $result;

        // Now you can access the admin object directly
        //$admin = $foundPosts->admin; // This should now be an instance of the Admin model

        // Assert: Check that the post is found and has the associated comments
        $this->assertNotEmpty($foundPosts);

    }
    public function testWithHasMany(): void 
    {
        $foundPosts = $this->post->with(['postContent'])->find(1);


        $query = $this->post->getQuery();
        echo "\n";
        echo "\n";
        echo $query;

        $result = $foundPosts->convertJsonArrayToModelCollection();

        echo $result;

        // Assert: Check that the post is found and has the associated comments
        $this->assertNotEmpty($foundPosts);

    }


    public function testBuildQuery(): void
    {
        $this->post->select(['post_id', 'title'])
        ->where('status', '=', 'publish')
        ->with(['content' => function($query){
            $query->select(['post_id', 'title']);
        }]);
        // ->orderBy('post_id', 'DESC')
        // ->limit(10)
        // ->offset(0);
        $query = $this->post->getQuery();
        echo $query;
        $this->assertStringContainsString('SELECT post_id', $query);

    }

    protected function tearDown(): void
    {
        // Clean up any remaining test data
        $this->cleanupTestData();
        
        // Close the database connection
        $this->db = null;
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 