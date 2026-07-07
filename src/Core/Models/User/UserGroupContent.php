<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class UserGroupContent extends Model
{
    public int $user_group_id;
    public int $language_id;
    public string $name;
    public string $content;

    public function __construct() 
    {
        parent::__construct();
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id', 'user_group_id');
    }

    public function getPrimaryKey(): string
    {
        return 'user_group_id';
    }
} 