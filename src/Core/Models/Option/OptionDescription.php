<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;

class OptionDescription extends Model
{
    protected string $table = 'option_description';
    protected string $tableAlias = 'od';

    /**
     * Option ID
     */
    protected int $option_id;

    /**
     * Language ID
     */
    protected int $language_id;

    /**
     * Name
     */
    protected string $name;

    /**
     * Value
     */
    protected string $value;

    /**
     * Get parent option relationship
     */
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id', 'option_id');
    }

    public function getOptionId(): int
    {
        return $this->option_id;
    }

    public function setOptionId(int $option_id): void
    {
        $this->option_id = $option_id;
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

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 