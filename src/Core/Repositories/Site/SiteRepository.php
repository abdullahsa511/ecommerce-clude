<?php

declare(strict_types=1);

namespace App\Core\Repositories\Site;

use App\Core\Models\Geoip\Country;
use App\Core\Models\Localisation\Currency;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Product\LengthType;
use App\Core\Models\Order\OrderStatus;
use App\Core\Models\Geoip\Region;
use App\Core\Models\Site\Site;
use App\Core\Models\Product\WeightType;
use App\Core\Models\Site\SiteData;
use App\Core\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;
use PDO;

class SiteRepository extends BaseRepository implements SiteRepositoryInterface
{
    protected Country $country;
    protected Region $region;
    protected Language $language;
    protected Currency $currency;
    protected OrderStatus $orderStatus;
    protected WeightType $weightType;
    // protected LengthType $lengthType;

    public function __construct(PDO $db, Language $language, Country $country, Region $region, Currency $currency, OrderStatus $orderStatus, WeightType $weightType)
    {
        parent::__construct($db, 'site', Site::class);
        $this->country = $country;
        $this->country->setDb($db);

        $this->region = $region;
        $this->region->setDb($db);

        $this->language = $language;
        $this->language->setDb($db);

        $this->currency = $currency;
        $this->currency->setDb($db);

        $this->orderStatus = $orderStatus;
        $this->orderStatus->setDb($db);

        $this->weightType = $weightType;
        $this->weightType->setDb($db);

        // $this->lengthType = $lengthType;
    }

    public function getAll(int $start = 0, int $limit = 20, ?array $siteIds = null): array
    {

        $query = $this->model->where('site_id', '>', 0);
        if($siteIds && count($siteIds)){
            $query->whereIn('site_id', $siteIds);
        }
        $query->offset($start);
        $query->limit($limit);

        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();


        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function getData(int $siteId, int $countryId, int $languageId): array
    {
        $site = $this->find($siteId);
        if (!$site) {
            return [];
        }

        return [
            'countries' => $this->getCountries(),
            'regions' => $this->getRegions($countryId),
            'languages' => $this->getLanguages(),
            'currencies' => $this->getCurrencies(),
            'orderStatuses' => $this->getOrderStatuses($languageId),
            'weightTypes' => $this->getWeightTypes(),
            // 'lengthTypes' => $this->getLengthTypes()
        ];
    }

    public function getSiteData(Site $site): array
    {
        return [
            'country' => $this->country,
            'region' => $this->region,
            'language' => $this->language,
            'currency' => $this->currency,
            'weightType' => $this->weightType,
            // 'lengthType' => $this->lengthType
        ];
    }

    public function findByKey(string $key): ?Site
    {
        $result = $this->model->findBy(['key' => $key]);
        return $result[0]?$this->model->set($result[0]):null;
    }


    public function findByHost(string $host): ?Site
    {
        $result = $this->model->findBy(['host' => $host]);
        return $result[0] ? $this->model->set($result[0]) : null;
    }

    

    public function getCountries(): Collection
    {
        $foundCountry = $this->country->select(['name', 'country_id'])
            ->where('status', '=', 1)
            ->findAll();

        return collect($foundCountry);
    }

    public function getRegions(int $countryId): array
    {
        return $this->region->select(['name', 'region_id as array_key', 'name as array_value'])
            ->where('country_id', '=', $countryId)
            ->where('status', '=', 1)
            ->findAll();
    }

    public function getLanguages(): Collection
    {
       $foundLanguages = $this->language->select(['name', 'language_id'])->findAll();

       return collect($foundLanguages);
    }

    public function getCurrencies(): Collection
    {
        $foundCurrencies = $this->currency->select(['name', 'currency_id'])
            ->where('status', '=', 1)
            ->findAll();

        return collect($foundCurrencies);
    }

    public function getOrderStatuses(int $languageId): array
    {
        return $this->orderStatus->select(['name', 'order_status_id as array_key', 'name as array_value'])
            ->where('language_id', '=', $languageId)
            ->findAll();
    }

    public function getWeightTypes(): Collection
    {
        $foundWeightTypes = $this->weightType->with(['weightTypeContent'])->findAll();
        return collect($foundWeightTypes);
    }

    // private function getLengthTypes(): array
    // {
    //     return $this->lengthType->with(['lengthTypeContent'])->findAll();
    // }

    public function createSite(SiteData $siteData): Site
    {
        $siteDataArray = $siteData->toArray();
        $site = $this->model->create($siteDataArray);
        return $site;
    }
} 
