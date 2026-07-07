<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Setting\SettingContent;
use App\Core\Repositories\Setting\SettingContentRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PDO;

class SettingContentTest extends TestCase
{
    private SettingContentRepositoryInterface $settingContentRepository;
    private SiteRepositoryInterface $siteRepository;
    private ?PDO $db = null;
    private KernelCli $kernel;

    // Test data
    private array $siteData;
    private array $settingContentData;
    private array $settingContentDataToDelete;

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

        $this->settingContentData = [
            'namespace' => 'test',
            'key' => 'test_content',
            'value' => json_encode(['content' => 'Test content value']),
            'language_id' => 1
        ];

        $this->settingContentDataToDelete = [
            'namespace' => 'test',
            'key' => 'content_to_delete',
            'value' => json_encode(['content' => 'Content to delete']),
            'language_id' => 1
        ];
        
        // Initialize the CLI kernel
        $this->kernel = new KernelCli();
        
        // Get dependencies from the container
        $this->db = $this->kernel->getContainer()->make(PDO::class);
        $this->settingContentRepository = $this->kernel->getContainer()->make(SettingContentRepositoryInterface::class);
        $this->siteRepository = $this->kernel->getContainer()->make(SiteRepositoryInterface::class);

        // Clean up any existing test data
        $this->cleanupTestData();
    }

    private function cleanupTestData(): void
    {
        // First delete setting contents
        $site = $this->siteRepository->findByHost($this->siteData['host']);
        if ($site) {
            $this->settingContentRepository->delete(
                $site->site_id,
                $this->settingContentData['language_id'],
                $this->settingContentData['namespace'],
                $this->settingContentData['key']
            );
            $this->settingContentRepository->delete(
                $site->site_id,
                $this->settingContentDataToDelete['language_id'],
                $this->settingContentDataToDelete['namespace'],
                $this->settingContentDataToDelete['key']
            );
            $this->siteRepository->delete($site->site_id);
        }
    }

    public function testCreateAndFindSettingContent(): void
    {
        // First create a site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Create setting content data with site_id
        $settingContentData = array_merge($this->settingContentData, ['site_id' => $site->site_id]);
        
        // Create a test setting content
        $settingContent = $this->settingContentRepository->create($settingContentData);
        $this->assertNotNull($settingContent);

        // Find the setting content
        $foundSettingContent = $this->settingContentRepository->findBySiteAndKey(
            $site->site_id,
            $this->settingContentData['language_id'],
            $this->settingContentData['namespace'],
            $this->settingContentData['key']
        );
        
        // Assert the setting content was found and has correct data
        $this->assertNotNull($foundSettingContent);
        $this->assertEquals($this->settingContentData['value'], $foundSettingContent->value);
        $this->assertEquals($this->settingContentData['language_id'], $foundSettingContent->language_id);

        // Test finding all setting contents for the site
        $siteSettingContents = $this->settingContentRepository->findBySite($site->site_id);
        $this->assertCount(1, $siteSettingContents);

        // Test finding setting contents by language
        $languageSettingContents = $this->settingContentRepository->findByLanguage(
            $site->site_id,
            $this->settingContentData['language_id']
        );
        $this->assertCount(1, $languageSettingContents);

        // Test finding setting contents by namespace
        $namespaceSettingContents = $this->settingContentRepository->findByNamespace(
            $site->site_id,
            $this->settingContentData['language_id'],
            $this->settingContentData['namespace']
        );
        $this->assertCount(1, $namespaceSettingContents);

        // Cleanup
        $this->cleanupTestData();
    }

    public function testUpdateSettingContent(): void
    {
        // First create a site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Create setting content data with site_id
        $settingContentData = array_merge($this->settingContentData, ['site_id' => $site->site_id]);
        
        // Create a test setting content
        $settingContent = $this->settingContentRepository->create($settingContentData);
        $this->assertNotNull($settingContent);

        // Update setting content data
        $updateData = [
            'value' => json_encode(['content' => 'Updated content value'])
        ];
        
        // Update the setting content
        $updated = $this->settingContentRepository->updateBySiteAndKey(
            $site->site_id,
            $this->settingContentData['language_id'],
            $this->settingContentData['namespace'],
            $this->settingContentData['key'],
            $updateData
        );

        // Get the updated setting content
        $updatedSettingContent = $this->settingContentRepository->findBySiteAndKey(
            $site->site_id,
            $this->settingContentData['language_id'],
            $this->settingContentData['namespace'],
            $this->settingContentData['key']
        );

        // Assert the changes
        $this->assertTrue($updated);
        $this->assertEquals($updateData['value'], $updatedSettingContent->value);

        // Cleanup
        $this->cleanupTestData();
    }

    public function testDeleteSettingContent(): void
    {
        // First create a site
        $site = $this->siteRepository->create($this->siteData);
        $this->assertNotNull($site);

        // Create setting content data with site_id
        $settingContentData = array_merge($this->settingContentDataToDelete, ['site_id' => $site->site_id]);
        
        // Create a test setting content
        $settingContent = $this->settingContentRepository->create($settingContentData);
        $this->assertNotNull($settingContent);

        // Delete the setting content
        $deleted = $this->settingContentRepository->deleteBySiteAndKey(
            $site->site_id,
            $this->settingContentDataToDelete['language_id'],
            $this->settingContentDataToDelete['namespace'],
            $this->settingContentDataToDelete['key']
        );

        // Try to find the deleted setting content
        $deletedSettingContent = $this->settingContentRepository->findBySiteAndKey(
            $site->site_id,
            $this->settingContentDataToDelete['language_id'],
            $this->settingContentDataToDelete['namespace'],
            $this->settingContentDataToDelete['key']
        );

        // Assert the deletion
        $this->assertTrue($deleted);
        $this->assertNull($deletedSettingContent);

        // Cleanup
        $this->cleanupTestData();
    }

    public function testSettingContentNotFound(): void
    {
        // Try to find a non-existent setting content
        $settingContent = $this->settingContentRepository->findBySiteAndKey(99999, 1, 'test', 'non_existent');
        $this->assertNull($settingContent);

        // Test finding setting contents for non-existent site
        $siteSettingContents = $this->settingContentRepository->findBySite(99999);
        $this->assertEmpty($siteSettingContents);

        // Test finding setting contents for non-existent language
        $languageSettingContents = $this->settingContentRepository->findByLanguage(99999, 999);
        $this->assertEmpty($languageSettingContents);

        // Test finding setting contents for non-existent namespace
        $namespaceSettingContents = $this->settingContentRepository->findByNamespace(99999, 1, 'non_existent');
        $this->assertEmpty($namespaceSettingContents);
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