<?php

declare(strict_types=1);

namespace App\Core\Models;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class User extends Model
{
    public int $user_id;
    public int $user_group_id;
    public int $site_id;
    public string $username;
    public string|null $first_name;
    public string|null $last_name;
    public string $password;
    public string $email;
    public string $phone_number;
    public string|null $url;
    public int $status;
    public string|null $display_name;
    public string|null $avatar;
    public ?string $bio;
    public ?string $designation;
    public string|null $token;
    public int $subscribe;
    public int $notify_orders;
    public int $notify_quotes;
    public string $created_at;
    public string $updated_at;
    public string|null $otp_code;
    public string|null $otp_created_at;
    public string|null $otp_expiry_time;
    public bool|null $is_verified = false;
    public bool|null $is_admin = false;

    // abdullah add from here
    public int|null $customer_id;

    private static $namespace = 'user';
    
    
    public function __construct() 
    {
        parent::__construct();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @ Currently logged in user data or empty array if guest
     * @return mixed
     */
    public static function current() {
        return session(self::$namespace, []);
    }
}
