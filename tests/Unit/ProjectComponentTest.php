<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Components\Featuredprojectslider;

class ProjectComponentTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private Featuredprojectslider $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->component = $this->container->make(Featuredprojectslider::class);
    }

    public function testResults(): void
    {
        $result = $this->component->results();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('sectionTitle', $result);
        $this->assertArrayHasKey('sectionSubtitle', $result);
        $this->assertArrayHasKey('items', $result);
    }


    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 