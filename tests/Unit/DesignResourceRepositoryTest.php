<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Repositories\Design\DesignResourceRepository;
use App\Core\Repositories\Design\DesignResourceRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class DesignResourceRepositoryTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private DesignResourceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(DesignResourceRepositoryInterface::class);
    }

    public function testImportFinishesMethodExists(): void
    {
        $this->assertTrue(method_exists($this->repository, 'importFinishes'));
    }

    public function testImportFinishesMethodSignature(): void
    {
        $reflection = new \ReflectionMethod($this->repository, 'importFinishes');
        $this->assertEquals('importFinishes', $reflection->getName());
        $this->assertEquals('string', $reflection->getParameters()[0]->getType()->getName());
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
}
