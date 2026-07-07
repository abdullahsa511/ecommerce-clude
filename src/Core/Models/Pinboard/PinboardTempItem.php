<?php

declare(strict_types=1);

namespace App\Core\Models\Pinboard;

use App\Core\Models\Base\Model;
use App\Core\Models\Media\Media;
use App\Core\Models\Post\Comment;
use App\Core\Models\Post\Post;
use App\Core\Models\Product\Product;
use App\Core\Models\Project\Project;

class PinboardTempItem extends Model
{
    protected string $table = 'pinboard_temp_item';
    // protected string $tableAlias = 'pi';

    protected int $pinboard_temp_item_id;
    protected int $language_id;
    protected string $uuid;
    protected int $pinboard_temp_id;

    protected ?int $model_id;
    protected ?string $model_type;
    
    protected ?int $product_id;
    protected ?int $project_id;
    protected ?int $media_id;
    protected ?int $comment_id;
    protected ?int $post_id;
    
    protected ?string $title;
    protected string $description;
    protected int $quantity;
    protected float $unit_price;
    protected float $total_price;
    
    protected ?string $photo;
    
    protected int $sort_order;

    protected string $created_at;
    protected string $updated_at;
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

    

} 
