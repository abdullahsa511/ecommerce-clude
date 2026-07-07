<?php

declare(strict_types=1);

namespace App\Core\Models\Pinboard;

use App\Core\Models\Base\Model;
use App\Core\Models\Media\Media;
use App\Core\Models\Post\Comment;
use App\Core\Models\Post\Post;
use App\Core\Models\Product\Product;
use App\Core\Models\Project\Project;

class PinboardItem extends Model
{
    protected string $table = 'pinboard_item';
    // protected string $tableAlias = 'pi';

    protected int $pinboard_item_id;
    protected int $language_id;
    protected string $uuid;
    protected int $pinboard_id;

    protected ?int $model_id;
    protected ?string $model_type;
    
    protected ?int $product_id;
    protected ?int $project_id;
    protected ?int $media_id;
    protected ?int $comment_id;
    protected ?int $post_id;
    
    protected string $description;
    protected int $quantity;
    protected float $unit_price;
    protected float $total_price;
    
    protected ?string $photo;
    
    protected int $sort_order;

    protected ?string $created_at;
    protected ?string $updated_at;
    protected ?string $comments;

    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * Get the polymorphic related model
     * This method creates conditional joins for all possible model types
     * to support eager loading with with() function
     * 
     * @return array Relationship definition with conditional joins
     */
    public function model()
    {
        $tableAlias = $this->getTableAlias();
        $relatedTableAlias = $this->relatedTableAlias ?? 'model';
        
        // Map table names to model classes
        $modelMap = [
            'product' => ['class' => Product::class, 'columns' => ['product_id', 'image_thumb as product_image', 'product_code as product_title']],
            'project' => ['class' => Project::class, 'columns' => ['project_id', 'image_thumb as project_image', 'title as project_title']],
            'media' => ['class' => Media::class, 'columns' => ['media_id', 'file as media_image', 'name as media_title']],
            'post' => ['class' => Post::class, 'columns' => ['post_id', 'feature_image_thumb as post_image', 'title as post_title']],
            'comment' => ['class' => Comment::class, 'columns' => ['comment_id', 'url as comment_image', 'email as comment_title']],
        ];
        
        $joins = [];
        $allColumns = [];
        $modelInstances = [];
        
        // Create conditional LEFT JOINs for each possible model type
        foreach ($modelMap as $tableName => $modelConfig) {
            $modelClass = $modelConfig['class'] ?? null;
            $columnsToSelect = $modelConfig['columns'] ?? [];
            
            if (!$modelClass || !class_exists($modelClass)) {
                continue;
            }
            
            $modelInstance = new $modelClass();
            if ($this->db) {
                $modelInstance->setDb($this->db);
            }
            
            $relatedTable = str_replace('`', '', $modelInstance->getTable());
            $relatedTableAliasForType = "`{$relatedTableAlias}_{$tableName}`";
            $primaryKey = $modelInstance->getPrimaryKey();
            
            // Create conditional join: only join when model_type matches
            // The join builder creates: "ON first operator second"
            // We need: "ON model_type = 'product' AND model_id = alias.id"
            // So we'll split it: first = model_type check, operator = 'AND', second = model_id = alias.id
            // This creates: "ON model_type = 'product' AND model_id = alias.id" ✓
            $joins[] = [
                'table' => "`{$relatedTable}` AS {$relatedTableAliasForType}",
                'first' => "{$tableAlias}.model_type = '{$tableName}'",
                'operator' => 'AND',
                'second' => "{$tableAlias}.model_id = {$relatedTableAliasForType}.{$primaryKey}",
                'type' => 'LEFT',
                'aliased' => $relatedTableAliasForType
            ];
            
            // Build columns with table alias prefix as associative array (like prepareColumns)
            $columns = [];
            foreach ($columnsToSelect as $column) {
                // If column already has an alias (contains 'as'), use alias as key
                if (stripos($column, ' as ') !== false) {
                    [$columnName, $alias] = explode(' as ', $column, 2);
                    $columnName = trim($columnName);
                    $alias = trim($alias);
                    // Add table alias if column doesn't already have one
                    if (strpos($columnName, '.') === false) {
                        $columns[$alias] = "{$relatedTableAliasForType}.{$columnName} as {$alias}";
                    } else {
                        $columns[$alias] = "{$columnName} as {$alias}";
                    }
                } else {
                    // No alias, use column name as key
                    $columnName = trim($column);
                    if (strpos($columnName, '.') === false) {
                        $columns[$columnName] = "{$relatedTableAliasForType}.{$columnName}";
                    } else {
                        $parts = explode('.', $columnName);
                        $columns[$parts[1] ?? $columnName] = $columnName;
                    }
                }
            }
            $allColumns = array_merge($allColumns, $columns);
            
            $modelInstances[$tableName] = $modelInstance;
        }
        
        // Build the join SQL with conditions for the 'join' field
        $joinSql = [];
        foreach ($joins as $join) {
            // Build conditional join SQL with proper ON condition
            $joinSql[] = "{$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
        }
        
        return [
            'join' => implode(' ', $joinSql),
            'model' => $modelInstances['product'] ?? reset($modelInstances), // Default model for compatibility
            'tableAlias' => $relatedTableAlias,
            'class' => null, // Polymorphic, no single class
            'type' => 'one',
            'columns' => $allColumns,
            'joins' => $joins,
            'polymorphic' => true,
            'modelMap' => $modelMap
        ];
    }

