<?php

declare(strict_types=1);

namespace App\Core\Repositories\Site;

use App\Core\Models\Site\Site;
use App\Core\Models\Site\SiteData;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface SiteRepositoryInterface extends BaseRepositoryInterface
{

    public function getAll(int $start = 0, int $limit = 20, ?array $siteIds = null): array;
    public function getCountries(): Collection;
    public function getRegions(int $countryId): array;
    public function getLanguages(): Collection;
    public function getCurrencies(): Collection;
    public function getOrderStatuses(int $languageId): array;
    public function getWeightTypes(): Collection;
    public function getSiteData(Site $site): array;
    public function getData(int $siteId, int $countryId, int $languageId): array;
    public function findByKey(string $key): ?Site;
    public function findByHost(string $host): ?Site;
    public function createSite(SiteData $siteData): Site;
}
