<?php

declare(strict_types=1);

namespace App\Core\Models\Tax;

use App\Core\Models\Base\Model;

class TaxRate extends Model
{
    public int $tax_rate_id;
    public int $region_group_id;
    public string $name;
    public float $rate;
    public string $type;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * Check if the tax rate is valid
     */
    public function isValid(): bool
    {
        return !empty($this->name) && $this->rate >= 0 && in_array($this->type, ['P', 'F']);
    }

    /**
     * Get the formatted name
     */
    public function getFormattedName(): string
    {
        return ucfirst(strtolower($this->name));
    }

    /**
     * Get the formatted rate
     */
    public function getFormattedRate(): string
    {
        return number_format($this->rate, 4);
    }

    /**
     * Get the type description
     */
    public function getTypeDescription(): string
    {
        return match($this->type) {
            'P' => 'Percentage',
            'F' => 'Fixed Amount',
            default => 'Unknown'
        };
    }

    /**
     * Calculate tax amount for a given base amount
     */
    public function calculateTax(float $baseAmount): float
    {
        return match($this->type) {
            'P' => $baseAmount * ($this->rate / 100),
            'F' => $this->rate,
            default => 0.0
        };
    }
} 