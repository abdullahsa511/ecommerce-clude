<?php

namespace App\Plugins\CustomeUser\Controller;


use App\Core\Repositories\Auth\ScopeRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;

class AuthController extends \App\Core\Controllers\AuthController
{
    public function __construct(AuthService $authService, UserRepositoryInterface $userRepository, ScopeRepositoryInterface $scopeRepository)
    {
        parent::__construct($authService, $userRepository, $scopeRepository);
    }
}
