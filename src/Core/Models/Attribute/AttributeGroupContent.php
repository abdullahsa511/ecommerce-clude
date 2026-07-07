<?php

declare(strict_types=1);

namespace App\Core\Models\Attribute;

use App\Core\Models\Base\Model;

class AttributeGroupContent extends Model
{
    protected string $table = 'attribute_group_content'; 
    // protected string $primaryKey = 'attribute_group_id';

    public int $attribute_group_id;
    public int $language_id;
    public string $name;

    public function __construct()
    {
        parent::__construct();
    }
}