    // public function product()
    // {
    //     return $this->belongsTo(Product::class, 'model_id', 'product_id');
    // }

    // public function project()
    // {
    //     return $this->belongsTo(Project::class, 'model_id', 'project_id');
    // }

    // public function media()
    // {
    //     return $this->belongsTo(Media::class, 'model_id', 'media_id');
    // }

    // public function comment()
    // {
    //     return $this->belongsTo(Comment::class, 'model_id', 'comment_id');
    // }

    // public function post()
    // {
    //     return $this->belongsTo(Post::class, 'model_id', 'post_id');
    // }

} 

class PinboardItemData
{
    public ?int $pinboard_item_id;
    public ?int $language_id;
    public ?string $uuid;
    public ?int $pinboard_id;
    public ?int $model_id;
    public ?string $model_type;
    public ?string $description;
    public ?string $options;
    public ?string $comments;
    public ?int $quantity;
    public ?float $unit_price;
    public ?float $total_price;
    public ?int $sort_order;
    public ?string $photo;
    public ?string $product_url;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(array $data = [])
    {
        $this->pinboard_item_id = self::toNullableInt($data['pinboard_item_id'] ?? null);
        $this->language_id = self::toNullableInt($data['language_id'] ?? 1) ?? 1;
        $this->uuid = self::toNullableString($data['uuid'] ?? null);
        $this->pinboard_id = self::toNullableInt($data['pinboard_id'] ?? null);
        $this->model_id = self::toNullableInt($data['model_id'] ?? null);
        $this->model_type = self::toNullableString($data['model_type'] ?? null);
        $this->description = self::toNullableString($data['description'] ?? null);
        $this->options = self::toNullableJson($data['options'] ?? null);
        $this->comments = self::toNullableJson($data['comments'] ?? null);
        $this->quantity = self::toNullableInt($data['quantity'] ?? 0) ?? 0;
        $this->unit_price = self::toNullableFloat($data['unit_price'] ?? 0) ?? 0.0;
        $this->total_price = self::toNullableFloat($data['total_price'] ?? 0) ?? 0.0;
        $this->photo = self::toNullableString($data['photo'] ?? null);
        $this->product_url = self::toNullableString($data['product_url'] ?? null);
        $this->sort_order = self::toNullableInt($data['sort_order'] ?? 0) ?? 0;
        $this->created_at = self::toNullableString($data['created_at'] ?? null);
        $this->updated_at = self::toNullableString($data['updated_at'] ?? null);
    }

    public function toArray(): array
    {
        $row = [
            'language_id' => $this->language_id,
            'uuid' => $this->uuid,
            'pinboard_id' => $this->pinboard_id,
            'model_id' => $this->model_id,
            'model_type' => $this->model_type,
            'description' => $this->description,
            'options' => $this->options,
            'comments' => $this->comments,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'photo' => $this->photo,
            'product_url' => $this->product_url,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Keep PK only when explicitly provided for update/upsert flows.
        if ($this->pinboard_item_id !== null) {
            $row['pinboard_item_id'] = $this->pinboard_item_id;
        }

        return $row;
    }

    private static function toNullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function toNullableFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private static function toNullableString(mixed $value): ?string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return null;
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    private static function toNullableJson(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            $json = json_encode($value);
            return $json === false ? null : $json;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed === '' ? null : $trimmed;
        }

        return null;
    }
}