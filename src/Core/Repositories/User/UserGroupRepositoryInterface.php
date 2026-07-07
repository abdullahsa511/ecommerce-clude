<?php

namespace App\Core\Repositories\User;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface UserGroupRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all user groups with content
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single user group with content by ID
     * 
     * @param int $userGroupId
     * @return array|null
     */
    public function get(int $userGroupId): ?array;

    /**
     * Import user groups from CSV file
     * 
     * @param string $csv_file
     * @return array
     */
    public function importUserGroups(string $csv_file): array;

} 