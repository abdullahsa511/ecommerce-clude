<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;

/**
 * SolutionController handles the solution page.
 */
class SolutionController extends Controller
{

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
    }

    public function index(): Response
    {
        return $this->renderResponse('index');
    }

}
