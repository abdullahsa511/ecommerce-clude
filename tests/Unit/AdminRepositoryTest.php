<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Post;
use App\Core\Models\Admin\Admin;
use App\Core\Repositories\Admin\AdminRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use PDO;

class AdminRepositoryTest extends TestCase
{
    private AdminRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Admin $admin;


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Post model
        $this->admin = new Admin();
        $this->admin->setDb($this->db);
        $this->repository = $this->container->make(AdminRepositoryInterface::class);
    }

    public function testFindByUsername(): void
    {
        $admin = $this->repository->findByUsername('shofiul');
        $this->assertNotNull($admin);
        $this->assertEquals('shofiul', $admin->username);
    }

    public function testFindByEmail(): void
    {
        $admin = $this->repository->findByEmail('shofiul@krost.com.au');
        $this->assertNotNull($admin);
        $this->assertEquals('shofiul@krost.com.au', $admin->email);
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