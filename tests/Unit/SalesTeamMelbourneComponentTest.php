<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Components\Salesteammelbourne;
use App\Core\Models\Component\ComponentData;

class SalesTeamMelbourneComponentTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private Salesteammelbourne $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->component = $this->container->make(Salesteammelbourne::class);
    }

    public function testResults(): void
    {
        $result = $this->component->results();
        $this->assertInstanceOf(ComponentData::class, $result);
        $this->assertObjectHasProperty('section_title', $result);
        $this->assertIsString($result->section_title);
        $this->assertObjectHasProperty('section_subtitle', $result);
        $this->assertIsString($result->section_subtitle);
        $this->assertObjectHasProperty('items', $result);
        $this->assertIsArray($result->items);
    }


    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 