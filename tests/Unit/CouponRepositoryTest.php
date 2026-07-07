<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Cart\Coupon;
use App\Core\Repositories\Cart\CouponRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class CouponRepositoryTest extends TestCase
{
    private CouponRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Coupon model
        $this->coupon = new Coupon();
        $this->coupon->setDb($this->db);
        $this->repository = $this->container->make(CouponRepositoryInterface::class);
    }

    public function testGetAll(): void
    {
        $result = $this->repository->getAll();
        $this->assertInstanceOf(Collection::class, $result['items']);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('total_pages', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertArrayHasKey('per_page', $result);
        $this->assertIsInt($result['total']);
        $this->assertIsInt($result['total_pages']);
        $this->assertIsInt($result['current_page']);
        $this->assertIsInt($result['per_page']);
    }

    public function testGet(): void
    {
        $coupon = $this->repository->get(1);
        $this->assertNotNull($coupon);
        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertEquals(1, $coupon->coupon_id);
    }

    

    public function testFind(): void
    {
        $coupon = $this->repository->find(1);
        $this->assertNotNull($coupon);
        $this->assertInstanceOf(Coupon::class, $coupon);
    }

    public function testFindAll(): void
    {
        $coupons = $this->repository->findAll();
        $this->assertNotNull($coupons);
        $this->assertIsArray($coupons);
        $this->assertNotEmpty($coupons);
    }

    // public function testCreate(): void
    // {
    //     $coupon = $this->repository->create([
    //         'name' => 'test coupon',
    //         'code' => 'TEST123',
    //         'type' => 'P',
    //         'discount' => 10.00,
    //         'total' => 100.00,
    //         'limit' => 1,
    //         'limit_user' => '1',
    //         'logged_in' => 1,
    //         'free_shipping' => 1,
    //         'status' => 1,
    //         'created_at' => '2021-01-01 00:00:00',
    //         'updated_at' => '2021-01-01 00:00:00'
    //     ]);
    //     $this->assertNotNull($coupon);
    //     $this->assertInstanceOf(Coupon::class, $coupon);
    // }

    public function testUpdate(): void
    {
        $coupon = $this->repository->update(1, [
            'name' => 'test updated',
            'status' => 1,
        ]);

        $coupon = $this->repository->get(1);
        $this->assertNotNull($coupon);
        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertEquals('test updated', $coupon->name);
    }

    // public function testInsertMultiple(): void
    // {
    //     $coupons = $this->repository->insertMultiple([
    //         [
    //             'name' => 'test coupon 1',
    //             'code' => 'TEST1',
    //             'type' => 'percentage',
    //             'discount' => 10,
    //             'status' => 1,
    //         ],
    //         [
    //             'name' => 'test coupon 2',
    //             'code' => 'TEST2',
    //             'type' => 'fixed',
    //             'discount' => 20,
    //             'status' => 1,
    //         ]
    //     ]);
    //     $this->assertTrue($coupons);
    // }

    public function testDelete(): void
    {
        $coupon = $this->repository->find(3);
        $this->assertNotNull($coupon);
        $coupon_id = $coupon->coupon_id;
        $deleted = $this->repository->delete($coupon_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([8, 9]);
        $this->assertIsInt($deleted);
        $this->assertEquals(2, $deleted);
    }

    protected function tearDown(): void
    {
        // Close the database connection
        $this->db = null;
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 