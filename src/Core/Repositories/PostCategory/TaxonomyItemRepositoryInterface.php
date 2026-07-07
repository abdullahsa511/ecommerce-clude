<?php

declare(strict_types=1);

namespace App\Core\Repositories\PostCategory;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\PostCategory\TaxonomyItemData;

interface TaxonomyItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all taxonomy items with pagination
     *
     * @param int $languageId
     * @param int $start
     * @param int $limit
     * @return array{list: array<TaxonomyItem>, total: int}
     */
    public function getAll(int $languageId, int $start, int $limit): array;

    /**
     * Get a single taxonomy item by ID
     *
     * @param int $taxonomyItemId
     * @return TaxonomyItem|null
     */
    public function get(int $taxonomyItemId): ?TaxonomyItem;
    // public function getCategoryBySlug(string $slug): ?TaxonomyItem;

    
    public function getTaxonomyItems(int $taxonomyId, array $fields): array;
    public function getTaxonomyItemsByTaxonomyId(int $taxonomyId, array $fields): array;
    public function getTaxonomyItemsByTaxonomyIds(array $taxonomyIds, array $fields): array;
    public function insertTaxonomyItemContents(array $data): bool;
    
    /**
     * Create a new taxonomy item with content
     *
     * @param TaxonomyItemData $data
     * @return TaxonomyItem|null
     */
    public function createTaxonomyItem(TaxonomyItemData $data): ?TaxonomyItem;
    
    /**
     * Update a taxonomy item with content
     *
     * @param int $taxonomyItemId
     * @param TaxonomyItemData $data
     * @return TaxonomyItem|null
     */
    public function updateTaxonomyItem(int $taxonomyItemId, TaxonomyItemData $data): ?TaxonomyItem;
    
    /**
     * Delete a taxonomy item and its content
     *
     * @param int $taxonomyItemId
     * @return bool
     */
    public function deleteTaxonomyItem(int $taxonomyItemId): bool;
    
    /**
     * Show a taxonomy item with content
     *
     * @param int $taxonomyItemId
     * @return TaxonomyItem|null
     */
    public function showTaxonomyItem(int $taxonomyItemId): ?TaxonomyItem;
    public function updateCategoryOrder(array $data): array;
    public function updateTaxonomyItemImage(array $files, string $property, int $taxonomy_item_id): bool;
    public function deleteTaxonomyItemImage(int $taxonomy_item_id, string $property): bool;
} 