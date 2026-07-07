<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;

/**
 * DashboardController handles the dashboard page.
 */
class DashboardController extends Controller
{

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
    }



}
