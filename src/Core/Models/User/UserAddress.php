<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class UserAddress extends Model
{
    public int $user_address_id;
    public int $user_id;
    public string $first_name;
    public string $last_name;
    public string $company;
    public string $address_1;
    public string $address_2;
    public string $city;
    public string $post_code;
    public int $country_id;
    public int $region_id;
    public int $default_address;
    public int $is_billing;
    public int $is_shipping;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 