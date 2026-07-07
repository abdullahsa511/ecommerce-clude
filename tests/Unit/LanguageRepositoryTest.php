<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Localisation\Language;
use App\Core\Repositories\Localisation\LanguageRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PDO;

class LanguageRepositoryTest extends TestCase
{
    private LanguageRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Language model
        $this->language = new Language();
        $this->language->setDb($this->db);
        $this->repository = $this->container->make(LanguageRepositoryInterface::class);
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
        $language = $this->repository->get(1);
        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals(1, $language->language_id);
    }



    public function testFind(): void
    {
        $language = $this->repository->find(1);
        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
    }

    public function testFindAll(): void
    {
        $languages = $this->repository->findAll();
        $this->assertNotNull($languages);
        $this->assertIsArray($languages);
        $this->assertNotEmpty($languages);
    }

    public function testCreate(): void
    {
        $language = $this->repository->create([
            'name' => 'Test Language',
            'code' => 'tl',
            'locale' => 'tl_PH',
            'rtl' => 0,
            'sort_order' => 1,
            'status' => 1,
        ]);
        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
    }

    public function testUpdate(): void
    {
        $language = $this->repository->update(6, [
            'name' => 'test updated',
            'code' => 'tl',
            'locale' => 'tl_PH',
            'rtl' => 0,
            'sort_order' => 1,
            'status' => 1,
        ]);

        // $this->assertTrue($post);
        
        $language = $this->repository->get(6);
        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('test updated', $language->name);
    }

    public function testInsertMultiple(): void
    {
        $languages = $this->repository->insertMultiple([
            [
                'name' => 'test 2',
                'code' => 'tl',
                'locale' => 'tl_PH',
                'rtl' => 0,
                'sort_order' => 1,
                'status' => 1,
            ],
            [
                'name' => 'test 3',
                'code' => 'tl',
                'locale' => 'tl_PH',
                'rtl' => 0,
                'sort_order' => 1,
                'status' => 1,
            ]
        ]);
        $this->assertTrue($languages);
    }

    public function testDelete(): void
    {
        $language = $this->repository->find(6);
        $this->assertNotNull($language);
        $language_id = $language->language_id;
        $deleted = $this->repository->delete($language_id);
        $this->assertTrue($deleted);
    }

    public function testDeleteMultiple(): void
    {
        $deleted = $this->repository->deleteMultiple([5, 7]);
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