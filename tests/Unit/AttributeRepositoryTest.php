<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Post;
use App\Core\Models\Admin\Admin;
use App\Core\Models\Attribute\Attribute;
use App\Core\Repositories\Attribute\AttributeRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use PDO;

class AttributeRepositoryTest extends TestCase
{
    private AttributeRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Attribute $attribute;


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Post model
        $this->attribute = new Attribute();
        $this->attribute->setDb($this->db);
        $this->repository = $this->container->make(AttributeRepositoryInterface::class);
    }

    public function testGetAll(): void
    {
        $attributes = $this->repository->getAll(1);
        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('query', $attributes);
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