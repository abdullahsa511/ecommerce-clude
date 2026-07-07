<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;
use App\Core\Models\Option\OptionDescription;
use App\Core\Models\Option\OptionContent;
use App\Core\Models\Option\OptionValue;
use App\Core\Models\Product\ProductOption;

class Option extends Model
{
    protected string $table = 'option';
    protected string $primaryKey = 'option_id';
    protected array $fillable = [
        // 'option_code',
        'type',
        'sort_order',
        'type_id'
    ];

    public int|null $option_id;
    public string|null $option_code;
    public string|null $type;
    public int|null $sort_order;
    public int|null $type_id;


    public function content()
    {
        return $this->hasMany(OptionContent::class, 'option_id');
    }

   
    /**
     * Define relationship with OptionValue model
     */
    public function optionValue()
    {
        return $this->hasMany(OptionValue::class, 'option_id');
    }

    /**
     * Define relationship with ProductOption model
     */
    public function productOption()
    {
        return $this->hasMany(ProductOption::class, 'option_id');
    }

    /**
     * Get option descriptions relationship
     */
    public function optionDescription()
    {
        return $this->hasOne(OptionDescription::class, 'option_id', 'option_id');
    }


    public function __construct() 
    {
        parent::__construct();
    }
} 