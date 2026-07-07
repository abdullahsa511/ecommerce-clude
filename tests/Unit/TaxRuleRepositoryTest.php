<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Tax\TaxRule;
use App\Core\Repositories\Tax\TaxRuleRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class TaxRuleRepositoryTest extends TestCase
{
    private TaxRuleRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newTaxRule = [
        "tax_rule_id" => 4,
        "tax_type_id" => 1,
        "tax_rate_id" => 1,
        "based" => "shipping",
        "priority" => 1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxRuleRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newTaxRule);
        $this->assertInstanceOf(TaxRule::class, $result);
        $this->assertEquals($this->newTaxRule['tax_type_id'], $result->tax_type_id);
        $this->assertEquals($this->newTaxRule['tax_rate_id'], $result->tax_rate_id);
        $this->assertEquals($this->newTaxRule['based'], $result->based);
        $this->assertEquals($this->newTaxRule['priority'], $result->priority);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1);
        $this->assertInstanceOf(TaxRule::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['priority' => 2];
        $result = $this->repository->update(1, $updateData);
        $this->assertInstanceOf(TaxRule::class, $result);
        $this->assertEquals($updateData['priority'], $result->priority);
    }

    public function testDelete(): void
    {
        $result = $this->repository->delete(1);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 