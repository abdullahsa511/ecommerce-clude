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
use App\Core\Exceptions\ValidationException;
use App\Core\Models\Media\Media;
use PDO;
use App\Core\Models\Post\Post;
use App\Core\Models\Product\Product;
use League\Csv\Reader;

use function App\Core\System\utils\app;
use function App\Core\System\utils\env;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    private ProjectImage $projectImage;
    private Media $media;
    private Post $post;
    private Product $product;
    public function __construct(
        PDO $db, 
        ProjectImage $projectImage,
        Media $media,
        Post $post,
        Product $product
    ){
        parent::__construct($db, 'project', Project::class);
        $this->projectImage = $projectImage;
        $this->projectImage->setDb($db);
        $this->media = $media;
        $this->media->setDb($db);
        $this->post = $post;
        $this->post->setDb($db);
        $this->product = $product;
        $this->product->setDb($db);
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
        $way_points = [];
        if(isset($result->data->banner_way_points) && is_string($result->data->banner_way_points)){
            $way_points = json_decode($result->data->banner_way_points, true);
        }else{
            $way_points = [];
        }
        $result->way_points = $way_points;
        return $result;
    }

    public function getFeaturedProjectSliderComponentData(array $param)
    {
        $projectId = isset($param['project_id']) ? $param['project_id'] : '';
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

            if ($projectId) {
                $query->where('project_id','!=', $projectId);
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

    public function getProjectListPaginationData(int $current_page, int $per_page, bool $is_admin = false)
    {
        $limit = $per_page;
        // $start = $per_page * $current_page;
        $start = $per_page * ($current_page);
        $offset = ($current_page - 1) * $per_page;

        $total = $this->model->where('project.status_id', '!=', 1)->where('project.status', '!=', 'Draft')->countAll();
        $this->model->clearQuery();
        $query = $this->model;
        $query->join('project_image', 'project_image.project_id', '=', 'project.project_id')
            ->select([
                'project.project_id',
                'project.preview_text',
                'project.title',
                'project.label',
                'project.image',
                'project.image_thumb',
                'project.slug',
                'project.link_text'
            ])
            ->where('project.status_id', '!=', 1)->where('project.status', '!=', 'Draft')
            ->orderBy('project.project_id', 'DESC')
            ->limit($limit)
            ->offset($offset);
            // ->offset($start);

        $data = $query->findAll();


        foreach ($data as $key => $project) {
            if (!empty($project['image'])) {
                $decoded = json_decode($project['image'], true);
                if (is_array($decoded) && isset($decoded[0]['objectURL'])) {
                    $data[$key]['image'] = $decoded[0]['objectURL'];
                } else {
                    $data[$key]['image'] = $project['image'];
                }
                
            } else {
                $data[$key]['image'] = null;
            }
            if (!empty($project['image_thumb'])) {
                $decoded = json_decode($project['image_thumb'], true);
                if (is_array($decoded) && isset($decoded[0]['objectURL'])) {
                    $data[$key]['image_thumb'] = $decoded[0]['objectURL'];
                } else {
                    $data[$key]['image_thumb'] = $project['image_thumb'];
                }
                
            } else {
                $data[$key]['image_thumb'] = null;
            }
            $data[$key]['is_admin'] = $is_admin;
            $data[$key]['edit_link'] = env('APP_ADMIN_URL')."/ecommerce/projects/edit/{$project['project_id']}/general";
        }
        return [
            'data' => $data,
            'total' => $total,
            'current_page' => $current_page,
            'per_page' => $per_page,
        ];
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
        $results = (array) $result->data;
        $results['section_title'] = $result->keyline_quote ?? '';
        $results['description'] = $result->description ?? '';
        $results['description-two'] = $result->meta_description ?? '';
        return $results;
    }

    public function getProjectGalleryComponentData_backup(array $param)
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
                ->select(['project.slug', 'project.name'])
                ->select($param['fields'])
                ->orderBy('project_image.sort_order', 'ASC');

            // if (isset($param['item_count']) && $param['item_count'] > 0) {
            //     $query->limit($param['item_count']);
            // }

            $results = $query->findAll(false);
            $galleryThumb = [];

            foreach ($results as $index => $result) {
                $imageData = json_decode($result['image'] ?? '{}', true);
                $imageData = isset($imageData['objectURL'])?$imageData:$imageData[0]??[];
                $imageName = isset($imageData['name'])? $imageData['name'] : '' ?? '';
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
                    'target' => '#img-' . ($index + 1),
                    'alt' => (isset($result['name']) ? $result['name'] . ' - ' : '') .
                        str_replace(['_', '-'], ' ', pathinfo($imageName, PATHINFO_FILENAME))
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

    public function getProjectGalleryComponentData(array $param)
    {
        $model = 'project';
    
        if (isset($param['model']) && $model === $param['model']) {
            $query = $this->model;
    
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
    
            $query->where('project.slug', '=', $param['slug'])
                ->select(['project.slug', 'project.name'])
                ->select($param['fields'])
                ->orderBy('project_image.sort_order', 'ASC');
    
            $results = $query->findAll(false);
            $galleryThumb = [];
    
            foreach ($results as $index => $result) {
                $imageData = json_decode($result['image'] ?? '{}', true);
    
                $imageData = isset($imageData['objectURL'])
                    ? $imageData
                    : ($imageData[0] ?? []);
    
                if (!isset($imageData['objectURL'])) {
                    continue;
                }
    
                $imageUrl = $imageData['objectURL'];
                $imageName = $imageData['name'] ?? '';
    
                $projectName = trim($result['name'] ?? 'Project');
    
                $imageTitle = ucwords(
                    trim(
                        str_replace(
                            ['_', '-'],
                            ' ',
                            pathinfo($imageName, PATHINFO_FILENAME)
                        )
                    )
                );
    
                $genericPatterns = [
                    '/^gallery\s*img\s*\d*$/i',
                    '/^gallery\s*image\s*\d*$/i',
                    '/^gallery\s*\d*$/i',
                    '/^image\s*\d*$/i',
                    '/^img\s*\d*$/i',
                    '/^photo\s*\d*$/i',
                ];
    
                $isGenericImageName = false;
    
                foreach ($genericPatterns as $pattern) {
                    if (preg_match($pattern, $imageTitle)) {
                        $isGenericImageName = true;
                        break;
                    }
                }
    
                if (!empty($imageTitle) && !$isGenericImageName) {
                    $templatedAlt = sprintf(
                        '%s - %s',
                        $projectName,
                        $imageTitle
                    );
                } else {
                    $templatedAlt = sprintf(
                        '%s - Project Gallery Image %d',
                        $projectName,
                        $index + 1
                    );
                }
    
                $isActive = ($index === 0) ? ' active' : '';
                $showClass = ($index === 0) ? ' active show' : '';
    
                $galleryThumb[] = [
                    'thumb_image' => $imageUrl,
                    'image' => $imageUrl,
                    'thumb_class' => ' th-gallery-thumb' . $isActive,
                    'class' => 'tab-pane fade th-gallery-img th-project-gallery-img-preview' . $showClass,
                    'thumb_id' => 'img-' . ($index + 1) . '-tab',
                    'id' => 'img-' . ($index + 1),
                    'target' => '#img-' . ($index + 1),
                    'alt' => htmlspecialchars(
                        $templatedAlt,
                        ENT_QUOTES,
                        'UTF-8'
                    )
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
                    'subtitle' => $results->location,
                    'class' => 'col-md-4 text-first under-hero-item'
                ],
                [
                    'title' => 'DESIGNER',
                    'subtitle' => $results->designer,
                    'class' => 'col-md-4 text-left under-hero-item'
                ],
                [
                    'title' => 'PHOTOGRAPHER',
                    'subtitle' => $results->photographer,
                    'class' => 'col-md-4 text-left under-hero-item'
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
        $total = $this->model->where('project.status', '!=', 1)->where('project.status', '!=', 'Draft')->countAll();
        // $total = $this->model->countAll();
        $count_per_page = isset($param['item_count']) ? $param['item_count'] : 0;
        $per_page = isset($param['per_page']) ? $param['per_page'] : 0;
        $current_page = isset($param['current_page']) ? $param['current_page'] : 0;
        $limit = ($per_page * $current_page);
        if ($limit > 0) {
            $query->limit($limit);
        }
        if (isset($param['fields']) && is_array($param['fields'])) {
            $query->select(['project.credit_label']);
            $query->select($param['fields']);
        }
        $query->where('project.status_id', '!=', 1)->where('project.status', '!=', 'Draft');
        $query->orderBy('project_id', 'DESC');
        $result = $query->findAll();

        return [
            'data' => $result,
            'total' => $total,
            'show_total_pages' => $limit,
            'current_page' => $current_page ?? 1,
            'per_page' => $param['item_count'] ?? 21
        ];
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

    public function insertProjectImages(array $data, int $project_id): array
    {
        $imageData = [];
        $image_link = [];
        $config = app('config');
        $imageServer = $config['APP_URL'];

        foreach ($data as $image) {
            $img = [];
            $img['project_id'] = $project_id;
            $img['image_link'] = $image['image'];
            $image_link[] = $image['image'];
            $img['media_id'] = $image['media_id'];
            $img['image'] = json_encode([
                'name' => $image['name'],
                'objectURL' => $imageServer . $image['objectURL'],
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => ROOT_DIR . PUBLIC_PATH . $image['image'],
                'status' => $image['status'],
                'created_at' => date('Y-m-d')
            ]);
            $img['sort_order'] = 0;
            $img['media_id'] = $image['media_id'];
            $img['status'] = json_encode($image['status']);
            $img['way_points'] = json_encode([]);
            $imageData[] = $img;
        }
        if (count($imageData)) {
            $this->db->beginTransaction();
            $this->projectImage->insert($imageData);
            $this->db->commit();
        }

        return $this->galleryResponseFormat($project_id, $image_link);
    }

    private function galleryResponseFormat(int $project_id, array $image_link): array
    {
        $this->projectImage->clearQuery();

        $imageData = $this->projectImage
            ->where('project_id', '=', $project_id)
            ->whereIn('image_link', $image_link)
            ->select([
                'project_image_id',
                'project_id',
                'image_link',
                'image',
                'sort_order',
                'status',
                'created_at'
            ])
            ->orderBy('sort_order', 'ASC')
            ->findAll(false);

        $files = [];

        foreach ($imageData as $item) {
            $image = is_string($item['image'])
                ? json_decode($item['image'], true)
                : (array) $item['image'];

            $files[] = [
                'project_image_id' => $item['project_image_id'],
                'path'             => $item['image_link'],
                'name'             => $image['name'] ?? basename($item['image_link']),
                'image'            => $item['image_link'],
                'description'      => '',
                'size'             => $image['size'] ?? 0,
                'type'             => $image['type'] ?? '',
                'objectURL'        => $item['image_link'],
                'created_at'       => date('Y-m-d', strtotime($item['created_at'])),
                'file'             => [],
                'dimensions'       => null,
                'target_size'      => [],
                'status'           => $image['status'] ?? [],
                'error'            => [],
            ];
        }

        return [
            'files' => $files
        ];
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
                // 'objectURL' => $imageServer . ($item['objectURL'] ?? ''),
                'objectURL' => $item['objectURL'] ?? '',
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

                $existing = $this->model->where('title', '=', $projectData['title'])->where('project_id', '!=', $projectData['project_id'])->first();
                if ($existing) {
                    throw new Exception('Project title ' . $projectData['title'] . ' already exists. ');
                }

                // $images = $data->getImages($project->project_id);
                unset($projectData['images']);
                $project->update($projectData);
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
        $this->model->clearQuery();
        $project = $this->model->where('project_id', '=', $projectId)
            ->with([
                'images' => function ($q) {
                    return $q->select(['project_image_id', 'project_id', 'image_link', 'image', 'sort_order', 'status', 'created_at'])
                    ->orderBy('sort_order', 'ASC');
                },
                'customer' => function ($q) {
                    return $q->select(['customer.user_id as customer_id', 'CONCAT(customer.first_name, " ", customer.last_name) as name', 'customer.email as email']);
                }
            ]);

        $project = $project->first();
        if ($project && isset($project->data->images) && is_string($project->data->images)) {
            $images = json_decode($project->data->images, true);
            if (is_array($images)) {
                usort($images, function (array $a, array $b): int {
                    return (int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0);
                });
                $project->data->images = json_encode($images);
            }
        }
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
            $query->select(['project.credit_label']); // credit by
            $query->select($param['fields']); // Add other needed columns
            $query->where('project.status_id', '!=', 1)->where('project.status', '!=', 'Draft');
            $query->orderBy('project.project_id', 'DESC');
            $query->limit(4);
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
                if (isset($project['credit_label'])) {
                    $item['credit_label'] = $project['credit_label'];
                }
                if (isset($project['description'])) {
                    $item['des'] = $project['description'];
                }
                if (isset($project['preview_text'])) {
                    $item['preview_text'] = $project['preview_text'];
                }
                if (isset($project['designer'])) {
                    $item['designer'] = $project['designer'];
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
        $defaultFields = $this->getDefaultFields($headers);

        foreach ($records as $offset => $record) {
            try {
                // Ensure all required fields exist with default values
                // $record = $this->ensureAllFieldsExist($record, $headers);
                // 
                $record = isset($record['project_id']) && $record['project_id'] ? $record : array_merge($defaultFields, $record);

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
                $project = (array) $validationResult->project;

                // Check for duplicates using unique identifier
                $uniqueIdentifier = $post['project_id'] ?? ($content['slug'] ?? md5(json_encode($record)));
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updatedProjects[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }

                
                $validProjects[] = $project;
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

                $uniqueKeys = ['title'];
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
        $defaultFields = $this->getDefaultImageFields($headers);
        $records = $reader->getRecords();

        $projectImageLinks = array_column(iterator_to_array($records), 'image_link');
        $allProjectImages = $this->projectImage->select(['project_image_id', 'project_id', 'image_link'])
        ->whereIn('image_link', $projectImageLinks)->limit(0)->findAll();
        $allProjectImagesMap = array_column($allProjectImages, 'project_image_id', 'image_link');

        $validImages = [];
        $invalidImages = [];
        $updatedImages = [];
        $processedIdentifiers = [];
        $mediaData = [];

        foreach ($records as $offset => $record) {
            try {
                if(isset($record['status']) && !empty($record['status'])){
                    $record['status'] = json_encode([
                        "name" => ($record['status'] == 'show' ? 'Uploaded' : 'Pending'),
                        "severity" => ($record['status'] == 'show' ? 'success' : 'info')
                    ]);
                }else{
                    $record['status'] = json_encode([
                        "name" => 'Pending',
                        "severity" => 'info'
                    ]);
                }

                $way_points = [];
                for($i = 1; $i <= 6; $i++){
                    $way_point_name = 'way_point_'.$i.'_name';
                    $way_point_link = 'way_point_'.$i.'_link';
                    if(isset($record[$way_point_name]) && !empty($record[$way_point_name])
                    && isset($record[$way_point_link]) && !empty($record[$way_point_link])){
                        $way_points[] = [
                            'name' => $record[$way_point_name],
                            'link' => $record[$way_point_link]
                        ];
                    }
                    unset($record[$way_point_name]);
                    unset($record[$way_point_link]);
                }
                $record['way_points'] = json_encode($way_points);

                // Convert image_link to image JSON format
                if (isset($record['image_link']) && !empty($record['image_link'])) {
                    $record['image_link'] = $record['image_link'];
                    $record['image'] = $this->convertImageToJsonFormat($record['image_link'], 'gallery');
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
                if (isset($allProjectImagesMap[$record['image_link']])) {
                    $updatedImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'reason' => 'Project image ' .$record['image_link']. ' already exists in database - will be updated'
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
                if(isset($imageValidation->media) && !empty($imageValidation->media)){
                    $mediaData[] = (array) $imageValidation->media;
                }
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
        if(!empty($mediaData)){
            $this->media->upsert($mediaData, ['meta']);
            $imageLinks = array_column($mediaData, 'meta');
            $this->media->upsert($mediaData, ['path']);
            $mediaIds = $this->media->whereIn('meta', $imageLinks)->select(['meta', 'media_id'])->limit(0)->findAll();
            $mediaIdsMap = array_column($mediaIds, 'media_id', 'meta');
        }
        foreach($validImages as $key => $image){
            if(isset($mediaIdsMap[$image['image_link']])){
                $validImages[$key]['media_id'] = $mediaIdsMap[$image['image_link']];
            }else{
                $validImages[$key]['media_id'] = null;
            }
        }
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

    private function convertImageToJsonFormat(string $imageValue, string $subFolder = ""): string
    {
        $path = null;
        if ($imageValue === '' || $imageValue === null) {
            return '[]';
        }
        $imageValue = is_string($imageValue) ? $imageValue : (string)$imageValue;
        if ($this->isValidJson($imageValue)) {
            return $imageValue;
        }
        if (!str_contains($imageValue, '/media/Projects/')) {
            $path = "/media/Projects/";
        }
        if (!!$subFolder && !empty($subFolder)) {
            $path .= $subFolder . '/';
        }
        if ($path && !empty($path)) {
            $imageValue = $path . $imageValue;
        }

        
        $data = [
            [
                'id' => null,
                'file' => [
                    'name'      => basename($imageValue),
                    'size'      => 0,
                    'type'      => 'image/jpeg',
                    'error'     => 0,
                    'tmp_name'  => $imageValue,
                    'full_path' => basename($imageValue),
                ],
                'name'             => basename($imageValue),
                'size'             => 0,
                'type'             => 'image/jpeg',
                'image'            => $imageValue,
                'status'           => [
                    'name'     => 'Uploaded',
                    'severity' => 'success',
                ],
                'media_id'         => null,
                'objectURL'        => $imageValue,
                'created_at'       => '',
                'description'      => '',
                'post_image_id'    => null,
                'project_image_id' => null,
            ],
        ];

        return json_encode($data) ?: '[]';
    }

    private function getDefaultImageFields(array $headers): array
    {
        $defaults = [];
        $defaults['sort_order'] = 1;
        $defaults['status'] = json_encode([
            "active" => true,
            "featured" => false
        ]);
        $defaults['way_points'] = json_encode([]);
        $defaults['image_link'] = '';
        $defaults['image'] = json_encode([]);
        return $defaults;
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


    private function doesProjectExist(int $projectId): bool
    {
        $project = $this->model->where('project_id', '=', $projectId)->first();
        return $project !== null;
    }


    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];

        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['site_id'] = 1;
        $defaultFields['status'] = 'Draft';
        $defaultFields['status_id'] = 4;
        $defaultFields['is_featured'] = 0;

        return $defaultFields;
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

    public function updateWayPoints(array $data): array
    {
        $model_id = $data['model_id'];
        $model_type = $data['model_type'];
        $way_points = $data['way_points'];

        $model_type = $model_type ?? 'project';
        $query = null;
        if($model_type == 'project') {
           $query = $this->model->where('project_id', '=', $model_id)->first();
        } else if($model_type == 'product') {
          $query = $this->product->where('product_id', '=', $model_id)->first();
        } else if($model_type == 'post') {
          $query = $this->post->where('post_id', '=', $model_id)->first();
        }

        if (!$query) {
            return [
                'success' => false,
                'message' => 'Project not found'
            ];
        }
        $updatedData = $query->update(['banner_way_points' => json_encode($way_points)]);
        return [
            'success' => true,
            'message' => 'Way points updated successfully',
            'data' => $data
        ]; 
    }

    public function removeWayPoint(array $data): array
    {
        $model_id = $data['project_id'] ?? null;
        $point_id = $data['point_id'] ?? null;
    
        if (!$model_id || !$point_id) {
            return [
                'success' => false,
                'message' => 'Invalid project_id or point_id'
            ];
        }
    
        $query = $this->model->where('project_id', '=', $model_id)->first();
    
        if (!$query) {
            return [
                'success' => false,
                'message' => 'Project not found'
            ];
        }
    
        $way_points = $query->banner_way_points;
    
        // Decode safely
        $way_points = $way_points ? json_decode($way_points, true) : [];
    
        if (!is_array($way_points)) {
            $way_points = [];
        }
    
        // Filter out the waypoint
        $way_points = array_values(array_filter($way_points, function ($point) use ($point_id) {
            return isset($point['id']) && $point['id'] != $point_id;
        }));
    
        $updated = $query->update([
            'banner_way_points' => json_encode($way_points)
        ]);
    
        return [
            'success' => true,
            'message' => 'Way point removed successfully',
            'way_points' => $updated ? $way_points : []
        ];
    }

    public function reorderProjectImages(array $data, int $project_image_id): array
    {
        $dataMapped = [];
        $orderedItems = array_values($data);
        $projectImageIds = array_values(array_unique(array_filter(array_map(
            static fn($item) => isset($item['project_image_id']) ? (int) $item['project_image_id'] : 0,
            $orderedItems
        ))));

        if (empty($projectImageIds)) {
            return [
                'success' => true,
                'message' => 'No valid project images found to reorder',
                'data' => []
            ];
        }

        $this->projectImage->clearQuery();
        $query = $this->projectImage
            ->where('project_id', '=', $project_image_id)
            ->whereIn('project_image_id', $projectImageIds)
            ->select(['project_image_id', 'project_id', 'image_link', 'image', 'status']);
            // $query = $existingImages->getQuery();
        $existingImages = $query->orderBy('sort_order', 'ASC')->findAll();

        $existingMap = [];
        foreach ($existingImages as $existingImage) {
            $existingMap[(int) $existingImage['project_image_id']] = $existingImage;
        }

        $processedImageIds = [];
        foreach ($orderedItems as $index => $item) {
            if (!isset($item['project_image_id'])) {
                continue;
            }

            $projectImageId = (int) $item['project_image_id'];
            if (isset($processedImageIds[$projectImageId])) {
                continue;
            }

            if (!isset($existingMap[$projectImageId])) {
                continue;
            }

            $existingImage = $existingMap[$projectImageId];
            $imageLink = trim((string) ($existingImage['image_link'] ?? ''));
            if ($imageLink === '') {
                $imageLink = trim((string) ($item['image_link'] ?? $item['name'] ?? ''));
            }

            $dataMapped[] = [
                'project_image_id' => $projectImageId,
                'sort_order' => $index + 1,
                'project_id' => $project_image_id,
                'image_link' => $imageLink,
                'status' => $existingImage['status'],
                'image' => $existingImage['image']
            ];

            $processedImageIds[$projectImageId] = true;
        }

        if (empty($dataMapped)) {
            return [
                'success' => true,
                'message' => 'No matching project images found to reorder',
                'data' => []
            ];
        }

        $updated = $this->projectImage->upsert($dataMapped, ['project_image_id']);
            return [
                'success' => true,
                'message' => 'Project images reordered successfully',
                'data' => $dataMapped
            ];
    }

    /**
     * @param  list<int>  $ids  product_image_id values
     * @return array{success: bool, deleted_ids: list<int>, property: string}
     */
    public function deleteMultipleImagesById(array $ids, string $property = 'images'): array
    {
        $this->projectImage->clearQuery();
        $deleted = $this->projectImage->deleteMultiple($ids);

        if($deleted > 0){
            return [
                'success' => true,
                'deleted_ids' => $ids,
                'property' => $property,
            ];
        }
        return [
            'success' => false,
            'deleted_ids' => [],
            'property' => $property,
        ];
    }

    public function relatedProjectSearch(string $search): array
    {
        $this->model->clearQuery();
        $result = $this->model
            ->where('project.name', 'LIKE', '%' . $search . '%')
            ->orWhere('project.slug', 'LIKE', '%' . $search . '%')
            ->select(['project.project_id', 'project.name', 'project.slug', 'project.image'])
            ->limit(50)
            ->findAll(false);

        $baseUrl = env('APP_URL');
        foreach ($result as &$project) {
            // $images = json_decode($project['image'], true);
            $images =isset($project['image']) && !empty($project['image']) ? json_decode($project['image'], true) : [];
            $project['image'] = isset($images[0]['objectURL']) && !empty($images[0]['objectURL']) ? $images[0]['objectURL'] : '';
            // not more 20 characters in the description
            // $tagLine = isset($project['tag_line']) ? (string) $project['tag_line'] : '';
            // $project['description'] = strlen($tagLine) > 20 ? substr($tagLine, 0, 20) . '...' : $tagLine;
        }

        return $result;
    }
}