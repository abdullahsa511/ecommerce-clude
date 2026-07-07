<?php

declare(strict_types=1);

namespace App\Core\Models\Tax;

use App\Core\Models\Base\Model;

class TaxType extends Model
{
    public int $tax_type_id;
    public string $name;
    public string $content;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Check if the tax type is valid
     */
    public function isValid(): bool
    {
        return !empty($this->name) && !empty($this->content);
    }

    /**
     * Get the formatted name
     */
    public function getFormattedName(): string
    {
        return ucfirst(strtolower($this->name));
    }
} 