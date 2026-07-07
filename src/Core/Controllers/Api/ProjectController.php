<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Models\Project\ProjectData;
use App\Core\Models\Project\ProjectResponse;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Project\ProjectRepositoryInterface;
use App\Core\Repositories\Project\ProjectStatusRepositoryInterface;
use Exception;
use stdClass;

class ProjectController extends ApiController
{
    private ProjectRepositoryInterface $projectRepository;
    private ProjectStatusRepositoryInterface $projectStatusRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectStatusRepositoryInterface $projectStatusRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        parent::__construct();
        $this->projectRepository = $projectRepository;
        $this->projectStatusRepository = $projectStatusRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $projects = $this->projectRepository->getAll();
        $results = [];
        foreach ($projects as $project) {
            $results[] = new ProjectResponse((object) $project);
        }
        return $this->renderResponse($results);
    }

    /**
     * Show a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {

        $project = $this->projectRepository->showProject((int) $id);
        if (!$project) {
            return $this->renderError(404, 'Project not found');
        }
        $response = new ProjectResponse($project->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new language.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->input('project');
            $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
                'title' => 'required|string',
            ], $data);

            if (!$data || !is_array($data)) {
                return $this->renderError(400, 'Invalid project data provided');
            }
            
            $data = new ProjectData($data);
            $project = $this->projectRepository->createProject($data);

            if ($project instanceof Exception) {
                // Wrap the exception message in an array
                return $this->renderError(422, 'Could not create project.', [$project->getMessage()]);
            }

            if ($project === null) {
                return $this->renderError(422, 'Could not create project.', ['Unknown error occurred.']);
            }
            // return Project directly
            return $this->renderResponse($project);

        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
    }

    /**
     * Update a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->input('project');
            $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
                'title' => 'required|string',
            ], $data);

            // Ensure project_id is set from the route parameter
            if (!isset($data['project_id'])) {
                $data['project_id'] = (int) $id;
            }
            // get projectstatus
            $projectStatusId = isset($data['status_id']) ? $data['status_id']: '';
            if($projectStatusId){
                $status = $this->projectStatusRepository->get($projectStatusId);
                $data['status'] = $status->name;
            }
            $data = new ProjectData($data);
            $project = $this->projectRepository->updateProject($data);
            
            if ($project instanceof Exception) {
                return $this->renderError(422, 'Could not update project.', [$project->getMessage()]);
            }

            if ($project === null) {
                return $this->renderError(422, 'Could not update project.', ['Unknown error occurred.']);
            }

            // Fetch the updated project with all relationships to return complete data
            $updatedProject = $this->projectRepository->showProject((int) $id);
            if (!$updatedProject) {
                return $this->renderError(404, 'Updated project not found');
            }

            // Return the updated project as ProjectResponse (consistent with show method)
            $response = new ProjectResponse($updatedProject->data);
            return $this->renderResponse($response);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Delete a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->projectRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Project deleted successfully']);
    }

    public function getStatuses(Request $request): Response
    {
        $statuses = $this->projectStatusRepository->findAll();
        return $this->renderResponse($statuses);
    }


    public function importProjects(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $projects = $this->projectRepository->importProjects($csv_file_path);
        return $this->renderResponse(['success' => $projects]);
    }

    public function importProjectImages(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $projects = $this->projectRepository->importProjectImages($csv_file_path);
        return $this->renderResponse(['success' => $projects]);
    }


    public function upload(Request $request, int $project_id): Response
    {
        $property = $request->input('property');

        // Set default size
        $size = [
            // 'width' => 1099,
            // 'height' => 733,
        ];

        $folder = 'media/Projects/';
        $is_banner = false;
        // Override size based on property
        if ($property === 'banner_image') {
            $folder .= 'banner';
            $is_banner = true;
            // $size = [
            //     'width' => 990,
            //     'height' => 660,
            // ];
            // i will keep original size
            $size = [];
        }  elseif ($property === 'image') { // banner 1600 x 657
            $folder .= 'banner';
            $is_banner = true;
            // $size = [
            //     'width' => 990,
            //     'height' => 660,
            // ];
            // i will keep original size
            $size = [];
        } elseif ($property === 'main_image_one') {
            $folder .= 'main-image-one';
            // $size = [
            //     'width' => 1346,
            //     'height' => 608,
            // ];
        } elseif ($property === 'main_image_two') {
            $folder .= 'main-image-two';
            // $size = [
            //     'width' => 670,
            //     'height' => 686,
            // ];
        } elseif ($property === 'featured_image') {
            $folder .= 'main-image-one';
            $size['featured_image_one'] = [
                // 'width' => 691,
                // 'height' => 461,
            ];
            $size['featured_image_two'] = [
                // 'width' => 537,
                // 'height' => 501,
            ];
        } elseif($property === 'image_thumb') { // 436 x 503
            $folder .= 'thumbnail';
            // $size = [
            //     'width' => 425,
            //     'height' => 284,
            // ];
        } elseif($property === 'project_images') {
            $folder .= 'gallery';
        }

        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder, null, $is_banner);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
            $featureProperties = ['image', 'image_thumb', 'main_image_one', 'main_image_two'];
            if (in_array($property, $featureProperties)) {
                // Feature image, update the property
                $this->projectRepository->updateProjectMainFeatureImage($result['files'], $property, $project_id);
            } else {
                // Gallery or other images, insert individually
                $result = $this->projectRepository->insertProjectImages($result['files'], $project_id);
                return $this->renderResponse($result);
            }
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    public function deleteProjectImage(Request $request, int $project_image_id): Response
    {
        $deleted = $this->projectRepository->deleteProjectImage($project_image_id);
        return $this->renderResponse(['message' => 'Media deleted successfully', 'deleted' => $deleted]);
    }

    public function deleteByPath(Request $request): Response
    {
        $path = $request->input('path');
        $property = $request->input('property');
        $projectId = (int) $request->input('project_id');

        // Validate input
        if (empty($path) || empty($property) || empty($projectId)) {
            return $this->renderError(422, 'Path, property, and project_id are required.');
        }

        try {
            // Delete the media file (if exists) from media table / storage
            $deletedFromMedia = $this->mediaRepository->deleteMediaByPath($path);

            // Update project record and remove file from filesystem
            $deletedFromProject = $this->projectRepository->deleteProjectMainImage($path, $property, $projectId);

            if ($deletedFromProject) {
                return $this->renderResponse([
                    'message' => 'Media deleted successfully',
                    'path' => $path,
                    'property' => $property,
                    'project_id' => $projectId
                ]);
            }

            return $this->renderError(404, 'Media or project record not found.');
        } catch (Exception $e) {
            return $this->renderError(500, 'An error occurred while deleting media: ' . $e->getMessage());
        }
    }
    

    public function getProjectPaginationData(Request $request): Response
    {
            $is_admin = $this->isAdmin();
            $current_page = (int) $_GET['current_page'];
            $per_page =  (int) $_GET['per_page'];

            // var_dump($per_page); exit;

            $projectLists = $this->projectRepository->getProjectListPaginationData($current_page, $per_page, $is_admin);
            return $this->renderResponse($projectLists);

     
        
    }

    public function updateWayPoints(Request $request): Response
    {
        $data = $request->all();
        $this->projectRepository->updateWayPoints($data);
        return $this->renderResponse(['message' => 'Way points updated successfully']);
    }
    
    public function removeWayPoint(Request $request): Response
    {
        $data = $request->all();
        $removed = $this->projectRepository->removeWayPoint($data);
        if (!$removed) {
            return $this->renderError(400, 'Failed to remove way point');
        }
        return $this->renderResponse($removed);
    }

    public function reorderImages(Request $request, int $project_image_id): Response
    {
        // echo 'reorderImages'; exit;
        $data = $request->all();
        $reordered = $this->projectRepository->reorderProjectImages($data, $project_image_id);
        return $this->renderResponse($reordered);
    }

    public function deleteMultipleImagesById(Request $request): Response
    {
        try {
            $data = $request->validate([
                'project_image_ids' => 'required|array',
                'property' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $ids = array_values(array_filter(
            array_map('intval', $data['project_image_ids']),
            static fn (int $id): bool => $id > 0,
        ));

        if ($ids === []) {
            return $this->renderError(422, 'No valid resource document ids provided');
        }

        $property = $data['property'] ?? 'images';
        $projectImages = $this->projectRepository->deleteMultipleImagesById($ids, $property);
        return $this->renderResponse($projectImages);
    }

    public function relatedProjectSearch(Request $request): Response
    {
        $relatedProjects = $this->projectRepository->relatedProjectSearch($request->input('search'));
        return $this->renderResponse($relatedProjects);
    }
}
