<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;

class OptionValueContent extends Model
{
    protected string $table = 'option_value_content';
    protected string $tableAlias = 'ovc';

    /**
     * Option Value ID
     */
    protected int $option_value_id;

    /**
     * Language ID
     */
    protected int $language_id;

    /**
     * Name
     */
    protected string $name;

    /**
     * Get parent option value relationship
     */
    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id', 'option_value_id');
    }

    public function getOptionValueId(): int
    {
        return $this->option_value_id;
    }

    public function setOptionValueId(int $option_value_id): void
    {
        $this->option_value_id = $option_value_id;
    }

    public function getLanguageId(): int
    {
        return $this->language_id;
    }

    public function setLanguageId(int $language_id): void
    {
        $this->language_id = $language_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 