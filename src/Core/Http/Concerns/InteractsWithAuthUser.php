<?php

declare(strict_types=1);

namespace App\Core\Http\Concerns;

use App\Core\Models\User;
use App\Core\Repositories\Admin\AdminRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;
use Throwable;
use function App\Core\System\utils\app;

trait InteractsWithAuthUser
{
    /**
     * Get authenticated user set by auth middleware.
     */
    protected function authUser(): ?User
    {
        $user = is_array($this->auth) ? ($this->auth['entity'] ?? null) : $this->request->getAttribute('user');

        return $user instanceof User ? $user : null;
    }

    /**
     * Get normalized auth scopes from request auth context.
     *
     * @return array<int, string>
     */
    protected function authScopes(): array
    {
        $rawScopes = is_array($this->auth) ? ($this->auth['scopes'] ?? []) : [];

        if (is_string($rawScopes)) {
            $decoded = json_decode($rawScopes, true);
            if (is_array($decoded)) {
                $rawScopes = $decoded;
            } else {
                $rawScopes = explode(',', $rawScopes);
            }
        }

        if (!is_array($rawScopes)) {
            return [];
        }

        $scopes = [];
        foreach ($rawScopes as $scope) {
            if (!is_string($scope)) {
                continue;
            }

            $chunks = preg_split('/[\s,]+/', $scope, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            foreach ($chunks as $chunk) {
                $scopes[] = trim($chunk);
            }
        }

        return array_values(array_unique(array_filter($scopes)));
    }

    /**
     * Check if the authenticated user has admin access.
     */
    protected function isAdmin(): bool
    {
        $auth = $this->resolveAuthContext();
        $scopes = $this->extractScopes($auth);
        if (in_array('admin', $scopes, true)) {
            return true;
        }

        $user = $this->extractAuthUser($auth);
        if (!$user instanceof User) {
            $user = $this->resolveSessionUser();
            if (!$user instanceof User && $this->isSessionAdminByScope()) {
                return true;
            }
            if (!$user instanceof User) {
                return false;
            }
        }

        if (isset($user->is_admin) && (bool) $user->is_admin) {
            return true;
        }

        if (isset($user->data->is_admin) && (bool) $user->data->is_admin) {
            return true;
        }

        if ($this->hasActiveAdminAccount($user)) {
            return true;
        }

        return false;
    }

    /**
     * Backward-compatible alias used by existing controllers/middleware.
     */
    protected function isAdminUser(): bool
    {
        return $this->isAdmin();
    }

    /**
     * @param mixed $auth
     */
    private function extractAuthUser(mixed $auth): ?User
    {
        $user = is_array($auth) ? ($auth['entity'] ?? null) : null;
        if (!$user instanceof User) {
            $user = $this->request->getAttribute('user');
        }

        return $user instanceof User ? $user : null;
    }

    /**
     * @param mixed $auth
     * @return array<int, string>
     */
    private function extractScopes(mixed $auth): array
    {
        $rawScopes = is_array($auth) ? ($auth['scopes'] ?? []) : [];
        if ($rawScopes === [] && is_array($this->auth) && isset($this->auth['scopes'])) {
            $rawScopes = $this->auth['scopes'];
        }

        if (is_string($rawScopes)) {
            $decoded = json_decode($rawScopes, true);
            if (is_array($decoded)) {
                $rawScopes = $decoded;
            } else {
                $rawScopes = explode(',', $rawScopes);
            }
        }

        if (!is_array($rawScopes)) {
            return [];
        }

        $scopes = [];
        foreach ($rawScopes as $scope) {
            if (!is_string($scope)) {
                continue;
            }

            $chunks = preg_split('/[\s,]+/', $scope, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            foreach ($chunks as $chunk) {
                $scopes[] = trim($chunk);
            }
        }

        return array_values(array_unique(array_filter($scopes)));
    }

    /**
     * Resolve auth from middleware attributes first, then token from request.
     *
     * @return array<string, mixed>|null
     */
    private function resolveAuthContext(): ?array
    {
        $auth = $this->auth;
        if (!is_array($auth)) {
            $requestAuth = $this->request->getAttribute('auth');
            $auth = is_array($requestAuth) ? $requestAuth : null;
        }

        if (is_array($auth)) {
            return $auth;
        }

        $token = $this->extractAccessToken();
        if ($token === '') {
            return null;
        }

        try {
            /** @var AuthService $authService */
            $authService = app(AuthService::class);
            $resolved = $authService->validateToken($token);
        } catch (Throwable $e) {
            return null;
        }

        return is_array($resolved) ? $resolved : null;
    }

    private function extractAccessToken(): string
    {
        $authHeader = (string) ($this->request->header('Authorization') ?? '');
        if ($authHeader !== '' && str_starts_with($authHeader, 'Bearer ')) {
            return trim(substr($authHeader, 7));
        }

        return trim((string) ($this->request->cookie('admin_access_token') ?? ''));
    }

    private function isSessionAdminByScope(): bool
    {
        try {
            /** @var AuthService $authService */
            $authService = app(AuthService::class);
        } catch (Throwable $e) {
            return false;
        }

        $csv = $authService->getSessionScopeString();
        if ($csv === '') {
            return false;
        }

        $scopes = array_map('trim', explode(',', $csv));

        return in_array('admin', $scopes, true);
    }

    private function resolveSessionUser(): ?User
    {
        try {
            /** @var AuthService $authService */
            $authService = app(AuthService::class);
        } catch (Throwable $e) {
            return null;
        }

        $userId = $authService->getAuthenticatedSessionUserId();
        if ($userId === null || $userId <= 0) {
            return null;
        }

        try {
            /** @var UserRepositoryInterface $userRepository */
            $userRepository = app(UserRepositoryInterface::class);
            $user = $userRepository->find($userId);
        } catch (Throwable $e) {
            return null;
        }

        return $user instanceof User ? $user : null;
    }

    private function hasActiveAdminAccount(User $user): bool
    {
        $email = '';
        if (isset($user->email) && is_string($user->email)) {
            $email = trim($user->email);
        } elseif (isset($user->data->email) && is_string($user->data->email)) {
            $email = trim($user->data->email);
        }

        if ($email === '') {
            return false;
        }

        try {
            /** @var AdminRepositoryInterface $adminRepository */
            $adminRepository = app(AdminRepositoryInterface::class);
            $admin = $adminRepository->findByEmail($email);
        } catch (Throwable $e) {
            return false;
        }

        if ($admin === null) {
            return false;
        }

        if (isset($admin->status)) {
            return (int) $admin->status === 1;
        }

        return isset($admin->data->status) && (int) $admin->data->status === 1;
    }
}
