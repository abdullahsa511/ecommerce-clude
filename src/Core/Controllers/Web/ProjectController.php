<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use Exception;
use App\Core\Repositories\Project\ProjectRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;

use function App\Core\System\utils\env;

/**
 * ProjectController handles the project page.
 */
class ProjectController extends Controller
{
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        SiteRepositoryInterface $siteRepository
    )
    {
        parent::__construct($siteRepository);
        $this->projectRepository = $projectRepository;
    }

    public function index(Request $request): Response
    {
        $per_page = $request->query('per_page')?? 30;
        $current_page = $request->query('current_page')?? 1;

        $currentUrl = env('APP_URL') . '/projects';
        $imageUrl = $currentUrl . 'img/bg/Krost_Business_Furniture_2026.png';
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => "Projects | Commercial Office Fit-outs | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Krost Business Furniture - Australian commercial furniture manufacturer since 1989. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Projects | Commercial Office Fit-outs | Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $this->renderResponse('project-index', [
            'per_page' => (int) $per_page, 
            'current_page' => (int) $current_page,
            'is_admin' => $this->isAdmin(),
            'title' => "Projects | Krost Business Furniture",
            'type' => "website",
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'og_image' => $imageUrl,
            'metaData' => [
                    'meta_title' =>  'Projects | Commercial Office Fit-outs | Krost Business Furniture',
                    'meta_description' => "Explore Krost's portfolio of commercial office fit-outs across Australia — real workplaces furnished with Krost workstations, seating and storage.",
                    'meta_keywords' => 'commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ],
            'product_schema' => $productSchema
        ]);
    }

    public function projectDetail(Request $request, $slug): Response
    {
        // if($slug == 'project-1'){
        //     return $this->renderResponse('project-detail');
        // }
        $projectObject = $this->projectRepository->getBySlug($slug);
        $projectArray = (array) $projectObject?->data??[];

        $imageThumbnail = isset($projectArray['image_thumb']) ? $projectArray['image_thumb'] : [];

        if (is_string($imageThumbnail)) {
            $imageThumbnail = json_decode($imageThumbnail, true);
        }
        $projectId = isset($projectArray['project_id']) ? $projectArray['project_id'] : '';
        // echo '<pre>';
        // print_r($projectObject);
        // echo '</pre>';
        
        $imageUrl = env('APP_URL') . $imageThumbnail[0]['objectURL'] ?? '';

        $title = 'Krost Business Furniture';
        $name = $projectArray['name'] ?? '';
        if($name){
            $title = $name . " | ". $title;
        }

        $currentUrl =
        'https'
        . '://'
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Projects',
            'name' => $title,
            'image' => [
                $imageUrl
            ],
            'description' => isset($projectArray['meta_description']) ? $projectArray['meta_description'] : '',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );


        return $this->renderResponse('project-detail', 
        [
            'slug' => $slug,
            'is_admin' => $this->isAdmin(), 
            'title' => $title, 
            'project_title' => $name??' this project ',
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'type' => "Projects",
            'og_image' => $imageUrl,
            'project_id' => $projectId,
            'page' => 'project-details',
            'metaData' => [
                'meta_title' => isset($projectArray['meta_title']) ? $projectArray['meta_title'] : '',
                'meta_description' => isset($projectArray['meta_description']) ? $projectArray['meta_description'] : '',
                'meta_keywords' => isset($projectArray['meta_keywords']) ? $projectArray['meta_keywords'] : '',
            ],
            'product_schema' => $productSchema
        ]);
    }

}