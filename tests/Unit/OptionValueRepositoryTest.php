<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Option\OptionValue;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Repositories\Option\OptionValueRepositoryInterface;

class OptionValueRepositoryTest extends TestCase
{
    private OptionValueRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $newOptionValue = [
        "option_value_id" => 1,
        "option_id" => 1,
        "image" => "Product",
        "sort_order" => 1
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OptionValueRepositoryInterface::class);
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
        $result = $this->repository->create($this->newOptionValue);
        $this->assertInstanceOf(OptionValue::class, $result);
    }
    public function testFind(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(OptionValue::class, $result);
    }
    public function testFindAll(){
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }
    public function testUpdate(){
        $result = $this->repository->update($this->newOptionValue['option_value_id'], ['value' => 'TE']);
        $this->assertInstanceOf(OptionValue::class, $result);
        $this->assertEquals('TE', $result->value);
    }
    public function testDelete(){
        $result = $this->repository->delete($this->newOptionValue['option_value_id']);
        $this->assertTrue($result);
    }

    
    

    protected function tearDown(): void
    {
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 