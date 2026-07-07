<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PDO;

class SiteTest extends TestCase
{
    private SiteRepositoryInterface $siteRepository;
    private ?PDO $db = null;
    private KernelCli $kernel;

    // Test data
    private array $siteData;
    private array $siteDataToDelete;
    private array $currentSiteData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize test data
        $this->siteData = [
            'key' => 'test-site',
            'name' => 'Test Site',
            'host' => 'test.example.com',
            'theme' => 'default',
            'template' => 'standard',
            'settings' => json_encode(['maintenance_mode' => false])
        ];

        $this->siteDataToDelete = [
            'key' => 'site-to-delete',
            'name' => 'Site To Delete',
            'host' => 'delete.example.com',
            'theme' => 'default',
            'template' => 'standard',
            'settings' => json_encode(['maintenance_mode' => true])
        ];

        $this->currentSiteData = [
            'key' => 'current-site',
            'name' => 'Current Site',
            'host' => 'current.example.com',
            'theme' => 'modern',
            'template' => 'custom',
            'settings' => json_encode(['maintenance_mode' => false])
        ];
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        
        // Get dependencies from the container
        $this->db = $this->kernel->getContainer()->make(PDO::class);
        $this->siteRepository = $this->kernel->getContainer()->make(SiteRepositoryInterface::class);

        // Clean up any existing test data
        $this->cleanupTestData();
    }

    private function cleanupTestData(): void
    {
        $testHosts = [
            $this->siteData['host'],
            $this->siteDataToDelete['host'],
            $this->currentSiteData['host']
        ];
        foreach ($testHosts as $host) {
            $site = $this->siteRepository->findByHost($host);
            if ($site) {
                $this->siteRepository->delete($site->site_id);
            }
        }
    }

    public function testCreateAndFindSite(): void
    {
        // Create a test site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Find the site by ID
        $foundSite = $this->siteRepository->find($site->site_id);
        $this->assertNotNull($foundSite);
        $this->assertEquals($this->siteData['name'], $foundSite->name);
        $this->assertEquals($this->siteData['host'], $foundSite->host);

        // Find the site by key
        $foundByKey = $this->siteRepository->findByKey($this->siteData['key']);
        $this->assertNotNull($foundByKey);
        $this->assertEquals($site->site_id, $foundByKey->site_id);

        // Find the site by host
        $foundByHost = $this->siteRepository->findByHost($this->siteData['host']);
        $this->assertNotNull($foundByHost);
        $this->assertEquals($site->site_id, $foundByHost->site_id);

        // Cleanup
        $this->siteRepository->delete($site->site_id);
    }

    public function testUpdateSite(): void
    {
        // Create a test site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Update site data
        $updateData = [
            'name' => 'Updated Test Site',
            'theme' => 'modern'
        ];
        
        // Update the site
        $updated = $this->siteRepository->update($site->site_id, $updateData);
        $updatedSite = $this->siteRepository->find($site->site_id);

        // Assert the changes
        $this->assertTrue($updated);
        $this->assertEquals('Updated Test Site', $updatedSite->name);
        $this->assertEquals('modern', $updatedSite->theme);
        $this->assertEquals($this->siteData['host'], $updatedSite->host);

        // Cleanup
        $this->siteRepository->delete($site->site_id);
    }

    public function testDeleteSite(): void
    {
        // Create a test site
        $site = $this->siteRepository->create($this->siteDataToDelete);
        $this->assertNotNull($site);

        // Delete the site
        $deleted = $this->siteRepository->delete($site->site_id);
        $deletedSite = $this->siteRepository->find($site->site_id);

        // Assert the deletion
        $this->assertTrue($deleted);
        $this->assertNull($deletedSite);
    }

    public function testSiteNotFound(): void
    {
        // Try to find a non-existent site
        $site = $this->siteRepository->find(99999);
        $this->assertNull($site);

        $siteByKey = $this->siteRepository->findByKey('non-existent-key');
        $this->assertNull($siteByKey);

        $siteByHost = $this->siteRepository->findByHost('non-existent.example.com');
        $this->assertNull($siteByHost);
    }

    protected function tearDown(): void
    {
        // Clean up any remaining test data
        $this->cleanupTestData();
        
        // Close the database connection
        $this->db = null;
        
        // Reset the kernel for the next test
        $this->kernel->reset();
        
        parent::tearDown();
    }
} 