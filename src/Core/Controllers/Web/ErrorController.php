<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;

/**
 * ErrorController handles the home page.
 */
class ErrorController extends Controller
{

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
    }


    // 400, 401, 403, 404, 409, 410, 429, 500, 503

    public function fourZeroZero(): Response
    {
    return $this->renderResponse('400');
    }

    public function fourZeroOne(): Response
    {
        return $this->renderResponse('401');
    }
    
    public function fourZeroThree(): Response
    {
        return $this->renderResponse('403');
    }

    public function fourZeroFour(): Response
    {
        return $this->renderResponse('404');
    }

    public function fourZeroNine(): Response
    {
        return $this->renderResponse('409');
    }

    public function fourZeroTen(): Response
    {
        return $this->renderResponse('410');
    }

    public function fourTwentySix(): Response
    {
        return $this->renderResponse('426');
    }

    public function fourTwentyNine(): Response
    {
        return $this->renderResponse('429');
    }

    public function fiveZeroZero(): Response
    {
        return $this->renderResponse('500');
    }
    
    public function fiveZeroThree(): Response
    {
        return $this->renderResponse('503');
    }
}
