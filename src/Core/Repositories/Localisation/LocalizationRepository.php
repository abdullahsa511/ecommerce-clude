<?php

declare(strict_types=1);

namespace App\Core\Repositories\Localisation;

use App\Core\Models\Localisation\Language;
use App\Core\Models\Localisation\Currency;
use App\Core\Models\Geoip\Country;
use App\Core\Models\Geoip\Region;
use App\Core\Models\Geoip\RegionGroup;
use App\Core\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;
use PDO;

class LocalizationRepository extends BaseRepository implements LocalizationRepositoryInterface
{
    private Language $languageModel;
    private Currency $currencyModel;
    private Country $countryModel;
    private Region $regionModel;
    private RegionGroup $regionGroupModel;

    public function __construct(
        PDO $db, 
        Language $languageModel, 
        Currency $currencyModel, 
        Country $countryModel, 
        Region $regionModel, 
        RegionGroup $regionGroupModel )
    {
        parent::__construct($db, '', null);
        $this->languageModel = $languageModel;
        $this->currencyModel = $currencyModel;
        $this->countryModel = $countryModel;
        $this->regionModel = $regionModel;
        $this->regionGroupModel = $regionGroupModel;
    }

    // Language methods
    public function findLanguage(int $language_id): ?object
    {
        return $this->languageModel->find($language_id);
    }

    public function getAllLanguages(): array
    {
        $this->languageModel->orderBy('name', 'ASC');
        $result = $this->languageModel->findAll();
        $items = collect($result);
        $totalRecords = $this->languageModel->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findLanguageByName(string $name): ?Language
    {
        $this->languageModel->where('name', '=', $name);
        $this->languageModel->limit(1);
        $results = $this->languageModel->findAll();
        return !empty($results) ? $this->languageModel->set($results[0]) : null;
    }

    public function createLanguage(array $data): ?object
    {
        return $this->languageModel->create($data);
    }

    public function updateLanguage(int $language_id, array $data): bool
    {
        return $this->languageModel->update($language_id, $data);
    }

    public function deleteLanguage(int $language_id): bool
    {
        return $this->languageModel->delete($language_id);
    }

    // Currency methods
    public function findCurrency(int $currency_id): ?object
    {
        return $this->currencyModel->find($currency_id);
    }

    public function getAllCurrencies(): array
    {
        $this->currencyModel->orderBy('code', 'ASC');
        $result = $this->currencyModel->findAll();
        $items = collect($result);
        $totalRecords = $this->currencyModel->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findCurrencyByCode(string $code): ?Currency
    {
        $this->currencyModel->where('code', '=', $code);
        $this->currencyModel->limit(1);
        $results = $this->currencyModel->findAll();
        return !empty($results) ? $this->currencyModel->set($results[0]) : null;
    }

    public function createCurrency(array $data): ?object
    {
        return $this->currencyModel->create($data);
    }

    public function updateCurrency(int $currency_id, array $data): bool
    {
        return $this->currencyModel->update($currency_id, $data);
    }

    public function deleteCurrency(int $currency_id): bool
    {
        return $this->currencyModel->delete($currency_id);
    }

    // Country methods
    public function findCountry(int $country_id): ?object
    {
        return $this->countryModel->find($country_id);
    }

    public function getAllCountries(): array
    {
        $this->countryModel->orderBy('name', 'ASC');
        $result = $this->countryModel->findAll();
        $items = collect($result);
        $totalRecords = $this->countryModel->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findCountryByCode(string $code): ?Country
    {
        $this->countryModel->where('code', '=', $code);
        $this->countryModel->limit(1);
        $results = $this->countryModel->findAll();
        return !empty($results) ? $this->countryModel->set($results[0]) : null;
    }

    public function createCountry(array $data): ?object
    {
        return $this->countryModel->create($data);
    }

    public function updateCountry(int $country_id, array $data): bool
    {
        return $this->countryModel->update($country_id, $data);
    }

    public function deleteCountry(int $country_id): bool
    {
        return $this->countryModel->delete($country_id);
    }

    // Region methods
    public function findRegion(int $region_id): ?object
    {
        return $this->regionModel->find($region_id);
    }

    public function getAllRegions(): array
    {
        $this->regionModel->orderBy('name', 'ASC');
        $result = $this->regionModel->findAll();
        $items = collect($result);
        $totalRecords = $this->regionModel->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findRegionByName(string $name): ?Region
    {
        $this->regionModel->where('name', '=', $name);
        $this->regionModel->limit(1);
        $results = $this->regionModel->findAll();
        return !empty($results) ? $this->regionModel->set($results[0]) : null;
    }

    public function createRegion(array $data): ?object
    {
        return $this->regionModel->create($data);
    }

    public function updateRegion(int $region_id, array $data): bool
    {
        return $this->regionModel->update($region_id, $data);
    }

    public function deleteRegion(int $region_id): bool
    {
        return $this->regionModel->delete($region_id);
    }

    // Region Group methods
    public function findRegionGroup(int $region_group_id): ?object
    {
        return $this->regionGroupModel->find($region_group_id);
    }

    public function getAllRegionGroups(): array
    {
        $this->regionGroupModel->orderBy('name', 'ASC');
        $result = $this->regionGroupModel->findAll();
        $items = collect($result);
        $totalRecords = $this->regionGroupModel->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findRegionGroupByName(string $name): ?RegionGroup
    {
        $this->regionGroupModel->where('name', '=', $name);
        $this->regionGroupModel->limit(1);
        $results = $this->regionGroupModel->findAll();
        return !empty($results) ? $this->regionGroupModel->set($results[0]) : null;
    }

    public function createRegionGroup(array $data): ?object
    {
        return $this->regionGroupModel->create($data);
    }

    public function updateRegionGroup(int $region_group_id, array $data): bool
    {
        return $this->regionGroupModel->update($region_group_id, $data);
    }

    public function deleteRegionGroup(int $region_group_id): bool
    {
        return $this->regionGroupModel->delete($region_group_id);
    }
} 