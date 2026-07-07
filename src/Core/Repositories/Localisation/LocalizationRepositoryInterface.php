<?php

declare(strict_types=1);

namespace App\Core\Repositories\Localisation;

use App\Core\Models\Localisation\Language;
use App\Core\Models\Localisation\Currency;
use App\Core\Models\Geoip\Country;
use App\Core\Models\Geoip\Region;
use App\Core\Models\Geoip\RegionGroup;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface LocalizationRepositoryInterface extends BaseRepositoryInterface
{
    // Language methods
    public function findLanguage(int $language_id): ?object;
    public function getAllLanguages(): array;
    public function findLanguageByName(string $name): ?Language;
    public function createLanguage(array $data): ?object;
    public function updateLanguage(int $language_id, array $data): bool;
    public function deleteLanguage(int $language_id): bool;

    // Currency methods
    public function findCurrency(int $currency_id): ?object;
    public function getAllCurrencies(): array;
    public function findCurrencyByCode(string $code): ?Currency;
    public function createCurrency(array $data): ?object;
    public function updateCurrency(int $currency_id, array $data): bool;
    public function deleteCurrency(int $currency_id): bool;

    // Country methods
    public function findCountry(int $country_id): ?object;
    public function getAllCountries(): array;
    public function findCountryByCode(string $code): ?Country;
    public function createCountry(array $data): ?object;
    public function updateCountry(int $country_id, array $data): bool;
    public function deleteCountry(int $country_id): bool;

    // Region methods
    public function findRegion(int $region_id): ?object;
    public function getAllRegions(): array;
    public function findRegionByName(string $name): ?Region;
    public function createRegion(array $data): ?object;
    public function updateRegion(int $region_id, array $data): bool;
    public function deleteRegion(int $region_id): bool;

    // Region Group methods
    public function findRegionGroup(int $region_group_id): ?object;
    public function getAllRegionGroups(): array;
    public function findRegionGroupByName(string $name): ?RegionGroup;
    public function createRegionGroup(array $data): ?object;
    public function updateRegionGroup(int $region_group_id, array $data): bool;
    public function deleteRegionGroup(int $region_group_id): bool;
} 