<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class LengthTypeContent extends Model
{
    public int $length_type_id;
    public int $language_id;
    public string $name;
    public string $unit;


    public function getPrimaryKey(): string
    {
        return 'length_type_id';
    }

    public function lengthType(): array
    {
        return $this->belongsTo(LengthType::class, 'length_type_id', 'length_type_id');
    }
} 