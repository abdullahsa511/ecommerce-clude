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

     /**
     * Delete a user from the database by ID.
     *
     * @param int $id
     * @return array
     */
    public function getUserScopes(int $id): array;

    public function findUsers(): array;
    public function importUsers(string $csv_file): array;
    // public function userAuth(): array;
    // public function updateUserImage(array $data, int $user_id): bool;
    // public function deleteUserImage(int $user_id): bool;
    // public function createRequest(string $name, string $description, string $attachments_path): bool;

    public function deleteUser(int $id): bool;


    // 'App\Core\Repositories\UserRepository' does not implement methods 'userAuth', 'updateUserImage', 'deleteUserImage', 'createRequest'intelephense(P1037)
    public function createUser(array $data);



}
