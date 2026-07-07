<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class WeightTypeContent extends Model
{
    public int $weight_type_id;
    public int $language_id;
    public string $name;
    public string $unit;

    public function __construct() 
    {
        parent::__construct();
        // $this->table = 'weight_type_content';
        // $this->tableAlias = 'weight_type_content';
    }

    public function getPrimaryKey(): string
    {
        return 'weight_type_id';
    }

    public function weightType(): array
    {
        return $this->belongsTo(WeightType::class, 'weight_type_id', 'weight_type_id');
    }
} 