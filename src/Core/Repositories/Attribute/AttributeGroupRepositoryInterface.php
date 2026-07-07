<?php

declare(strict_types=1);

namespace App\Core\Repositories\Attribute;

use App\Core\Models\Attribute\AttributeGroup;

interface AttributeGroupRepositoryInterface
{
    /**
     * Get all attribute groups with pagination and filtering
     * 
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @param int|null $sort_order Filter by sort order
     * @return array{data: array, total: int}
     */
    public function getAll(int $start = 0, int $limit = 10, ?int $sort_order = null): array;

    /**
     * Get attribute group by ID
     * 
     * @param int $attribute_group_id
     * @return |null
     */
    public function get($attribute_group_id);

    public function findByName(string $name): ?AttributeGroup;
    public function findByCode(string $code): ?AttributeGroup;

    public function getAllAttributeGroups(): array;

    /**
     * Add new attribute group
     * 
     * @param array $data Attribute group data
     * @return array|null
     */
    public function add(array $data): array;

    /**
     * Update attribute group
     * 
     * @param int $attribute_group_id
     * @param array $data Attribute group data
     * @return bool
     */
    public function edit(int $attribute_group_id, array $data): ?AttributeGroup;

     /**
     * Update attribute group
     * 
     * @param array $data Attribute group data
     * @return array|null
     */
    public function updateAttributeGroups($data, $id): array;

    /**
     * Delete attribute group
     * 
     * @param int $attribute_group_id
     * @return AttributeGroup|null
     */
    public function deleteAttributeGroup(int $attribute_group_id): ?AttributeGroup;

    /**
     * Get attribute group with its content
     * 
     * @param int $attribute_group_id
     * @return AttributeGroup|null
     */
    public function getWithContent(int $attribute_group_id): ?AttributeGroup;

    /**
     * Get attribute group with its attributes
     * 
     * @param int $attribute_group_id
     * @return AttributeGroup|null
     */
    public function getWithAttributes(int $attribute_group_id): ?AttributeGroup;
    // import csv data
    public function importAttributeGroups(string $csv_file): array;

    public function getAllAttributeGroupById($id);
} 