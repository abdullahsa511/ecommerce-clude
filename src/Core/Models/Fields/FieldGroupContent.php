<?php

declare(strict_types=1);

namespace App\Core\Models\Fields;

use App\Core\Models\Base\Model;

class FieldGroupContent extends Model
{
    protected string $table = 'field_group_content';
    protected string $tableAlias = 'field_group_content';

    public int $field_group_id;
    public int $language_id;
    public string $name;
        
    public function __construct() 
    {
        parent::__construct();
    }

} 