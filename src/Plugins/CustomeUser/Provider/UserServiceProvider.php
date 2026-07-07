<?php


namespace App\Plugins\CustomeUser\Provider;

use App\Core\Repositories\UserRepositoryInterface;
use App\Plugins\CustomeUser\Repository\UserRepository;
use function App\Core\System\utils\app;

class UserServiceProvider {
    public function register() {
        app()->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
