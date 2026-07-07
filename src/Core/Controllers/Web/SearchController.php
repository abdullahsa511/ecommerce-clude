<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Repositories\Taxonomy\TaxonomyItemRepositoryInterface;
use Exception;

/**
 * HomeController handles the home page.
 */
class SearchController extends Controller
{
    private TaxonomyItemRepositoryInterface $categoryRepository;

    public function __construct(
        TaxonomyItemRepositoryInterface $categoryRepository,
        SiteRepositoryInterface $siteRepository
    )
    {
        parent::__construct($siteRepository);
        $this->categoryRepository = $categoryRepository;
    }
    
    public function search(Request $request): Response
    {
        $search = $request->query('query');
        // echo $search;
        // exit;
        return $this->renderResponse('search', ['category' => 'workstation', 'slug' => 'alex', 'title' => "Search Results | Krost Business Furniture"]);
        // return $this->renderResponse('search', ['query' => $search]);
    }
}
