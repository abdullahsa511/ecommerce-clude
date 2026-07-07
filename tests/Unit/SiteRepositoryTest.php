<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Site\Site;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PDO;

class SiteRepositoryTest extends TestCase
{
    private SiteRepositoryInterface $repository;
    private ?PDO $db = null;
    private KernelCli $kernel;
    private Container $container;
    private Site $site;


    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        
        // Get dependencies from the container
        $this->db = $this->container->make(PDO::class);
        
        // Initialize Site model
        $this->site = new Site();
        $this->site->setDb($this->db);
        $this->repository = $this->container->make(SiteRepositoryInterface::class);
    }


    public function testGetAll(): void 
    {
        $testArray = [1, 2, 3, 4];
        $site = $this->repository->getAll(0, 20, $testArray);
        $this->assertNotNull($site);
    }

    public function testGetData(): void 
    {
        $siteData = $this->repository->getData(1, 1, 1);
        print_r ($siteData);
    }
    
    public function testGetSiteData(): void 
    {
        $site = $this->repository->find(1); // Assuming we want to get the site with ID 1
        $siteData = $this->repository->getSiteData($site);
    }
    

    public function testGetCountries(): void 
    {
        $countries = $this->repository->getCountries();

        echo $countries;
        $this->assertInstanceOf(Collection::class, $countries);
    }
    public function testGetRegions(): void 
    {
        $regions = $this->repository->getRegions(1);

        echo $regions;

        $this->assertIsArray($regions);
    }

    public function testGetLanguages(): void 
    {
        $languages = $this->repository->getLanguages();

        echo $languages;

        $this->assertInstanceOf(Collection::class, $languages);
    }
    public function testGetCurrencies(): void 
    {
        $currencies = $this->repository->getCurrencies();

        echo $currencies;

        $this->assertInstanceOf(Collection::class, $currencies);
    }
    public function testGetOrderStatuses(): void 
    {
        $orderStatuses = $this->repository->getOrderStatuses(1);

        echo $orderStatuses;

        $this->assertIsArray($orderStatuses);
    }
    public function testGetWeightTypes(): void 
    {
        $weightTypes = $this->repository->getWeightTypes();

        echo $weightTypes;

        $this->assertInstanceOf(Collection::class, $weightTypes);
    }


    public function testFindByKey(): void 
    {
        $result = $this->repository->findByKey('test');
        echo $result;
    }
    public function testFindByHost(): void 
    {
        $result = $this->repository->findByHost('1234');
        echo $result;
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