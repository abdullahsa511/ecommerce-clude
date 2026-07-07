<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;

use function App\Core\System\utils\env;

/**
 * AboutController handles the about page.
 */
class AboutController extends Controller
{

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
    }

    public function index(): Response
    {
        $baseUrl = env('APP_URL');
        $currentUrl = $baseUrl . '/about';
        $imageUrl = $baseUrl . '/img/bg/Krost_Business_Furniture_2026.png';
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'About',
            'name' => "About Krost Business Furniture | Australian Commercial Furniture",
            'image' => [$imageUrl],
            'description' => 'Krost has designed and manufactured commercial office furniture in Australia since 1989, with showrooms in Sydney, Melbourne and Brisbane. Discover our story.',
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
        return $this->renderResponse('index', 
        [
            'manufacturingprocessdata' => [
            'videogallerymanufacturingprocess_section_title' => 'Manufacturing Process',
            'videogallerymanufacturingprocess_section_subtitle' => 'Manufacturing Process',
        ],
        'metaData' => [
                'meta_title' =>  'About Krost Business Furniture | Australian Commercial Furniture',
                'meta_description' => 'Krost has designed and manufactured commercial office furniture in Australia since 1989, with showrooms in Sydney, Melbourne and Brisbane. Discover our story.',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
        ],
        'is_admin' => $this->isAdmin(), 
        'title' => "About Us | Krost Business Furniture",
        'product_schema' => $productSchema,
        'type' => 'website',
        'canonical' => $currentUrl,
        'url' => $currentUrl,
        'og_image' => $imageUrl,
        ]);
    }

}