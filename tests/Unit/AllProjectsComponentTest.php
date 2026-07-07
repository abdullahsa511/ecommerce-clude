<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Components\Allprojects;
use App\Core\Models\Component\ComponentData;

class AllProjectsComponentTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private Allprojects $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->component = $this->container->make(Allprojects::class);
    }

    public function testResults(): void
    {
        $result = $this->component->results();
        $this->assertInstanceOf(ComponentData::class, $result);
        $this->assertObjectHasProperty('sectionTitle', $result);
        $this->assertIsString($result->sectionTitle);
        $this->assertObjectHasProperty('sectionSubtitle', $result);
        $this->assertIsString($result->sectionSubtitle);
        $this->assertObjectHasProperty('items', $result);
        $this->assertIsArray($result->items);
        $this->assertObjectHasProperty('loadBtn', $result);
        $this->assertIsString($result->loadBtn);
    }


    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 