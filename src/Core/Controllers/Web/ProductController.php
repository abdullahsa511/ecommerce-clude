<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Taxonomy\TaxonomyItemRepositoryInterface;
use Exception;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;

use function App\Core\System\utils\env;

/**
 * HomeController handles the home page.
 */
class ProductController extends Controller
{
    private TaxonomyItemRepositoryInterface $categoryRepository;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        TaxonomyItemRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        SiteRepositoryInterface $siteRepository
    )
    {
        parent::__construct($siteRepository);
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }
    public function product(Request $request, string $category, string $product): Response
    {
        
        $productObject = $this->productRepository->getBySlug($product);
        if((isset($productObject->data?->active) && $productObject->data?->active == 0) 
        || (isset($productObject->data?->archive) && $productObject->data?->archive == 1)
        || !isset($productObject->data?->status) || $productObject->data?->status == 0){
            return $this->redirect('/404', 404);
        }
        $metaData = [
            'meta_description' => '',
            'meta_keywords' => '',
            'meta_content' => '',
            'ld_json' => ''
        ];
        if($productObject && $productObject->data?->product_id){
            $metadata = $this->productRepository->getProductMetadata($productObject?->data->product_id);
            $metaData = $metadata['enSeo']??[
                'meta_description' => '',
                'meta_keywords' => '',
                'meta_content' => '',
                'ld_json' => ''
            ];
            $metaData['ld_json'] = json_encode([
                "@context" => "https://schema.org",
                "@type" => "Product",
                "name" => $productObject->product_title,
                "description" => $metaData['meta_description']??'',
                "brand" => [
                    "@type" => "Brand",
                    "name" => "Krost Business Furniture"
                ]
            ]);
        }
        $productArray = (array) $productObject?->data??[];
        
        $baseUrl = env('APP_URL');
        $imageThumb = $productArray['image_thumb'] ?? null;

        if (is_string($imageThumb)) {
            $imageThumb = json_decode($imageThumb, true);
        }
        
        $imageUrl = isset($imageThumb[0]['objectURL'])
            ? $baseUrl. $imageThumb[0]['objectURL']
            : '';
        
        // echo $imageUrl;

        // echo "<pre>";
        // print_r($productArray);
        // echo "<pre>";
        // exit;

        $title = 'Krost Business Furniture';
        $name = $productArray['product_title'] ?? '';
        if($name){
            $title = $name . " | ". $title;
        }
        
        $currentUrl =
             'https'
            . '://'
            . $_SERVER['HTTP_HOST']
            . $_SERVER['REQUEST_URI'];

        // echo $currentUrl;
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
        
            'name' => $productArray['title'] ?? '',
        
            'description' => strip_tags($productArray['short_description'] ?? ''),
        
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Krost Business Furniture'
            ],
        
            'material' => isset($productArray['material']) ? $productArray['material'] : '',
        
            'url' => $currentUrl
        ];
        if($imageUrl){
            $schema['image'] = [
                $imageUrl
            ];
        }
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        return $this->renderResponse('product', [
            'category' => $category, 
            'slug' => $product,
            'title' => $title,
            'url' => $currentUrl,
            'og_image' => $imageUrl,
            'metaData' => [
                'meta_title' =>  isset($productArray['meta_title'])? $productArray['meta_title'] : '',
                'meta_description' => isset($productArray['meta_description']) ? $productArray['meta_description'] : '',
                'meta_keywords' => isset($productArray['meta_keywords']) ? $productArray['meta_keywords'] : '',
            ],
            'is_admin' => $this->isAdmin(),
            'product_schema' => $productSchema,
            'catalogue_link' => $productArray['catalogue_link'] ?? '#'
        ]);
    }
    public function search(Request $request): Response
    {
        $search = $request->query('query');
        // echo $search;
        // exit;
        return $this->renderResponse('search', ['category' => 'workstation', 'slug' => 'alex']);
        // return $this->renderResponse('search', ['query' => $search]);
    }

    public function category(Request $request, string $category): Response
    {

        // echo 'abdullah cate'; exit;
        $productLink = '/products/'.$category;
        $category = $this->categoryRepository->getCategoryByProductLink($productLink);
        $title = 'Krost Business Furniture';
        $categoryName = $category['name'] ?? '';
        if($categoryName){
            $title = $categoryName . " | ". $title;
        }
        if(!$category) {
            return $this->renderResponse('404', []);
        }
        if(isset($category['parent'])) {
            $parentCategory = $category['parent']['slug']??$category['slug'];
            $subcategory = $category['slug'];
        }else{
            $parentCategory = $category['slug'];  
            $subcategory = $category['slug'];
        }
        $currentUrl =
        'https'
        . '://'
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];
        return $this->renderResponse('category', [
            'category' => $parentCategory, 
            'subcategory' => $subcategory,
            'section_subtitle' => $category['content'],
            'is_admin' => $this->isAdmin(),
            'metaData' => [
                'meta_title' => $title,
                'meta_description' => $category['meta_description'],
                'meta_keywords' => $category['meta_keywords'],
            ],
            'title' => $title,
            'canonical' => $currentUrl
        ]);
    }

    public function subcategory(Request $request, string $category, string $subcategory): Response
    {
        return $this->renderResponse('subcategory', ['category' => $category, 'subcategory' => $subcategory]);
    }

    public function products(Request $request): Response
    {
        return $this->renderResponse('products', $request->query());
    }

    public function productList(Request $request): Response
    {
        return $this->renderResponse('product-list', $request->query());
    }

    public function searchResults(Request $request): Response
    {
        $query = $request->query('query');
        $baseUrl = env('APP_URL');

        // echo $currentUrl;
        $currentUrl =
        'https'
        . '://'
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];

        $pageUrl = $baseUrl . '/search/results';
        $imageUrl = $baseUrl . '/img/bg/Krost_Business_Furniture_2026.png';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => '',
            'description' => '',
            'image' => [
                $imageUrl
            ],
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Krost Business Furniture'
            ],
            'material' => '',
            'url' => $pageUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $this->renderResponse('search-results', [
            'query' => $query, 
            'title' => $query ? "Search Results - " . $query ." | Krost Business Furniture" : "Search Results | Krost Business Furniture",
            'url' => $pageUrl,
            'canonical' => $currentUrl,
            'og_image' => $imageUrl,
            'type'=> 'website',
            'metaData' => [
                'meta_title' => $query ? 'Search results for ' . $query : 'All Results',
                'meta_description' => 'Krost Business Furniture - Australian commercial furniture manufacturer since 1989. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ],
            'is_admin' => $this->isAdmin(),
            'product_schema' => $productSchema,
            'robots' => true
        ]);
    }
}
