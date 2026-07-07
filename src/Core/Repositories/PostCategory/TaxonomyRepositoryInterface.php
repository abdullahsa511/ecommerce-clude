<?php

declare(strict_types=1);

namespace App\Core\Repositories\PostCategory;

use App\Core\Models\PostCategory\Taxonomy;
use App\Core\Repositories\Base\BaseRepositoryInterface;
interface TaxonomyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all taxonomies with pagination and filtering
     * 
     * @param int|null $taxonomyItemId
     * @param int $start
     * @param int $limit
     * @param string|null $postType
     * @param string|null $type
     * @return array{list: array<Taxonomy>, total: int}
     */
    public function getAll(
        ?int $taxonomyItemId,
        int $start,
        int $limit,
        ?string $postType = null,
        ?string $type = null
    ): array;

    public function getTaxonomy(int $taxonomyId): ?Taxonomy;
    public function insertTaxonomyContents(array $data): bool;
    public function getFinishTaxonomyIds(): array;
    public function getTagTaxonomyIds(): array;
    public function importTaxonomies(string $csv_file): array;
    public function getTaxonomyTypes(): array;
} 