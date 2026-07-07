<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Option\Option;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Repositories\Option\OptionRepositoryInterface;

class OptionRepositoryTest extends TestCase
{
    private OptionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newField = [
        "option_id" => 15,
        "type" => "Product",
        "sort_order" => 15
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OptionRepositoryInterface::class);
    }
    // public function testGetAll(): void
    // {
    //     $result = $this->repository->getAll();
        
    //     $this->assertIsArray($result);
    //     $this->assertInstanceOf(Collection::class, $result['items']);
    //     $this->assertIsInt($result['total']);
    //     $this->assertIsInt($result['total_pages']);
    //     $this->assertIsInt($result['current_page']);
    //     $this->assertIsInt($result['per_page']);
    // }
    public function testCreate(){
        $result = $this->repository->create($this->newField);
        $this->assertInstanceOf(Option::class, $result);
    }
    public function testFind(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(Option::class, $result);
    }
    public function testFindAll(){
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }
    public function testUpdate(){
        $result = $this->repository->update($this->newField['field_id'], ['value' => 'TE']);
        $this->assertInstanceOf(Option::class, $result);
        $this->assertEquals('TE', $result->value);
    }
    public function testDelete(){
        $result = $this->repository->delete($this->newField['field_id']);
        $this->assertTrue($result);
    }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 