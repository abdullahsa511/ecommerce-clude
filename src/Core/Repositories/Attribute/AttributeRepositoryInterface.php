<?php

declare(strict_types=1);

namespace App\Core\Repositories\Attribute;

use App\Core\Models\Attribute\Attribute;
use Illuminate\Support\Arr;

interface AttributeRepositoryInterface
{
    /**
     * Get all attributes with pagination and filtering
     * 
     * @param int $language_id Language ID for content
     * @param array|null $product_id Array of product IDs to filter by
     * @param int|null $attribute_group_id Filter by attribute group ID
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @param string|null $search Search term for attribute name
     * @return array{data: array, total: int}
     */
    public function getAll(
        int $language_id,
        ?array $product_id = null,
        ?int $attribute_group_id = null,
        int $start = 0,
        int $limit = 10,
        ?string $search = null
    ): array;

    /**
     * Get attribute by ID with content
     * 
     * @param int $attribute_id Attribute ID
     * @param int $language_id Language ID for content
     * @return Attribute|null
     */
    public function get(int $attribute_id, int $language_id): ?Attribute;

    /**
     * Add new attribute with content
     * 
     * @param array $attribute Attribute data
     * @param array $attribute_content Attribute content data
     * @param int $language_id Language ID for content
     * @return Attribute|null
     */
    public function add(array $attribute, array $attribute_content, int $language_id): ?Attribute;

    /**
     * Update attribute and its content
     * 
     * @param int $attribute_id Attribute ID
     * @param array $attribute Attribute data
     * @param array $attribute_content Attribute content data
     * @return bool
     */
    public function edit(int $attribute_id, array $attribute, array $attribute_content): bool;

    /**
     * Delete attribute and its content
     * 
     * @param array $attribute_id Array of attribute IDs to delete
     * @return bool
     */
    public function delete(int $attribute_id): bool;
    public function getAllAttributes(): array;
    public function getAllAttributeById($id);
    public function importAttributes(string $csv_file): array;
    public function createAttributes($data): array;
    public function updateAttributes($data, $id): array;
    public function deleteAttributes(int $id): bool;
    public function deleteMultipleAttributes(array $attribute_ids): bool;
    public function getAllAttributeGroups(): array;

} 