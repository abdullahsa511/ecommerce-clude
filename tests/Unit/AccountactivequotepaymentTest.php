<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use App\Core\Components\Accountactivequotepayment;
use App\Core\Models\Component\ComponentData;

class AccountactivequotepaymentTest extends TestCase
{
    private KernelCli $kernel;
    private Container $container;
    private Accountactivequotepayment $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->component = $this->container->make(Accountactivequotepayment::class);
    }

    public function testResults(): void
    {
        $result = $this->component->results();
        $this->assertArrayHasKey('section_title', $result);
        $this->assertArrayHasKey('section_subtitle', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('load_btn', $result);
        $this->assertArrayHasKey('payment_methods', $result);
        $this->assertArrayHasKey('order_number', $result);
        $this->assertArrayHasKey('sub_total', $result);
    }


    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 