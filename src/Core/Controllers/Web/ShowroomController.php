<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;

/**
 * AboutController handles the about page.
 */
class ShowroomController extends Controller
{

    private ShowroomRepositoryInterface $showroomRepository;

    public function __construct(
        ShowroomRepositoryInterface $showroomRepository,
        SiteRepositoryInterface $siteRepository
    )
    {
        parent::__construct($siteRepository);
        $this->showroomRepository = $showroomRepository;
    }

    public function index(Request $request, string $slug): Response
    { 
        $showroomObject = $this->showroomRepository->findBySlug($slug);
        $showroomArray = (array) $showroomObject??[];
        $title = 'Krost Business Furniture';
        $name = $showroomArray['title'] ?? '';
        if($name){
            $title = $name . " | ". $title;
        }
        return $this->renderResponse('index', [
            'meta_description' => $title,
            'metaData' => [
                'meta_title' =>  $title,
                'meta_description' => 'Krost Business Furniture - Australian commercial furniture manufacturer since 1989. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations'
            ],
            'slug' => $slug,
            'is_admin' => $this->isAdmin(),
            'title' => $title
        ]);
    }
    public function details(Request $request, string $name): Response
    {
        $showroomObject = $this->showroomRepository->getBySlug($name);
        $showroomArray = (array) $showroomObject?->data??[];
        $title = 'Krost Business Furniture';
        $name = $showroomArray['title'] ?? '';
        if($name){
            $title = $name . " | ". $title;
        }
        return $this->renderResponse('details', ['showroom_name' => $name, 'title' => $title, 'is_admin' => $this->isAdmin()]);
    }


}
