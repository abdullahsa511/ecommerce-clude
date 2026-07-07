<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Components\Contactus;
use App\Core\Models\Component\ComponentData;

class ContactUsComponentTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private Contactus $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->component = $this->container->make(Contactus::class);
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