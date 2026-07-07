<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;

use function App\Core\System\utils\env;

/**
 * BlogController handles the blog page.
 */
class BlogController extends Controller
{
    private PostRepositoryInterface $blogRepository;

    public function __construct(
        PostRepositoryInterface $blogRepository,
        SiteRepositoryInterface $siteRepository
    )
    {
        parent::__construct($siteRepository);
        $this->blogRepository = $blogRepository;
    }

    public function index(Request $request): Response
    {
        $per_page = $request->query('per_page')?? 30;
        $current_page = $request->query('current_page')?? 1;

        $currentUrl = env('APP_URL') . '/blog';
        $imageUrl = $currentUrl . '/img/bg/Krost_Business_Furniture_2026.png';
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'website',
            'name' => "Blog | Workplace Design Insights | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Insights on workplace design, ergonomics, sustainability and commercial fit-outs from the Krost Business Furniture team.',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Commercial Office Furniture Australia | Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $this->renderResponse('index', [
            'per_page' => (int) $per_page, 
            'current_page' => (int) $current_page,
            'is_admin' => $this->isAdmin(),
            'title' => "Blogs | Krost Business Furniture",
            'type' => "website",
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'og_image' => $imageUrl,
            'product_schema' => $productSchema,
            'metaData' => [
                    'meta_title' =>  'Blog | Workplace Design Insights | Krost Business Furniture',
                    'meta_description' => 'Insights on workplace design, ergonomics, sustainability and commercial fit-outs from the Krost Business Furniture team.',
                    'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ]
        ]);
    }

    public function detail(Request $request, $slug): Response
    {
        $blogObject = $this->blogRepository->getBySlug($slug);
        if(!$blogObject) return $this->redirect('/404');
        $postContent = $blogObject?->postContent?json_decode($blogObject?->postContent, true):[];
        $postContentArray = (array) $postContent??[];
        $title = 'Krost Business Furniture';
        $name = $postContentArray['name'] ?? '';
        if($name){
            $title = $name . " | ". $title;
        }

        // Decode JSON string to PHP array
        $imageThumb = json_decode($blogObject->image_banner, true);

        $imageUrl = null;
        if (!empty($imageThumb) && isset($imageThumb[0]['objectURL'])) {
            $imageUrl = env('APP_URL') . $imageThumb[0]['objectURL'];
        }

        $currentUrl =
         'https'
        . '://'
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Blogs',
            'name' => $title,
            'description' => isset($postContentArray['meta_description']) ? $postContentArray['meta_description'] : '',
            "datePublished" => $blogObject->created_at,
            "dateModified" => $blogObject->updated_at,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Krost Business Furniture'
            ],
            'material' => '',
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


        return $this->renderResponse('detail', [
            'slug' => $slug,
            'is_admin' => $this->isAdmin(),
            'post_id' => $blogObject->post_id,
            'title' => $title,
            'type' => "article",
            'canonical' => $currentUrl,
            'blog_title' => $name ?? "Blog",
            'page' => 'blog-details',
            'url' => $currentUrl,
            'og_image' => $imageUrl,
            'product_schema' => $productSchema,
            'metaData' => [
                'meta_title' => $postContentArray['meta_title'],
                'meta_description' =>  $postContentArray['meta_description'],
                'meta_keywords' => $postContentArray['meta_keywords'],
            ],
        ]);
    }
}
