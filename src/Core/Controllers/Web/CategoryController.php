<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Product\CategoryRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;

use function App\Core\System\utils\env;

/**
 * HomeController handles the home page.
 */
class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;
    
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SiteRepositoryInterface $siteRepository
    )
    {
        parent::__construct($siteRepository);
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): Response
    {
        // return $this->renderResponse('index', [
        //     'metaData' => [
        //         'meta_title' => 'Product Categories | Krost Business Furniture',
        //         'meta_description' => "Australia's commercial furniture specialist since 1989 — workstations, seating, desks and joinery designed and supplied nationwide. Sydney, Melbourne & Brisbane. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story",
        //         'meta_keywords' => 'commercial office furniture, office furniture Australia, workstations, office seating, office chairs, office desks, meeting tables, office storage, commercial joinery, office screens',
        //     ], 
        //     'is_admin' => $this->isAdmin(), 
        //     'title' => "Product Categories | Krost Business Furniture"
        // ]);

        $currentUrl = env('APP_URL') . '/categories';            
        $imageUrl =  env('APP_URL') . '/img/bg/Krost_Business_Furniture_2026.png';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Catalogue Page',
            'name' => "Product Categories | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Explore the full range of Krost commercial office furniture by category — workstations, seating, desks, tables, storage, joinery and more.',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Product Categories | Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $this->renderResponse('index', [
            'page' => 'categories',
            'is_admin' => $this->isAdmin(),
            'title' => "Product Categories | Krost Business Furniture",
            'metaData' => [
                'meta_title' =>  'Product Categories | Krost Business Furniture',
                'meta_description' => 'Explore the full range of Krost commercial office furniture by category — workstations, seating, desks, tables, storage, joinery and more.',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ],
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'og_image'=> $imageUrl,
            'type'=> 'website',
            'product_schema' => $productSchema
        ]);
    }
    public function details(Request $request, $category, $subcategory = null): Response
    {
        // echo 'abdullah';
        // exit;
        $title = 'Krost Business Furniture';
        $categoryObject = $this->categoryRepository->getCategoryBySlug(1, 1, 1, $category);
        $categoryName = $categoryObject['name'] ?? '';
        if($categoryName){
            $title = $categoryName . " | ". $title;
        }
        $metaData = [
            'meta_title' => $categoryObject['meta_title'] ?? $title,
            'meta_description' => $categoryObject['meta_description'] ?? "Australia's commercial furniture specialist since 1989 — workstations, seating, desks and joinery designed and supplied nationwide. Sydney, Melbourne & Brisbane. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story",
            'meta_keywords' => $categoryObject['meta_keywords'] ?? 'commercial office furniture, office furniture Australia, workstations, office seating, office chairs, office desks, meeting tables, office storage, commercial joinery, office screens',
        ];
        $currentUrl =
        'https'
        . '://'
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];
        
        if($category == 'seating' && $subcategory == null){
            return $this->renderResponse('details-seating', 
            ['category' => $category, 
            'subcategory' => $category, 
            'title' => $title,
            'metaData' => $metaData, 
            'is_admin' => $this->isAdmin(),
            'canonical' => $currentUrl
        ]);
        }elseif($category == 'workstations' && $subcategory == null){
            return $this->renderResponse('details-workstations', [
                'category' => $category, 
                'subcategory' => $category, 
                'title' => $title,
                'metaData' => $metaData, 
                'is_admin' => $this->isAdmin(),
                'canonical' => $currentUrl
            ]);
        }else{
            return $this->renderResponse('details', [
                'category' => $category, 
                'subcategory' => $category, 
                'title' => $title,
                'metaData' => $metaData, 
                'is_admin' => $this->isAdmin(),
                'canonical' => $currentUrl
            ]);
        }
    }

}
