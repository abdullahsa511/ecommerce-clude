<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\User;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface as BaseUserRepositoryInterface;

interface UserRepositoryInterface extends BaseUserRepositoryInterface, BaseRepositoryInterface
{
    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
    public function findByUserId(int $user_id): ?User;

    /**
     * Find a user by email using the user table only (no customer join).
     * Use for OAuth / lookups where a customer row may not exist yet.
     */
    public function findByEmailSimple(string $email): ?User;

    /**
     * Find a specific user by ID with their roles
     *
     * @param int $id
     * @return array|null
     */
    public function findWithRoles(int $id): ?array;

     /**
     * Delete a user from the database by ID.
     *
     * @param int $id
     * @return array
     */
    public function getUserScopes(int $id): array;

    /**
     * Insert or update multiple users
     * 
     * @param array $data Array of user records to insert/update
     * @param array $uniqueKeys The unique keys to check for existing records
     * @return int|false The number of affected rows or false on failure
     */
    public function import(array $data, array $uniqueKeys): int|false;

    /**
     * Search for customers by name or email
     * 
     * @param string $search The search query
     * @return array The list of customers
     */
    public function customerSearch(string $search): array;

    /**
     * Find all users with their roles
     * 
     * @return array The list of users
     */
    public function findUsers(): array;

    /**
     * Get sales team data for sales team component
     * 
     * @param array $param Optional parameters for filtering and limiting
     * @return array The sales team data grouped by location
     */
    // public function getSalesTeamComponentData(array $param = []);
    public function existsByEmail(string $email): bool;
    public function importUsers(string $csv_file): array;
    public function updateUserImage(array $data, int $user_id): bool;
    public function deleteUserImage(int $user_id): bool;
    public function createRequest(string $name, string $description, string $attachments_path): bool;




    public function deleteUser(int $id): bool;

    public function loginUser(array $data): object|false;

    /**
     * Create a new user.
     *
     * @param array $data User data (email, password, name/first_name/last_name, etc.)
     * @return object|false The created user or false on failure
     */
    public function createUser(array $data): object|false;
     public function updateUserProfile(array $data);
}
