<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;

class OptionValue extends Model
{
    protected string $table = 'option_value';
    protected string $tableAlias = 'ov';

    /**
     * Option Value ID
     */
    protected int $option_value_id;

    /**
     * Option ID
     */
    protected int $option_id;

    /**
     * Sort order
     */
    protected int $sort_order;

    /**
     * Option value content data
     */
    protected array $option_value_content_data;

    /**
     * Get parent option relationship
     */
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id', 'option_id');
    }

    /**
     * Get option value content relationship
     */
    public function optionValueContent()
    {
        return $this->hasOne(OptionValueContent::class, 'option_value_id', 'option_value_id');
    }

    public function getOptionValueId(): int
    {
        return $this->option_value_id;
    }

    public function setOptionValueId(int $option_value_id): void
    {
        $this->option_value_id = $option_value_id;
    }

    public function getOptionId(): int
    {
        return $this->option_id;
    }

    public function setOptionId(int $option_id): void
    {
        $this->option_id = $option_id;
    }

    public function getSortOrder(): int
    {
        return $this->sort_order;
    }

    public function setSortOrder(int $sort_order): void
    {
        $this->sort_order = $sort_order;
    }

    public function getOptionValueContent(): ?OptionValueContent
    {
        return $this->option_value_content_data ?? null;
    }

    public function setOptionValueContent(array $data): void
    {
        $this->option_value_content_data = $data;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 