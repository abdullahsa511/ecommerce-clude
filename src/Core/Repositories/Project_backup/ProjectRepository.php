<?php

declare(strict_types=1);

namespace App\Core\Repositories\Project;

use App\Core\Models\Project\ProjectImage;
use App\Core\Models\Project\Project;
use App\Core\Models\Project\ProjectData;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Utilities\Debug;
use App\Core\Validation\ProjectDataValidation;
use App\Core\Validation\ProjectImageDataValidation;
use Exception;
use PDO;
use League\Csv\Reader;

use function App\Core\System\utils\app;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    private ProjectImage $projectImage;
    public function __construct(PDO $db, ProjectImage $projectImage)
    {
        parent::__construct($db, 'project', Project::class);
        $this->projectImage = $projectImage;
        $this->projectImage->setDb($db);
    }

    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;
        $query->with([
            'customer' => function ($q) {
                return $q->select(['customer.user_id', 'customer.first_name', 'customer.last_name', 'customer.email', 'CONCAT(customer.first_name, " ", customer.last_name) as name']);
            }
        ]);
        $query->orderBy('project_id', 'DESC');
        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }
        if (isset($param['fields']) && is_array($param['fields'])) {
            $query->select($param['fields']);
        }
        $result = $query->findAll();
        return $result;
    }

    public function get(int $projectId): ?Project
    {
        $query = $this->model
            ->where('project_id', '=', $projectId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }

        return $this->model->set($result[0]);
    }

    public function getBySlug(string $slug): ?Project
    {
        $query = $this->model
            ->where('slug', '=', $slug);
        $result = $query->first();
        return $result;
    }

    public function getFeaturedProjectSliderComponentData(array $param)
    {
        $model = 'project';
        //Now validate if the $params['model'] is a valid model
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }
            if (isset($param['is_recent']) && $param['is_recent'] == true) {
                $query->orderBy('project_id', 'DESC');
            }
            if (isset($param['is_featured']) && $param['is_featured'] == true) {
                $query->where('is_featured', '=', 1);
            }
            if (isset($param['join']) && is_array($param['join'])) {
                foreach ($param['join'] as $join) {
                    $query->join($join['table'], $join['on'], $join['type']);
                }
            }
            if (isset($param['fields']) && is_array($param['fields'])) {
                $query->select($param['fields']);
            }
            $result = $query->findAll();
            return $result;
        }
        return [];
    }

    public function getProjectDetailMainComponentData(array $param)
    {
        $model = $this->model::class;
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
        }
        $query->where('project_id', '=', $param['model_id']);

        $query->select($param['fields']);
        $result = $query->first();
        return $result;
    }

    // public function getProjectDetailPenetratingComponentData(array $param)
    // {
    //     $model = $this->model::class;
    //     if(isset($param['model']) && $model == $param['model']) {
    //         $query = $this->model;
    //     }
    //     $query->where('project_id', '=', $param['model_id']);
    //     if(isset($param['with']) && is_array($param['with'])) {
    //         $query->with($param['with']);
    //     }
    //     $query->select($param['fields']);
    //     $result = $query->first();
    //     return $result;
    // }

    public function getProjectDetailsComponentData(array $param)
    {
        $model = 'project';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
        }
        $query->where('slug', '=', $param['slug']);
        // if(isset($param['with']) && is_array($param['with'])) {
        //     $query->with($param['with']);
        // }
        $query->select($param['fields']);
        $result = $query->first();
        return $result;
    }

    public function getProjectGalleryComponentData(array $param)
    {
        $model = 'project';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }

            $query->where('project.slug', '=', $param['slug'])
                ->select($param['fields'])
                ->orderBy('project_image.sort_order', 'ASC');

            // if (isset($param['item_count']) && $param['item_count'] > 0) {
            //     $query->limit($param['item_count']);
            // }

            $results = $query->findAll(false);
            $galleryThumb = [];

            foreach ($results as $index => $result) {
                $imageData = json_decode($result['image'] ?? '{}', true);
                if (!isset($imageData['objectURL']))
                    continue;
                $imageUrl = $imageData['objectURL'];

                $isActive = ($index === 0) ? ' active' : '';
                $showClass = ($index === 0) ? ' active show' : '';

                $galleryThumb[] = [
                    'thumb_image' => $imageUrl,
                    'image' => $imageUrl,
                    'thumb_class' => ' th-gallery-thumb' . $isActive,
                    'class' => 'tab-pane fade th-gallery-img' . $showClass,
                    'thumb_id' => 'img-' . ($index + 1) . '-tab',
                    'id' => 'img-' . ($index + 1),
                    'target' => '#img-' . ($index + 1)
                ];
            }

            // If no results found, return default gallery items
            if (empty($galleryThumb)) {
                $galleryThumb = [
                    [
                        'thumb_image' => '/img/blog-detail/gallery-img-1.png',
                        'image' => '/img/blog-detail/gallery-img-1.png',
                        'thumb_class' => ' th-gallery-thumb active',
                        'class' => 'tab-pane fade th-gallery-img active show',
                        'thumb_id' => 'img-1-tab',
                        'id' => 'img-1',
                        'target' => '#img-1'
                    ],
                    [
                        'thumb_image' => '/img/blog-detail/gallery-img-2.png',
                        'image' => '/img/blog-detail/gallery-img-2.png',
                        'thumb_class' => ' th-gallery-thumb',
                        'class' => 'tab-pane fade th-gallery-img',
                        'thumb_id' => 'img-2-tab',
                        'id' => 'img-2',
                        'target' => '#img-2'
                    ],
                    [
                        'thumb_image' => '/img/blog-detail/gallery-img-3.png',
                        'image' => '/img/blog-detail/gallery-img-3.png',
                        'thumb_class' => ' th-gallery-thumb',
                        'class' => 'tab-pane fade th-gallery-img',
                        'thumb_id' => 'img-3-tab',
                        'id' => 'img-3',
                        'target' => '#img-3'
                    ],
                    [
                        'thumb_image' => '/img/blog-detail/gallery-img-4.png',
                        'image' => '/img/blog-detail/gallery-img-4.png',
                        'thumb_class' => ' th-gallery-thumb',
                        'class' => 'tab-pane fade th-gallery-img',
                        'thumb_id' => 'img-4-tab',
                        'id' => 'img-4',
                        'target' => '#img-4'
                    ],
                    [
                        'thumb_image' => '/img/blog-detail/gallery-img-5.png',
                        'image' => '/img/blog-detail/gallery-img-5.png',
                        'thumb_class' => ' th-gallery-thumb',
                        'class' => 'tab-pane fade th-gallery-img',
                        'thumb_id' => 'img-5-tab',
                        'id' => 'img-5',
                        'target' => '#img-5'
                    ]
                ];
            }

            return [
                'sectionTitle' => 'Project Gallery',
                'galleryThumb' => $galleryThumb
            ];
        }

        return [
            'sectionTitle' => 'Project Gallery',
            'galleryThumb' => []
        ];
    }

    public function getProjectDetailUnderHero(array $param)
    {
        $model = 'project';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;

            $query->where('project.slug', '=', $param['slug'])
                ->select($param['fields']);

            $results = $query->first();
            $items = [
                [
                    'title' => 'LOCATION',
                    'subtitle' => $results->location ?? '43-47 Hallam S Rd, Hallam VIC 3803',
                    'class' => 'col-md-4 text-first under-hero-item'
                ],
                [
                    'title' => 'DESIGNER',
                    'subtitle' => $results->designer ?? 'Total Fitouts',
                    'class' => 'col-md-4 text-center under-hero-item'
                ],
                [
                    'title' => 'PHOTOGRAPHER',
                    'subtitle' => $results->photographer ?? 'Pixel Collective',
                    'class' => 'col-md-4 text-center under-hero-item'
                ]
            ];

            return $items;

        }
        return [];
    }
    public function getProjectDetailPenetratingComponentData(array $param)
    {
        $model = 'project';
        $results = [];
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            $query->where('project.slug', '=', $param['slug']);
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }

            $query->select($param['fields'])
                ->orderBy('project.project_id', 'ASC')
                ->limit($param['item_count']);

            $results = $query->first();
            $results = (array) $results->data;


        }
        return $results;
    }

    public function getAllProjects(array $param)
    {
        $query = $this->model;
        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }
        if (isset($param['fields']) && is_array($param['fields'])) {
            $query->select($param['fields']);
        }
        $query->orderBy('project_id', 'DESC');
        $result = $query->findAll();
        return $result;
    }

    public function getProjectGallery(array $param)
    {
        $query = $this->model;
        if (isset($param['fields']) && is_array($param['fields'])) {
            $query->select($param['fields']);
        }
        $query->orderBy('project_id', 'DESC');
        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }
        $result = $query->findAll();
        return $result;
    }

    public function createProject(ProjectData $data): Project|Exception|null
    {
        $projectData = $data->toArray(); // no need
        $project = null;
        if (count($projectData) > 0) {
            try {
                // Check if title already exists
                $existing = $this->model->where('title', '=', $data->title)->first();
                if ($existing) {
                    // throw new Exception("Project title '{$data->title}' already exists.");
                    return null;
                }

                $projectData = Project::projectData($data);
                $project = $this->model->create($projectData);
                // $project = $this->model->create($projectData);
                // $images = $data->getImages($project->project_id);
                // if (count($images) > 0) {
                //     $this->projectImage->upsert($images, ['project_id', 'image_link']);
                //     $project->data->images = $images;
                // }
            } catch (Exception $e) {
                return $e;
            }
        }
        return $project;
    }

    public function insertProjectImages(array $data, int $project_id): bool
    {
        $imageData = [];
        $config = app('config');
        $imageServer = $config['APP_URL'];
        foreach ($data as $image) {
            $img = [];
            $img['project_id'] = $project_id;
            $img['image_link'] = $image['image'];
            $img['image'] = json_encode([
                'name' => $image['name'],
                'objectURL' => $imageServer . $image['objectURL'],
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => ROOT_DIR . PUBLIC_PATH . $image['image'],
                'status' => $image['status']
            ]);
            $img['sort_order'] = 0;
            $img['status'] = json_encode($image['status']);
            $img['way_points'] = json_encode([]);
            $imageData[] = $img;
        }
        if (count($imageData)) {
            $this->db->beginTransaction();
            $this->projectImage->insert($imageData);
            $this->db->commit();
        }
        return true;
    }
    public function updateProjectMainFeatureImage(array $data, string $property, int $project_id): bool
    {
        $project = $this->model->where('project_id', '=', $project_id)->first();
        if (!$project) {
            return false; // project not found
        }

        $config = app('config');
        $imageServer = $config['APP_URL'];
        $dataobj = $data;
        $config = app('config');
        $imageServer = $config['APP_URL'];

        $img = [];
        foreach ($dataobj as $item) {
            $img[] = [
                'project_id' => $project_id,
                'name' => $item['name'] ?? '',
                'size' => $item['size'] ?? '',
                'type' => $item['type'] ?? '',
                'image' => $item['image'] ?? '',
                'status' => isset($item['status']) && is_array($item['status'])
                    ? $item['status']
                    : ['name' => 'Uploaded', 'severity' => 'success'],
                'media_id' => null,
                'objectURL' => $imageServer . ($item['objectURL'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'product_id' => null,
                'description' => $item['description'] ?? '',
                'post_image_id' => null,
                'product_image_id' => null,
                'project_image_id' => $project_id,
                'project_section_images_id' => null,
            ];
        }
        $imgJson = json_encode($img);
        $this->db->beginTransaction();
        try {
            // UPDATE `project` SET `image` = $img WHERE `project`.`project_id` = $project_id
            $project->update([$property => $imgJson]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateProject(ProjectData $data): Project|Exception|null
    {
        $projectData = $data->toArray();
        if (count($projectData) > 0) {
            try {
                $project = $this->model->where('project_id', '=', $projectData['project_id'])->first();
                if (!$project) {
                    return new Exception('Project not found');
                }

                $existing = $this->model->where('title', '=', $projectData['title'])->first();
                if ($existing) {
                    // throw new Exception("Project title '{$projectData['title']}' already exists.");
                    return null;
                }

                // $images = $data->getImages($project->project_id);
                unset($projectData['images']);
                $project = $project->update($projectData);
                // if(isset($images) && is_array($images)){
                //     if(count($images) > 0){
                //         $this->projectImage->deleteWhere(['project_id' => $project->project_id]);
                //         $this->projectImage->upsert($images, ['project_id', 'image_link']);
                //         $project->data->images = $images;
                //     }else{
                //         $projectImages = $this->projectImage->where('project_id', '=', $project->project_id)->select(['project_image_id'])->findAll();
                //         if(isset($projectImages) && is_array($projectImages)){
                //             $projectImageIds = array_column($projectImages, 'project_image_id');
                //             $this->projectImage->deleteMultiple($projectImageIds);
                //         }
                //         $project->data->images = [];
                //     }
                // }
            } catch (Exception $e) {
                return $e;
            }
        }
        return $project;
    }

    public function showProject(int $projectId): Project|Exception|null
    {
        $project = $this->model->where('project_id', '=', $projectId)
            ->with([
                'images' => function ($q) {
                    return $q->select(['project_image_id', 'project_id', 'image_link', 'image', 'sort_order', 'status']);
                },
                'customer' => function ($q) {
                    return $q->select(['customer.user_id as customer_id', 'CONCAT(customer.first_name, " ", customer.last_name) as name']);
                }
            ]);

        $project = $project->first();
        return $project;
    }
    /** 
     * The modified methods started from here
     * 
     */

    public function insertProjects(array $data): bool
    {
        $projects = $data['projects'];
        $projectImages = $data['projectImages'];
        $this->db->beginTransaction();
        $this->model->insert($projects);

        $this->projectImage->insert($projectImages);

        $this->db->commit();
        return true;
    }

    public function getAllProjectsForComponent(): array
    {
        $projects = $this->model->orderBy('project_id', 'DESC')->findAll();

        if (empty($projects)) {
            return [
                'section_title' => 'All Projects',
                'section_subtitle' => 'No projects available at the moment.',
                'items' => [],
                'load_btn' => 'Load More',
                'message' => 'No projects found'
            ];
        }

        $results = [
            'section_title' => 'All Projects',
            'section_subtitle' => 'Explore our portfolio of completed projects and ongoing work.',
            'items' => [],
            'load_btn' => 'Load More'
        ];

        foreach ($projects as $project) {
            // Get project images
            $projectImages = $this->projectImage->where('project_id', '=', $project->project_id)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            // Get primary image or default
            $primaryImage = '/img/projects/project-default.png';
            if (!empty($projectImages)) {
                $primaryImage = $projectImages[0]->image_link ?? '/img/projects/project-default.png';
            }

            // Get project status
            $status = $this->getProjectStatus($project->status_id ?? 1);

            $projectData = [
                'id' => (string) $project->project_id,
                'title' => $project->name ?? $project->title ?? 'Untitled Project',
                'image' => $primaryImage,
                'description' => $project->description ?? 'No description available.',
                'label' => $project->label ?? $project->status ?? 'Project',
                'link_text' => $project->link_text ?? 'Read More',
                'project_info' => [
                    'project_id' => $project->project_id,
                    'site_id' => $project->site_id ?? 1,
                    'status_id' => $project->status_id ?? 1,
                    'customer_id' => $project->customer_id,
                    'name' => $project->name,
                    'slug' => $project->slug,
                    'description' => $project->description,
                    'location' => $project->location,
                    'status' => $project->status,
                    'image' => $project->image,
                    'meta_title' => $project->meta_title,
                    'meta_description' => $project->meta_description,
                    'meta_keywords' => $project->meta_keywords,
                    'title' => $project->title,
                    'label' => $project->label,
                    'link_text' => $project->link_text,
                    'is_featured' => $project->is_featured ?? 0,
                    'created_at' => $project->created_at ? date('F jS, Y', strtotime($project->created_at)) : 'N/A',
                    'updated_at' => $project->updated_at ? date('F jS, Y', strtotime($project->updated_at)) : 'N/A'
                ],
                'images' => array_map(function ($image) {
                    return [
                        'image_id' => $image->project_image_id,
                        'project_id' => $image->project_id,
                        'image_link' => $image->image_link,
                        'image' => $image->image,
                        'sort_order' => $image->sort_order ?? 0,
                        'status' => $image->status
                    ];
                }, $projectImages),
                'status_info' => [
                    'status_id' => $project->status_id ?? 1,
                    'status_name' => $status,
                    'is_featured' => $project->is_featured ?? 0,
                    'is_active' => $project->status_id == 1
                ],
                'seo_info' => [
                    'meta_title' => $project->meta_title,
                    'meta_description' => $project->meta_description,
                    'meta_keywords' => $project->meta_keywords,
                    'slug' => $project->slug
                ]
            ];

            $results['items'][] = $projectData;
        }

        return $results;
    }

    /**
     * Get project status name based on status ID
     */
    private function getProjectStatus(int $statusId): string
    {
        $statusMap = [
            1 => 'Active',
            2 => 'Completed',
            3 => 'On Hold',
            4 => 'Cancelled',
            5 => 'Draft'
        ];

        return $statusMap[$statusId] ?? 'Unknown';
    }

    public function getFeaturedProjectMasonryComponentData(array $param)
    {
        $model = 'project';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }
            if (isset($param['is_featured']) && $param['is_featured'] == 1) {
                $query->where('project.is_featured', '=', 1);
            }
            // Always select the needed columns
            $query->select($param['fields']); // Add other needed columns
            $query->orderBy('project.project_id', 'DESC');
            $projects = $query->findAll();

            $result = [];
            $item = [];
            foreach ($projects as $key => $project) {
                if (in_array($key, [0, 3])) {
                    $item['class'] = 'th-masonry-grid-item grid-col-span-7';
                }
                if (in_array($key, [1, 2])) {
                    $item['class'] = 'th-masonry-grid-item grid-col-span-6';
                }

                if (isset($project['name'])) {
                    $item['heading'] = $project['name'];
                }

                if (isset($project['description'])) {
                    $item['des'] = $project['description'];
                }

                if (isset($project['slug'])) {
                    $item['link'] = '/projects/' . $project['slug'];
                }

                if (isset($project['image'])) {
                    $image = json_decode($project['image'], true);
                    if (isset($image[0]['objectURL'])) {
                        $item['img'] = $image[0]['objectURL'];
                    } else {
                        $item['img'] = $image[0] ?? null;
                    }
                }

                $result[] = $item;
            }
            return [
                'section_title' => 'Featured Projects',
                'section_subtitle' => 'Explore our portfolio of completed projects and ongoing work.',
                'items' => $result
            ];
        }
        return [];
    }

    public function importProjects(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }


        $records = $reader->getRecords();

        $validProjects = [];
        $invalidProjects = [];
        $updatedProjects = [];
        $processedIdentifiers = [];

        foreach ($records as $offset => $record) {
            try {
                // Ensure all required fields exist with default values
                $record = $this->ensureAllFieldsExist($record, $headers);

                if (isset($record['image_thumb']) && !empty($record['image_thumb'])) {
                    $record['image_thumb'] = $this->convertImageToJsonFormat($record['image_thumb']);
                }

                if (isset($record['image']) && !empty($record['image'])) {
                    $record['image'] = $this->convertImageToJsonFormat($record['image']);
                }
                if (isset($record['main_image_one']) && !empty($record['main_image_one'])) {
                    $record['main_image_one'] = $this->convertImageToJsonFormat($record['main_image_one']);
                }
                if (isset($record['main_image_two']) && !empty($record['main_image_two'])) {
                    $record['main_image_two'] = $this->convertImageToJsonFormat($record['main_image_two']);
                }

                // Sanitize text fields to handle encoding issues
                $record = $this->sanitizeTextFields($record);

                $projectValidation = new ProjectDataValidation($record);

                $validationResult = $projectValidation->validate();
 
                if ($validationResult === false) {
                    $invalidProjects[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $projectValidation->getErrors()
                    ];
                    continue;
                }

                // Check for duplicates using unique identifier
                $uniqueIdentifier = $projectValidation->getUniqueIdentifier();
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updatedProjects[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }

                // Check if project already exists in database - if exists, mark as update
                if ($this->isProjectDuplicate($record)) {
                    $updatedProjects[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'reason' => 'Project already exists in database - will be updated'
                    ];
                    // Continue processing to include in validProjects for upsert
                }

                $normalizedData = $this->normalizeProjectData($validationResult, $headers);
                $validProjects[] = $normalizedData;
                $processedIdentifiers[] = $uniqueIdentifier;
            } catch (Exception $e) {
                error_log("Error processing row " . ($offset + 2) . ": " . $e->getMessage());
                $invalidProjects[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        $insertedCount = 0;
        if (!empty($validProjects)) {
            try {
                $this->db->beginTransaction();

                $uniqueKeys = ['project_id'];
                $insertedCount = $this->model->upsert($validProjects, $uniqueKeys);

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert projects: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validProjects),
            'invalid_records' => count($invalidProjects),
            'updated_records' => count($updatedProjects),
            'inserted_count' => $insertedCount,
            'valid_data' => $validProjects,
            'invalid_data' => $invalidProjects,
            'updated_data' => $updatedProjects
        ];
    }

    public function importProjectImages(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        // Get CSV headers first
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validImages = [];
        $invalidImages = [];
        $updatedImages = [];
        $processedIdentifiers = [];

        foreach ($records as $offset => $record) {
            try {
                // Ensure all required fields exist with default values based on CSV headers
                $record = $this->ensureAllImageFieldsExist($record, $headers);

                // Set default values for image-specific fields
                $record['sort_order'] = $record['sort_order'] ?? 1;
                $record['status'] = json_encode([
                    "active" => true,
                    "featured" => false
                ]);

                // Convert way_points to JSON if it exists
                if (isset($record['way_points']) && !empty($record['way_points'])) {
                    $record['way_points'] = json_encode($record['way_points']);
                }

                // Convert image_link to image JSON format
                if (isset($record['image_link']) && !empty($record['image_link'])) {
                    $record['image_link'] = $record['image_link'];
                    $record['image'] = $this->convertImageToJsonFormat($record['image_link']);
                } else {
                    // Ensure image field is always set with a default value
                    $record['image'] = json_encode([]);
                }

                // Create validation instance
                $imageValidation = new ProjectImageDataValidation($record);

                // Validate the data
                $validationResult = $imageValidation->validate();

                if ($validationResult === false) {
                    // Data is invalid
                    $invalidImages[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $imageValidation->getErrors()
                    ];
                    continue;
                }

                // Check for duplicates using unique identifier
                $uniqueIdentifier = $imageValidation->getUniqueIdentifier();
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updatedImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }

                // Check if image already exists in database - if exists, mark as update
                if ($this->isProjectImageDuplicate($record)) {
                    $updatedImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'reason' => 'Project image already exists in database - will be updated'
                    ];
                    // Continue processing to include in validImages for upsert
                }

                // Check if referenced project exists
                if (empty($record['project_id']) || !is_numeric($record['project_id'])) {
                    $invalidImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['project_id' => "Invalid or missing project_id"]
                    ];
                    continue;
                }

                if (!$this->doesProjectExist((int) $record['project_id'])) {
                    $invalidImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['project_id' => "Referenced project with ID {$record['project_id']} does not exist"]
                    ];
                    continue;
                }

                // Data is valid - normalize to ensure consistent structure
                // $normalizedData = $this->normalizeImageData($validationResult, $headers);
                $validImages[] = $validationResult;
                $processedIdentifiers[] = $uniqueIdentifier;
            } catch (Exception $e) {
                // Log the error and add to invalid images
                error_log("Error processing image row " . ($offset + 2) . ": " . $e->getMessage());
                $invalidImages[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        $insertedCount = 0;
        if (!empty($validImages)) {
            try {
                $this->db->beginTransaction();

                $uniqueKeys = ['project_id', 'image_link'];
                $insertedCount = $this->projectImage->upsert($validImages, $uniqueKeys);

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert project images: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validImages),
            'invalid_records' => count($invalidImages),
            'updated_records' => count($updatedImages),
            'inserted_count' => $insertedCount,
            'valid_data' => $validImages,
            'invalid_data' => $invalidImages,
            'updated_data' => $updatedImages
        ];
    }

    private function convertImageToJsonFormat(string $imageValue): string
    {
        // If the value is already in JSON format, return as is
        if ($this->isValidJson($imageValue)) {
            return $imageValue;
        }
        $imageValue = str_contains($imageValue, '/media/Projects/') ? $imageValue : "/media/Projects/{$imageValue}";

        //Check if the image is exists in the $imageValue path 
        $imgPath = str_replace('//', '/', ROOT_DIR . PUBLIC_PATH . $imageValue);
        //Retrive image file object
        $imageFile = new \SplFileInfo($imgPath);

        // Check if image file exists
        if (!$imageFile->isFile()) {
            return json_encode([]);
        }
        // Pepare the image path with server http from env APP_URL variable
        $config = app('config');
        $imageServerPath = $config['APP_URL'] . $imageValue;

        $imageData = [
            [
                'objectURL' => $imageServerPath,
                'name' => $imageFile->getFilename(),
                'size' => $imageFile->getSize(),
                'path' => $imgPath,
                'status' => ['name' => 'Uploaded', 'severity' => 'success']
            ]
        ];

        return json_encode($imageData);
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Sanitize text fields to handle character encoding issues
     * Converts Windows-1252 characters to UTF-8 equivalents
     */
    private function sanitizeTextFields(array $record): array
    {
        $textFields = [
            'description',
            'name',
            'location',
            'designer',
            'photographer',
            'status',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'title',
            'label',
            'keyline_quote',
            'link_text',
            'main_title',
            'main_description_one',
            'main_description_two',
            'main_description_three',
            'main_description_four'
        ];

        foreach ($textFields as $field) {
            if (isset($record[$field]) && is_string($record[$field]) && !empty($record[$field])) {
                $record[$field] = $this->fixTextEncoding($record[$field]);
            }
        }

        return $record;
    }

    private function fixTextEncoding(string $text): string
    {
        // First, try to detect if the text is already UTF-8
        if (mb_check_encoding($text, 'UTF-8')) {
            // If it's valid UTF-8, check for common Windows-1252 characters that might have been incorrectly encoded
            $replacements = [
                "\x92" => "'",  // Right single quotation mark
                "\x93" => '"',  // Left double quotation mark
                "\x94" => '"',  // Right double quotation mark
                "\x96" => "–",  // En dash
                "\x97" => "—",  // Em dash
                "\x85" => "…",  // Horizontal ellipsis
                "\x91" => "'",  // Left single quotation mark
                "\x99" => "™",  // Trademark symbol
                "\xa9" => "©",  // Copyright symbol
                "\xae" => "®",  // Registered trademark symbol
            ];

            $text = str_replace(array_keys($replacements), array_values($replacements), $text);
        } else {
            // If it's not valid UTF-8, try to convert from Windows-1252
            $converted = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
            if ($converted !== false) {
                $text = $converted;
            } else {
                // Fallback: remove or replace problematic characters
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
            }
        }

        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }

        return trim($text);
    }


    private function isProjectDuplicate(array $record): bool
    {
        if (!empty($record['slug'])) {
            $existingProject = $this->model->where('slug', '=', $record['slug'])->first();
            if ($existingProject) {
                return true;
            }
        }

        if (!empty($record['project_id'])) {
            $existingProject = $this->model->where('project_id', '=', $record['project_id'])->first();
            if ($existingProject) {
                return true;
            }
        }

        return false;
    }

    private function isProjectImageDuplicate(array $record): bool
    {
        // return false;
        if (!empty($record['project_id']) && !empty($record['image_link'])) {
            $existingImage = $this->projectImage->where('project_id', '=', $record['project_id'])
                ->where('image_link', '=', $record['image_link'])
                ->first();
            if ($existingImage) {
                return true;
            }
        }

        if (!empty($record['project_image_id'])) {
            $existingImage = $this->projectImage->where('project_image_id', '=', $record['project_image_id'])->first();
            if ($existingImage) {
                return true;
            }
        }

        return false;
    }

    private function doesProjectExist(int $projectId): bool
    {
        $project = $this->model->where('project_id', '=', $projectId)->first();
        return $project !== null;
    }


    private function ensureAllFieldsExist(array $record, array $headers): array
    {
        $defaultFields = [];

        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['site_id'] = 1;
        $defaultFields['status_id'] = 1;
        $defaultFields['is_featured'] = 0;

        return array_merge($defaultFields, $record);
    }

    private function ensureAllImageFieldsExist(array $record, array $headers): array
    {
        $defaultFields = [];

        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['sort_order'] = 1;
        $defaultFields['status'] = json_encode([
            "active" => true,
            "featured" => false
        ]);
        $defaultFields['way_points'] = json_encode([]);
        $defaultFields['image'] = json_encode([]);
        $defaultFields['image_link'] = '';

        return array_merge($defaultFields, $record);
    }


    private function normalizeProjectData(array $data, array $headers): array
    {
        $normalizedData = [];

        foreach ($headers as $header) {
            $normalizedData[$header] = $data[$header] ?? '';
        }

        // Adding required system fields that might not be in CSV
        $systemFields = ['created_at', 'updated_at'];
        foreach ($systemFields as $field) {
            if (!isset($normalizedData[$field])) {
                $normalizedData[$field] = date('Y-m-d H:i:s');
            }
        }

        return $normalizedData;
    }


    private function normalizeImageData(array $data, array $headers): array
    {
        $normalizedData = [];

        // Add all fields from CSV headers with default values
        foreach ($headers as $header) {
            $normalizedData[$header] = $data[$header] ?? '';
        }

        // Ensure required fields have proper default values
        if (!isset($normalizedData['image']) || empty($normalizedData['image'])) {
            $normalizedData['image'] = json_encode([]);
        }

        if (!isset($normalizedData['status']) || empty($normalizedData['status'])) {
            $normalizedData['status'] = json_encode([
                "active" => true,
                "featured" => false
            ]);
        }

        if (!isset($normalizedData['way_points']) || empty($normalizedData['way_points'])) {
            $normalizedData['way_points'] = json_encode([]);
        }

        if (!isset($normalizedData['sort_order'])) {
            $normalizedData['sort_order'] = 1;
        }

        return $normalizedData;
    }

    public function deleteProjectImage(int $project_image_id): bool
    {
        return $this->projectImage->delete($project_image_id);
    }
    public function deleteProjectMainImage(string $path, string $property, int $projectId): bool
    {
        // Fetch project
        $project = $this->model->where('project_id', '=', $projectId)->first();

        if (!$project) {
            return false; // Project not found
        }

        // Resolve physical file path (safe)
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $path;

        // Delete file if exists
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        // Update the project's image property — empty array to reset
        $project->update([
            $property => json_encode([]),
        ]);

        return true;
    }

}