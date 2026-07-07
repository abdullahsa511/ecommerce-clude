<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

interface AdminLoginCodeRepositoryInterface
{
    public function issueCode(int $userId, string $email, string $source, ?string $ip = null, ?string $userAgent = null): string;

    /**
     * @return array<string, mixed>|null
     */
    public function consumeCode(string $plainCode): ?array;
}

