<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Setting\Setting;
use App\Core\Repositories\SettingRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PDO;

class SettingTest extends TestCase
{
    private SettingRepositoryInterface $settingRepository;
    private SiteRepositoryInterface $siteRepository;
    private ?PDO $db = null;
    private KernelCli $kernel;

    // Test data
    private array $siteData;
    private array $settingData;
    private array $settingDataToDelete;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize test data
        $this->siteData = [
            'key' => 'test-site',
            'name' => 'Test Site',
            'host' => 'test.example.com',
            'theme' => 'default',
            'template' => 'standard'
        ];

        $this->settingData = [
            'namespace' => 'test',
            'key' => 'test_setting',
            'value' => json_encode(['enabled' => true])
        ];

        $this->settingDataToDelete = [
            'namespace' => 'test',
            'key' => 'setting_to_delete',
            'value' => json_encode(['enabled' => false])
        ];
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        
        // Get dependencies from the container
        $this->db = $this->kernel->getContainer()->make(PDO::class);
        $this->settingRepository = $this->kernel->getContainer()->make(SettingRepositoryInterface::class);
        $this->siteRepository = $this->kernel->getContainer()->make(SiteRepositoryInterface::class);

        // Clean up any existing test data
        $this->cleanupTestData();
    }

    private function cleanupTestData(): void
    {
        // First delete settings
        $site = $this->siteRepository->findByHost($this->siteData['host']);
        if ($site) {
            $this->siteRepository->delete($site->site_id);
        }
    }

    public function testCreateAndFindSetting(): void
    {
        // First create a site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Create setting data with site_id
        $settingData = array_merge($this->settingData, ['site_id' => $site->site_id]);
        
        // Create a test setting
        $setting = $this->settingRepository->create($settingData);
        $this->assertNotNull($setting);

        // Find the setting
        $foundSetting = $this->settingRepository->findBySiteAndKey(
            $site->site_id,
            $this->settingData['namespace'],
            $this->settingData['key']
        );
        
        // Assert the setting was found and has correct data
        $this->assertNotNull($foundSetting);
        $this->assertEquals($this->settingData['value'], $foundSetting->value);

        // Test finding all settings for the site
        $siteSettings = $this->settingRepository->findBySite($site->site_id);
        $this->assertCount(1, $siteSettings);

        // Test finding settings by namespace

        // Cleanup
        $this->cleanupTestData();
    }

    public function testUpdateSetting(): void
    {
        // First create a site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Create setting data with site_id
        $settingData = array_merge($this->settingData, ['site_id' => $site->site_id]);
        
        // Create a test setting
        $setting = $this->settingRepository->create($settingData);
        $this->assertNotNull($setting);

        // Update setting data
        $updateData = [
            'value' => json_encode(['enabled' => false])
        ];
        
        // Update the setting
        // $updated = $this->settingRepository->updateBySiteAndKey(
        //     $site->site_id,
        //     $this->settingData['namespace'],
        //     $this->settingData['key'],
        //     $updateData
        // );

        // Get the updated setting
        $updatedSetting = $this->settingRepository->findBySiteAndKey(
            $site->site_id,
            $this->settingData['namespace'],
            $this->settingData['key']
        );

        // Assert the changes
        // $this->assertTrue($updated);
        $this->assertEquals($updateData['value'], $updatedSetting->value);

        // Cleanup
        $this->cleanupTestData();
    }

    public function testDeleteSetting(): void
    {
        // First create a site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Create setting data with site_id
        $settingData = array_merge($this->settingDataToDelete, ['site_id' => $site->site_id]);
        
        // Create a test setting
        $setting = $this->settingRepository->create($settingData);
        $this->assertNotNull($setting);

        // Delete the setting
        // $deleted = $this->settingRepository->deleteBySiteAndKey(
        //     $site->site_id,
        //     $this->settingDataToDelete['namespace'],
        //     $this->settingDataToDelete['key']
        // );

        // Try to find the deleted setting
        $deletedSetting = $this->settingRepository->findBySiteAndKey(
            $site->site_id,
            $this->settingDataToDelete['namespace'],
            $this->settingDataToDelete['key']
        );

        // Assert the deletion
        // $this->assertTrue($deleted);
        $this->assertNull($deletedSetting);

        // Cleanup
        $this->cleanupTestData();
    }

    public function testSettingNotFound(): void
    {
        // Try to find a non-existent setting
        $setting = $this->settingRepository->findBySiteAndKey(99999, 'test', 'non_existent');
        $this->assertNull($setting);

        // Test finding settings for non-existent site
        $siteSettings = $this->settingRepository->findBySite(99999);
        $this->assertEmpty($siteSettings);

        // Test finding settings for non-existent namespace
        // $namespaceSettings = $this->settingRepository->findByNamespace(99999, 'non_existent');
        // $this->assertEmpty($namespaceSettings);
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