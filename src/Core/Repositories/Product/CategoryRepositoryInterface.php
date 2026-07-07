<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\ProductCategory;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories with pagination and filtering
     */
    public function getCategories(int $languageId, int $taxonomyId, int $siteId, ?string $search = null, ?string $type = null, int $start = 0, int $limit = 10): Collection;

    /**
     * Get categories with pages
     */
    public function getCategoriesPages(int $languageId, int $taxonomyId, int $siteId, ?string $search = null, ?string $type = null): int;

    /**
     * Get a single category by ID
     */
    public function getCategory(int $id): ?ProductCategory;

    /**
     * Get category by slug
     */
    public function getCategoryBySlug(int $languageId, int $taxonomyId, int $siteId, string $slug): ?array;

    /**
     * Edit category
     */
    public function editCategory(int $id, array $data): ProductCategory;

    /**
     * Add new category
     */
    public function addCategory(int $languageId, int $taxonomyId, int $siteId, array $data): int;

    /**
     * Get categories in all languages
     */
    public function getCategoriesAllLanguages(int $taxonomyId, int $siteId, ?string $search = null, ?string $type = null, int $start = 0, int $limit = 10): array;

    /**
     * Edit taxonomy item
     */
    public function editTaxonomyItem(int $languageId, int $taxonomyId, int $siteId, int $postId, array $data): bool;

    /**
     * Add new taxonomy item
     */
    public function addTaxonomyItem(int $languageId, int $taxonomyId, int $siteId, array $data): int;

    /**
     * Update taxonomy items
     */
    public function updateTaxonomyItems(int $languageId, int $taxonomyId, int $siteId, array $data): bool;

    /**
     * Delete taxonomy item
     */
    public function deleteTaxonomyItem(int $languageId, int $taxonomyId, int $siteId, int $postId): bool;

    /**
     * Get categories for masonry component
     */
    public function getCategoriesForMasonryComponent(int $languageId, int $taxonomyId, int $siteId): array;

    /**
     * Get categories for slider nav component
     */
    public function getCategoriesForSliderNavComponent(int $languageId = 1, int $taxonomyId = 1, int $siteId = 1): array;

    public function getCategorySeatingDetailsComponentData(array $param): array;
    public function getCategoryWorkstationDetailsComponentData(array $param): array;
    public function getCategoryHeroComponentData(array $param): array;
    public function getHeaderMenu(bool $is_logged_in): array;
}
