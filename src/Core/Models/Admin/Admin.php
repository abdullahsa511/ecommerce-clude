<?php

declare(strict_types=1);

namespace App\Core\Models\Admin;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class Admin extends Model
{
    public int $admin_id;
    public string $username;
    public string $first_name;
    public string $last_name;
    public string $password;
    public string $email;
    public string $phone_number;
    public string $url;
    public string $display_name;
    public string $avatar;
    public ?string $bio;
    public ?int $role_id;
    public string $site_access;
    public int $status;
    public string $token;
    public string $created_at;
    public string $updated_at;
    private static string $namespace = 'admin';

    
    public function __construct() 
    {
        parent::__construct();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get currently logged in admin data or empty array if not logged in
     * @return mixed
     */
    public static function current()
    {
        return session(self::$namespace, []);
    }

    /**
     * Define relationship with AdminFailedLogin model
     */
    public function adminFailedLogin()
    {
        return $this->hasOne(AdminFailedLogin::class, 'admin_id');
    }

    /**
     * Define relationship with AdminPasswordReset model
     */
    public function adminPasswordReset()
    {
        return $this->hasOne(AdminPasswordReset::class, 'email');
    }

    /**
     * Define relationship with AdminRole model
     */
    public function adminRole()
    {
        return $this->hasOne(AdminRole::class, 'role_id');
    }

    /**
     * Define relationship with Role model
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'role_id');
    }
} 