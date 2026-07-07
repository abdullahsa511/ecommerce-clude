<?php

declare(strict_types=1);

namespace App\Core\Repositories\Project;

use App\Core\Models\Project\Project;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Project\ProjectData;
use Exception;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all countries with pagination and filtering
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 100
    ): array;

    /**
     * Get a single country by ID
     */
    public function get(int $projectId): ?Project;

    public function getBySlug(string $slug): ?Project;


    public function getFeaturedProjectSliderComponentData(array $param);

    public function getAllProjects(array $param);

    public function getProjectGallery(array $param);

    public function createProject(ProjectData $data): Project | Exception | null;

    public function showProject(int $projectId): Project | Exception | null;

    public function updateProject(ProjectData $data): Project | Exception | null;
    public function getProjectDetailMainComponentData(array $param);
     /** 
     * The modified methods started from here
     * 
    */

    public function getProjectDetailPenetratingComponentData(array $param);

    public function getProjectDetailsComponentData(array $param);

    public function getProjectGalleryComponentData(array $param);

    public function insertProjects(array $data): bool;


    public function getFeaturedProjectMasonryComponentData(array $param);

    public function getProjectDetailUnderHero(array $param);

    public function importProjects(string $csv_file): array;

    public function importProjectImages(string $csv_file): array;
    public function deleteProjectMainImage(string $path, string $property, int $project_id): bool; // project db table 
    public function deleteProjectImage(int $project_image_id): bool; // projcet image db table 
    public function insertProjectImages(array $data, int $project_id): array;
    public function updateProjectMainFeatureImage(array $data, string $property, int $project_id): bool;
    
    // public function getProjectListPaginationData(int $current_page, int $per_page);

    public function updateWayPoints(array $data): array;
    public function removeWayPoint(array $data): array;
    public function reorderProjectImages(array $data, int $project_id): array;
    public function deleteMultipleImagesById(array $ids, string $property = 'images'): array;
    public function relatedProjectSearch(string $search): array;
} 