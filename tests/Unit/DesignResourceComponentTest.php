<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Order\ReturnStatus;
use App\Core\Repositories\Order\ReturnStatusRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Components\Designresources;
use App\Core\Models\Component\ComponentData;

class DesignResourceComponentTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private Designresources $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->component = $this->container->make(Designresources::class);
    }

    public function testResults(): void
    {
        $result = $this->component->results();
        $this->assertInstanceOf(ComponentData::class, $result);
        $this->assertObjectHasProperty('section_title', $result);
        $this->assertObjectHasProperty('section_subtitle', $result);
        $this->assertObjectHasProperty('items', $result);
        $this->assertIsArray($result->items);
    }


    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 