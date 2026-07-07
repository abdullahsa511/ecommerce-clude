<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\User\UserAddress;
use App\Core\Repositories\User\UserAddressRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class UserAddressRepositoryTest extends TestCase
{
    private UserAddressRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private array $newUserAddress = [
        "user_address_id" => 3,
        "user_id" => 1,
        "first_name" => "John",
        "last_name" => "Doe",
        "company" => "Company Inc.",
        "address_1" => "123 Main St",
        "address_2" => "Apt 4B",
        "country_id" => 1,
        "region_id" => 1,
        "city" => "Metropolis",
        "post_code" => "12345",
        "default_address" => 1,
        "fields" => "{}"
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(UserAddressRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $result = $this->repository->create($this->newUserAddress);
        $this->assertInstanceOf(UserAddress::class, $result);
        $this->assertEquals($this->newUserAddress['first_name'], $result->first_name);
        $this->assertEquals($this->newUserAddress['last_name'], $result->last_name);
    }

    public function testFind(): void
    {
        $result = $this->repository->find(1);
        $this->assertInstanceOf(UserAddress::class, $result);
    }

    public function testFindAll(): void
    {
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
    }

    public function testUpdate(): void
    {
        $updateData = ['city' => 'Gotham'];
        $result = $this->repository->update(1, $updateData);
        $this->assertInstanceOf(UserAddress::class, $result);
        $this->assertEquals($updateData['city'], $result->city);
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