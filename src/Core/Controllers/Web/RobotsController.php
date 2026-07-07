<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;

use function App\Core\System\utils\env;

/**
 * Serves dynamic robots.txt with sitemap URL from APP_URL.
 */
class RobotsController extends Controller
{
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        return parent::__construct($siteRepository);
    }
    public function index(): Response
    {
        if ($this->isStagingSite()) {
            $content = <<<'ROBOTS'
# Block all 

User-agent: *
Disallow: /
ROBOTS;

            return new Response($content, 200, ['Content-Type' => 'text/plain']);
        }

        $baseUrl = rtrim(env('APP_URL', 'http://localhost:8089'), '/');

        $content = <<<ROBOTS
# robots.txt

# Allow all search engines
User-agent: *
Disallow:

# Disallow Search Results Pages from Google Indexing
Disallow: /search
Disallow: /search/results

# Disallow
Disallow: /app/
Disallow: /vendor/
Disallow: /private/      # if you have any private folder
Disallow: /config/       # config files
Disallow: /src/


# Disallow
Disallow: /install.php
Disallow: /setup.php

# Allow media 
Allow: /media/

# Allow
Sitemap: {$baseUrl}/sitemap.xml
ROBOTS;

        return new Response($content, 200, ['Content-Type' => 'text/plain']);
    }

    private function isStagingSite(): bool
    {
        $host = strtolower((string) ($this->request->header('Host') ?? ''));
        $host = explode(':', $host)[0];

        if ($host === 'krost.business' || str_ends_with($host, '.krost.business')) {
            return true;
        }

        $appUrl = strtolower((string) env('APP_URL', ''));
        return str_contains($appUrl, 'krost.business');
    }
}
