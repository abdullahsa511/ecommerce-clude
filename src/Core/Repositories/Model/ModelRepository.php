<?php

declare(strict_types=1);

namespace App\Core\Repositories\Model;

use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use ReflectionClass;

class ModelRepository extends BaseRepository implements ModelRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'models', \App\Core\Models\Post\Post::class);
    }

    public static function getModels(): array
    {
        return [
            [
                'model_id' => 1,
                'name' => 'Product',
                'type' => 'product',
                'class' => \App\Core\Models\Product\Product::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 2,
                'name' => 'Order',
                'type' => 'order',
                'class' => \App\Core\Models\Order\Order::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 3,
                'name' => 'User',
                'type' => 'user',
                'class' => \App\Core\Models\User::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 4,
                'name' => 'Taxonomy Item',
                'type' => 'taxonomy_item',
                'class' => \App\Core\Models\PostCategory\TaxonomyItem::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 5,
                'name' => 'Post',
                'type' => 'post',
                'class' => \App\Core\Models\Post\Post::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 6,
                'name' => 'Comment',
                'type' => 'comment',
                'class' => \App\Core\Models\Post\Comment::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 7,
                'name' => 'Media',
                'type' => 'media',
                'class' => \App\Core\Models\Media\Media::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 8,
                'name' => 'Site',
                'type' => 'site',
                'class' => \App\Core\Models\Site\Site::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 9,
                'name' => 'Admin',
                'type' => 'admin',
                'class' => \App\Core\Models\Admin\Admin::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 10,
                'name' => 'Country',
                'type' => 'country',
                'class' => \App\Core\Models\Geoip\Country::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 11,
                'name' => 'Project',
                'type' => 'project',
                'class' => \App\Core\Models\Project\Project::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 12,
                'name' => 'Pinboard',
                'type' => 'pinboard',
                'class' => \App\Core\Models\Pinboard\Pinboard::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 13,
                'name' => 'Menu',
                'type' => 'menu',
                'class' => \App\Core\Models\Menu\Menu::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 14,
                'name' => 'Quote',
                'type' => 'quote',
                'class' => \App\Core\Models\Quote\Quote::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 15,
                'name' => 'Design Resource',
                'type' => 'design_resource',
                'class' => \App\Core\Models\Design\DesignResource::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
             [
                'model_id' => 16,
                'name' => 'Showroom',
                'type' => 'showrooms',
                'class' => \App\Core\Models\Showroom\Showroom::class,
                'created_at' => date('Y-m-d H:i:s')
             ],
            [
                'model_id' => 17,
                'name' => 'Project Image',
                'type' => 'project_image',
                'class' => \App\Core\Models\Project\ProjectImage::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 18,
                'name' => 'Product Resource',
                'type' => 'product_resource',
                'class' => \App\Core\Models\Product\ProductResource::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 19,
                'name' => 'Design Resource Document',
                'type' => 'design_resource_document',
                'class' => \App\Core\Models\Design\DesignResourceDocument::class,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public static function getRelatedModels(string $modelName): ?array
    {
        $map = [
            'product' => [
                [
                    'model_id' => 1,
                    'name' => 'ProductContent',
                    'type' => 'product_content',
                    'source' => 'product.product_id',
                    'model' => 'product_content.product_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Product\ProductContent::class
                ],
                [
                    'model_id' => 18,
                    'name' => 'ProductResource',
                    'type' => 'product_resource',
                    'source' => 'product.product_id',
                    'model' => 'product_resource.product_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Product\ProductResource::class
                ],
                [
                    'model_id' => 19,
                    'name' => 'DesignResource',
                    'type' => 'design_resource',
                    'source' => 'product_resource.design_resource_id',
                    'model' => 'design_resource.design_resource_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Design\DesignResource::class
                ],
                [
                    'model_id' => 21,
                    'name' => 'DesignResourceDocument',
                    'type' => 'design_resource_document',
                    'source' => 'design_resource.design_resource_id',
                    'model' => 'design_resource_document.design_resource_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Design\DesignResourceDocument::class
                ]
            ],
            'order' => [
                [
                    'model_id' => 2,
                    'name' => 'OrderItem',
                    'type' => 'order_item',
                    'source' => 'order.order_id',
                    'model' => 'order_items.order_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Order\OrderItem::class
                ]
            ],
            'user' => [
                [
                    'model_id' => 3,
                    'name' => 'UserAddress',
                    'type' => 'user_address',
                    'source' => 'user.user_id',
                    'model' => 'user_addresses.user_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\User\UserAddress::class
                ]
            ],
            'taxonomy_item' => [
                [
                    'model_id' => 4,
                    'name' => 'TaxonomyItemContent',
                    'type' => 'taxonomy_item_content',
                    'source' => 'taxonomy_item.taxonomy_item_id',
                    'model' => 'taxonomy_item_content.taxonomy_item_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\PostCategory\TaxonomyItemContent::class
                ]
            ],
            'post' => [
                [
                    'model_id' => 5,
                    'name' => 'PostContent',
                    'type' => 'post_content',
                    'source' => 'post.post_id',
                    'model' => 'post_content.post_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Post\PostContent::class
                ],
                [
                    'model_id' => 5,
                    'name' => 'PostImage',
                    'type' => 'post_image',
                    'source' => 'post.post_id',
                    'model' => 'post_image.post_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Post\PostImage::class
                ]
            ],
            'media' => [
                [
                    'model_id' => 6,
                    'name' => 'MediaContent',
                    'type' => 'media_content',
                    'source' => 'media.media_id',
                    'model' => 'media_content.media_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Media\MediaContent::class
                ]
            ],
            'admin' => [
                [
                    'model_id' => 7,
                    'name' => 'AdminRole',
                    'type' => 'admin_role',
                    'source' => 'admin.admin_id',
                    'model' => 'admin_roles.admin_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Admin\AdminRole::class
                ]
            ],
            'project' => [
                [
                    'model_id' => 8,
                    'name' => 'ProjectImage',
                    'type' => 'project_image',
                    'source' => 'project.project_id',
                    'model' => 'project_image.project_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Project\ProjectImage::class
                ]
            ],
            'showroom' => [
                [
                    
                ]
            ],
            'pinboard' => [
                [
                    'model_id' => 9,
                    'name' => 'PinboardItem',
                    'type' => 'pinboard_item',
                    'source' => 'pinboard.pinboard_id',
                    'model' => 'pinboard_items.pinboard_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Pinboard\PinboardItem::class
                ]
            ],
            'menu' => [
                [
                    'model_id' => 10,
                    'name' => 'MenuItem',
                    'type' => 'menu_item',
                    'source' => 'menu.menu_id',
                    'model' => 'menu_items.menu_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Menu\MenuItem::class
                ]
            ],
            'quote' => [
                [
                    'model_id' => 11,
                    'name' => 'QuoteItem',
                    'type' => 'quote_item',
                    'source' => 'quote.quote_id',
                    'model' => 'quote_items.quote_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Quote\QuoteItem::class
                ]
            ],
            'design_resource' => [
                [
                   'model_id' => 20,
                    'name' => 'DesignResource',
                    'type' => 'design_resource',
                    'source' => 'design_resource.design_resource_id',
                    'model' => 'design_resource.design_resource_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Design\DesignResource::class
                ]
            ],
            'project_image' => [
                [
                    'model_id' => 17,
                    'name' => 'Project',
                    'type' => 'project',
                    'source' => 'project_image.project_id',
                    'model' => 'project.project_id',
                    'joinType' => 'LEFT',
                    'class' => \App\Core\Models\Project\Project::class
                ]
            ]
            // Add more mappings as needed
        ];
        $key = strtolower($modelName);
        return $map[$key] ?? null;
    }


    /**
     * Get table columns using database query
     * 
     * @param string $className Fully qualified class name or model type
     * @return array Columns with their data types and properties
     * @throws \Exception If class doesn't exist or query fails
     */
    public function getTableColumns(string $className): array
    {
        try {
            // First, try to resolve model type to class name
            $className = $this->resolveModelTypeToClassName($className);
            
            // If the class name doesn't start with a namespace, try to find it in common namespaces
            if (strpos($className, '\\') === false) {
                $commonNamespaces = [
                    'App\\Core\\Models\\',
                    'App\\Core\\Models\\Product\\',
                    'App\\Core\\Models\\Order\\',
                    'App\\Core\\Models\\Post\\',
                    'App\\Core\\Models\\User\\',
                    'App\\Core\\Models\\PostCategory\\',
                    'App\\Core\\Models\\Media\\',
                    'App\\Core\\Models\\Site\\',
                    'App\\Core\\Models\\Admin\\',
                    'App\\Core\\Models\\Geoip\\',
                    'App\\Core\\Models\\Project\\',
                    'App\\Core\\Models\\Pinboard\\',
                    'App\\Core\\Models\\Menu\\',
                    'App\\Core\\Models\\Quote\\',
                    'App\\Core\\Models\\Design\\'
                ];

                foreach ($commonNamespaces as $namespace) {
                    $fullClassName = $namespace . $className;
                    if (class_exists($fullClassName)) {
                        $className = $fullClassName;
                        break;
                    }
                }
            }

            // Check if class exists
            if (!class_exists($className)) {
                throw new \Exception("Class {$className} does not exist");
            }

            // Create an instance of the model to get table information
            $model = new $className();
            $model->setDb($this->db);
            $tableName = $model->getTable();
            
            // Directly execute SQL to get column information
            $sql = "SHOW FULL COLUMNS FROM {$tableName}";
            $results = $model->executeQuery($sql);
            
            $columns = [];
            foreach ($results as $column) {
                $field = str_contains('`', $column['Field'])?$column['Field']:'`'.$column['Field'].'`';
                $columns[] = $tableName.'.'.$field;
            }
            
            return $columns;
        } catch (\Exception $e) {
            throw new \Exception("Error getting table columns for class {$className}: " . $e->getMessage());
        }
    }
    
    /**
     * Get joined table columns for a main model and its related model
     * 
     * @param string $mainModelClass Main model class name
     * @param string $relatedModelClass Related model class name
     * @param string $joinType Type of join (left, right, inner)
     * @return array Combined columns from both tables with join information
     * @throws \Exception If models don't exist or query fails
     */
    public function getJoinedTableColumns(string $mainModelClass, string $relatedModelClass, string $joinType = 'left'): array
    {
        try {
            // Resolve main model class name if not fully qualified
            $mainModelClass = $this->resolveModelTypeToClassName($mainModelClass);
            $mainModelClass = $this->resolveClassName($mainModelClass);
            
            // Resolve related model class name if not fully qualified
            $relatedModelClass = $this->resolveModelTypeToClassName($relatedModelClass);
            $relatedModelClass = $this->resolveClassName($relatedModelClass);
            
            // Check if main model exists
            if (!class_exists($mainModelClass)) {
                throw new \Exception("Main model class {$mainModelClass} does not exist");
            }
            
            // Check if related model exists
            if (!class_exists($relatedModelClass)) {
                throw new \Exception("Related model class {$relatedModelClass} does not exist");
            }

            // Get main model table columns
            $mainModelColumns = $this->getTableColumns($mainModelClass);
            
            // Get related model table columns
            $relatedModelColumns = $this->getTableColumns($relatedModelClass);
            
            // Get main model table name
            $mainModelInstance = new $mainModelClass();
            $mainModelInstance->setDb($this->db);
            $mainTableName = $mainModelInstance->getTable();
            
            // Get related model table name
            $relatedModelInstance = new $relatedModelClass();
            $relatedModelInstance->setDb($this->db);
            $relatedTableName = $relatedModelInstance->getTable();
            
            // Combine columns with table prefixes and handle duplicates
            $joinedColumns = $this->mergeColumnsWithDuplicates($mainModelColumns, $relatedModelColumns, $mainTableName, $relatedTableName);
            
            return [
                'main_table' => $mainTableName,
                'related_table' => $relatedTableName,
                'main_model_class' => $mainModelClass,
                'related_model_class' => $relatedModelClass,
                'join_type' => $joinType,
                'main_columns' => $mainModelColumns,
                'related_columns' => $relatedModelColumns,
                'joined_columns' => $joinedColumns,
                'join_condition' => $this->generateJoinCondition($mainTableName, $relatedTableName, $mainModelClass, $relatedModelClass)
            ];
            
        } catch (\Exception $e) {
            throw new \Exception("Error getting joined table columns: " . $e->getMessage());
        }
    }
    
    /**
     * Merge columns from main and related tables, handling duplicates
     */
    private function mergeColumnsWithDuplicates(array $mainColumns, array $relatedColumns, string $mainTable, string $relatedTable): array
    {
        $mergedColumns = [];
        $usedColumnNames = [];
        
        // First, add main table columns
        foreach ($mainColumns as $column) {
            $columnName = $column['name'];
            $prefixedColumn = [
                'name' => $mainTable . '.' . $columnName,
                'display_name' => $mainTable . '.' . $columnName,
                'type' => $column['type'],
                'length' => $column['length'],
                'nullable' => $column['nullable'],
                'primary' => $column['primary'],
                'default' => $column['default'],
                'comment' => $column['comment'],
                'is_auto_increment' => $column['is_auto_increment'],
                'table' => $mainTable,
                'original_name' => $columnName,
                'source' => 'main'
            ];
            
            $mergedColumns[] = $prefixedColumn;
            $usedColumnNames[] = $columnName;
        }
        
        // Then add related table columns, skipping duplicates
        foreach ($relatedColumns as $column) {
            $columnName = $column['name'];
            
            // Skip if this column name already exists
            if (in_array($columnName, $usedColumnNames)) {
                continue;
            }
            
            $prefixedColumn = [
                'name' => $relatedTable . '.' . $columnName,
                'display_name' => $relatedTable . '.' . $columnName,
                'type' => $column['type'],
                'length' => $column['length'],
                'nullable' => $column['nullable'],
                'primary' => $column['primary'],
                'default' => $column['default'],
                'comment' => $column['comment'],
                'is_auto_increment' => $column['is_auto_increment'],
                'table' => $relatedTable,
                'original_name' => $columnName,
                'source' => 'related'
            ];
            
            $mergedColumns[] = $prefixedColumn;
            $usedColumnNames[] = $columnName;
        }
        
        return $mergedColumns;
    }
    
    
    /**
     * Generate join condition between main and related tables
     */
    private function generateJoinCondition(string $mainTable, string $relatedTable, string $mainClass, string $relatedClass): string
    {
        // Common join patterns based on table relationships
        $joinPatterns = [
            'product' => [
                'related_table' => 'product_content',
                'condition' => 'product.product_id = product_content.product_id'
            ],
            'order' => [
                'related_table' => 'order_items',
                'condition' => 'order.order_id = order_items.order_id'
            ],
            'user' => [
                'related_table' => 'user_addresses',
                'condition' => 'user.user_id = user_addresses.user_id'
            ],
            'post' => [
                'related_table' => 'post_content',
                'condition' => 'post.post_id = post_content.post_id'
            ],
            'media' => [
                'related_table' => 'media_content',
                'condition' => 'media.media_id = media_content.media_id'
            ],
            'project' => [
                'related_table' => 'project_image',
                'condition' => 'project.project_id = project_image.project_id'
            ],
            'pinboard' => [
                'related_table' => 'pinboard_items',
                'condition' => 'pinboard.pinboard_id = pinboard_items.pinboard_id'
            ],
            'menu' => [
                'related_table' => 'menu_items',
                'condition' => 'menu.menu_id = menu_items.menu_id'
            ],
            'quote' => [
                'related_table' => 'quote_items',
                'condition' => 'quote.quote_id = quote_items.quote_id'
            ]
        ];
        
        $modelType = $this->extractModelType($mainClass);
        
        if (isset($joinPatterns[$modelType])) {
            $pattern = $joinPatterns[$modelType];
            return str_replace(
                [$pattern['related_table'], $pattern['related_table'] . 's'],
                [$relatedTable, $relatedTable],
                $pattern['condition']
            );
        }
        
        // Default join condition (assumes foreign key relationship)
        $mainTableSingular = rtrim($mainTable, 's');
        return "{$mainTable}.id = {$relatedTable}.{$mainTableSingular}_id";
    }
    
    /**
     * Extract model type from class name
     */
    private function extractModelType(string $className): string
    {
        $parts = explode('\\', $className);
        $modelName = end($parts);
        
        // Convert CamelCase to snake_case
        $modelType = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
        
        return $modelType;
    }
    
    /**
     * Resolve class name by trying common namespaces
     */
    private function resolveClassName(string $className): string
    {
        if (strpos($className, '\\') === false) {
            $commonNamespaces = [
                'App\\Core\\Models\\',
                'App\\Core\\Models\\Product\\',
                'App\\Core\\Models\\Order\\',
                'App\\Core\\Models\\Post\\',
                'App\\Core\\Models\\User\\',
                'App\\Core\\Models\\PostCategory\\',
                'App\\Core\\Models\\Media\\',
                'App\\Core\\Models\\Site\\',
                'App\\Core\\Models\\Admin\\',
                'App\\Core\\Models\\Geoip\\',
                'App\\Core\\Models\\Project\\',
                'App\\Core\\Models\\Pinboard\\',
                'App\\Core\\Models\\Menu\\',
                'App\\Core\\Models\\Quote\\'
            ];

            foreach ($commonNamespaces as $namespace) {
                $fullClassName = $namespace . $className;
                if (class_exists($fullClassName)) {
                    return $fullClassName;
                }
            }
        }
        
        return $className;
    }

    /**
     * Resolve model type to class name
     */
    private function resolveModelTypeToClassName(string $modelType): string
    {
        // Get all models and find the one with matching type
        $models = self::getModels();
        
        foreach ($models as $model) {
            if ($model['type'] === $modelType) {
                return $model['class'];
            }
        }
        
        // If not found, return the original string (might be a class name)
        return $modelType;
    }


} 