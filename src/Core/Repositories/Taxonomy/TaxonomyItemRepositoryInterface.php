<?php

declare(strict_types=1);

namespace App\Core\Repositories\Taxonomy;

interface TaxonomyItemRepositoryInterface
{
    public function getByTaxonomyId(int $taxonomyId, ?int $parentId = null): array;
    public function getChildren(int $parentId): array;
    public function getParents(int $taxonomyItemId): array;
    public function getByStatus(int $status): array;
    public function getByPostType(string $postType): array;
    public function getBySiteId(int $siteId): array;
    public function insertTaxonomyItemContents(array $data): bool;
    public function importTaxonomyItems(string $csv_file, int $taxonomy_id): array;
    public function getCategoryByProductLink(string $productLink): ?array;
}
