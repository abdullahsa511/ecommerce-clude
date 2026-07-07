<?php

namespace App\Core\ModelsFilters;

use stdClass;

class RequestUri {
    public stdClass $filters;
    public int $page;
    public int $first; 
    public int $rows;
    public int $pageCount;
    public int $totalRecords;

    /**
     * Supported match modes and their SQL equivalents
     */
    private const MATCH_MODES = [
        'contains' => 'LIKE',
        'equals' => '=',
        'startsWith' => 'LIKE',
        'endsWith' => 'LIKE',
        'notEquals' => '!=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'between' => 'BETWEEN',
        'in' => 'IN'
    ];

    public function __construct(array $data = []) {
        $this->filters = new stdClass();
        
        // Initialize pagination properties
        $this->page = isset($data['page']) ? (int)$data['page'] : 1;
        $this->first = isset($data['first']) ? (int)$data['first'] : 0;
        $this->rows = isset($data['rows']) ? (int)$data['rows'] : 100;
        $this->pageCount = isset($data['pageCount']) ? (int)$data['pageCount'] : 0;

        // Parse filters
        if (isset($data['filters']) && is_array($data['filters'])) {
            foreach ($data['filters'] as $fieldName => $filterData) {
                if (is_array($filterData)) {
                    $this->filters->$fieldName = $this->parseFilter($fieldName, $filterData);
                }
            }
        }
    }

    /**
     * Parse a single filter from the query string format
     * 
     * @param string $fieldName The field name (e.g., 'product_code', 'item_code')
     * @param array $filterData The filter data array
     * @return stdClass Parsed filter object
     */
    private function parseFilter(string $fieldName, array $filterData): stdClass {
        $filter = new stdClass();
        $filter->field = $fieldName;
        $filter->operator = $filterData['operator'] ?? 'and';
        $filter->matchMode = $filterData['matchMode'] ?? 'contains';
        $filter->values = [];
        $filter->sqlOperator = self::MATCH_MODES[$filter->matchMode] ?? 'LIKE';

        // Extract values (can be indexed as 0, 1, 2, etc. or as array)
        foreach ($filterData as $key => $value) {
            if (is_numeric($key) || $key === 'value') {
                $filter->values[] = $value;
            }
        }

        // If no numeric keys found, check for 'value' key
        if (empty($filter->values) && isset($filterData['value'])) {
            $filter->values[] = $filterData['value'];
        }

        // Ensure at least one value exists
        if (empty($filter->values)) {
            $filter->values = [];
        }

        return $filter;
    }

    /**
     * Get all filters as an array
     * 
     * @return array Array of filter objects
     */
    public function getFilters(): array {
        $filters = [];
        foreach ($this->filters as $fieldName => $filter) {
            $filters[$fieldName] = $filter;
        }
        return $filters;
    }

    /**
     * Get a specific filter by field name
     * 
     * @param string $fieldName
     * @return stdClass|null
     */
    public function getFilter(string $fieldName): ?stdClass {
        return $this->filters->$fieldName ?? null;
    }

    /**
     * Check if a filter exists for a field
     * 
     * @param string $fieldName
     * @return bool
     */
    public function hasFilter(string $fieldName): bool {
        return isset($this->filters->$fieldName);
    }

    /**
     * Get the SQL value for a match mode
     * This prepares the value based on the match mode (e.g., adding % for LIKE operations)
     * 
     * @param string $matchMode
     * @param mixed $value
     * @return mixed Prepared value for SQL
     */
    public function getSqlValue(string $matchMode, $value) {
        switch ($matchMode) {
            case 'contains':
                return '%' . $value . '%';
            case 'startsWith':
                return $value . '%';
            case 'endsWith':
                return '%' . $value;
            case 'equals':
            case 'notEquals':
            case 'lt':
            case 'lte':
            case 'gt':
            case 'gte':
                return $value;
            case 'between':
                // For between, value should be an array with 2 elements
                return is_array($value) ? $value : [$value, $value];
            case 'in':
                // For in, value should be an array
                return is_array($value) ? $value : [$value];
            default:
                return '%' . $value . '%';
        }
    }

    /**
     * Get pagination offset
     * 
     * @return int
     */
    public function getOffset(): int {
        return $this->first;
    }

    /**
     * Get pagination limit
     * 
     * @return int
     */
    public function getLimit(): int {
        return $this->rows;
    }

    /**
     * Apply filters to a Laravel Eloquent query builder
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $columns Array of allowed column names to filter on. If empty, all columns are allowed.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyToQuery($query, array $columns = [], array $globalColumns = []) {
        $isFirstFilter = true;
        $globalFilter = false;
        $globalSearchValue = '';
        
        // Apply filters
        foreach ($this->filters as $fieldName => $filter) {
            // Validate that the field is in the allowed columns array and get the matched column name
            $globalFilter = isset($filter->field) && $filter->field === 'global';
            if ($globalFilter) {
                $globalSearchValue = $filter->values[0] ?? null;
                // dump($globalSearchValue); // AA100K
            }
           
            $matchedColumn = $this->isFieldAllowed($filter->field, $columns);
            if ($matchedColumn === null) {
                continue; // Skip this filter if field is not allowed
            }
            
            // Store the matched column name in the filter for use in query building
            $filter->columnName = $matchedColumn;
            
            $whereMethod = $filter->operator === 'or' ? 'orWhere' : 'where';
            
            if (!$isFirstFilter && $filter->operator === 'or') {
                $whereMethod = 'orWhere';
            } elseif ($isFirstFilter) {
                $whereMethod = 'where';
            }
            
            $this->applyFilterToQuery($query, $filter, $whereMethod);
            $isFirstFilter = false;
        }
        
        // Apply pagination
        // page = current page (1-based)
        // first = per_page (items per page)
        // rows = total rows loaded (used as maximum limit if needed)
        // ADD GLOBAL SEARCH QUERY -- ABDULLAH 2-6-2026
        if ($globalSearchValue) {
            // Item Code // item_code
            // Product Code // product_code
            // Description // description
            // KM ID // km_item_id
            $query->where('product.product_code', 'LIKE', '%' . $globalSearchValue . '%')
                  ->orWhere('item_code', 'LIKE', '%' . $globalSearchValue . '%')
                  ->orWhere('km_item_id', 'LIKE', '%' . $globalSearchValue . '%')
                  ->orWhere('description', 'LIKE', '%' . $globalSearchValue . '%');
        }
        $offset = $this->first;
        $limit = ($this->page * $this->rows) - $this->first;
        $query->orderBy('product_id', 'ASC');
        $query->orderBy('is_default', 'DESC');
        $query->offset($offset)->limit($limit);

        $this->first = ($this->page * $this->rows) - $this->rows;
        
        
        return $query;
    }

    /**
     * Check if a field is allowed in the columns array and return the matched column name
     * Handles both simple field names and table.field format
     * If a column has a dot, splits it and matches the last portion, then returns the full column name
     * 
     * @param string $field The field name to check
     * @param array $columns Array of allowed columns
     * @return string|null Returns the matched column name from $columns array, or null if not allowed
     */
    private function isFieldAllowed(string $field, array $columns): ?string {
        // If columns array is empty, allow all fields (backward compatibility)
        if (empty($columns)) {
            return $field;
        }

        // Check if field exists in columns (exact match)
        if (in_array($field, $columns, true)) {
            return $field;
        }

        // Check if field matches any column with table prefix (e.g., "table.field" matches "field")
        foreach ($columns as $column) {
            // If column has a dot, split it and get the last portion
            if (strpos($column, '.') !== false) {
                $columnParts = explode('.', $column);
                $columnField = end($columnParts); // Get the last portion after the last dot
                
                // If the filter field matches the last portion, return the full column name
                if ($field === $columnField) {
                    return $column; // Return the full column name (e.g., "product.product_code")
                }
            } else {
                // No dot in column, check for exact match
                if ($field === $column) {
                    return $column;
                }
            }
        }

        return null; // Field not found in allowed columns
    }

    /**
     * Apply a single filter to a query builder
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param stdClass $filter
     * @param string $whereMethod
     * @return void
     */
    private function applyFilterToQuery($query, stdClass $filter, string $whereMethod): void {
        if (empty($filter->values)) {
            return;
        }

        // Use columnName if set (from columns validation), otherwise use field
        $columnName = $filter->columnName ?? $filter->field;

        switch ($filter->matchMode) {
            case 'contains':
            case 'startsWith':
            case 'endsWith':
                $value = $this->getSqlValue($filter->matchMode, $filter->values[0]);
                $query->$whereMethod($columnName, 'LIKE', $value);
                break;
                
            case 'equals':
                $query->$whereMethod($columnName, '=', $filter->values[0]);
                break;
                
            case 'notEquals':
                $query->$whereMethod($columnName, '!=', $filter->values[0]);
                break;
                
            case 'lt':
                $query->$whereMethod($columnName, '<', $filter->values[0]);
                break;
                
            case 'lte':
                $query->$whereMethod($columnName, '<=', $filter->values[0]);
                break;
                
            case 'gt':
                $query->$whereMethod($columnName, '>', $filter->values[0]);
                break;
                
            case 'gte':
                $query->$whereMethod($columnName, '>=', $filter->values[0]);
                break;
                
            case 'between':
                if (count($filter->values) >= 2) {
                    $query->$whereMethod($columnName, 'BETWEEN', [$filter->values[0], $filter->values[1]]);
                }
                break;
                
            case 'in':
                $query->$whereMethod($columnName, 'IN', $filter->values);
                break;
        }
    }

    /**
     * Get SQL WHERE clause conditions as an array for PDO queries
     * Returns array with 'where' (SQL string) and 'params' (bindings array)
     * 
     * @param string $tablePrefix Optional table prefix for field names (only used if column doesn't already have table prefix)
     * @param array $columns Array of allowed column names to filter on. If empty, all columns are allowed.
     * @return array ['where' => string, 'params' => array]
     */
    public function getSqlConditions(string $tablePrefix = '', array $columns = []): array {
        $whereClauses = [];
        $params = [];
        $paramIndex = 0;
        
        foreach ($this->filters as $fieldName => $filter) {
            if (empty($filter->values)) {
                continue;
            }
            
            // Validate that the field is in the allowed columns array and get the matched column name
            $matchedColumn = $this->isFieldAllowed($filter->field, $columns);
            if ($matchedColumn === null) {
                continue; // Skip this filter if field is not allowed
            }
            
            // Use the matched column name. If it already has a table prefix, use it as-is.
            // Otherwise, apply the tablePrefix if provided.
            $field = $matchedColumn;
            if ($tablePrefix && strpos($matchedColumn, '.') === false) {
                // Only add table prefix if the matched column doesn't already have one
                $field = $tablePrefix . '.' . $matchedColumn;
            }
            
            $operator = $filter->operator === 'or' ? 'OR' : 'AND';
            
            $condition = $this->buildSqlCondition($filter, $field, $params, $paramIndex);
            
            if ($condition) {
                if (!empty($whereClauses)) {
                    $whereClauses[] = $operator;
                }
                $whereClauses[] = $condition;
            }
        }
        
        $whereSql = !empty($whereClauses) ? implode(' ', $whereClauses) : '';
        
        return [
            'where' => $whereSql,
            'params' => $params
        ];
    }

    /**
     * Build a single SQL condition
     * 
     * @param stdClass $filter
     * @param string $field
     * @param array &$params
     * @param int &$paramIndex
     * @return string|null
     */
    private function buildSqlCondition(stdClass $filter, string $field, array &$params, int &$paramIndex): ?string {
        $paramName = ':param' . $paramIndex;
        
        switch ($filter->matchMode) {
            case 'contains':
            case 'startsWith':
            case 'endsWith':
                $value = $this->getSqlValue($filter->matchMode, $filter->values[0]);
                $params[$paramName] = $value;
                $paramIndex++;
                return "{$field} LIKE {$paramName}";
                
            case 'equals':
                $params[$paramName] = $filter->values[0];
                $paramIndex++;
                return "{$field} = {$paramName}";
                
            case 'notEquals':
                $params[$paramName] = $filter->values[0];
                $paramIndex++;
                return "{$field} != {$paramName}";
                
            case 'lt':
                $params[$paramName] = $filter->values[0];
                $paramIndex++;
                return "{$field} < {$paramName}";
                
            case 'lte':
                $params[$paramName] = $filter->values[0];
                $paramIndex++;
                return "{$field} <= {$paramName}";
                
            case 'gt':
                $params[$paramName] = $filter->values[0];
                $paramIndex++;
                return "{$field} > {$paramName}";
                
            case 'gte':
                $params[$paramName] = $filter->values[0];
                $paramIndex++;
                return "{$field} >= {$paramName}";
                
            case 'between':
                if (count($filter->values) >= 2) {
                    $paramName1 = ':param' . $paramIndex;
                    $paramIndex++;
                    $paramName2 = ':param' . $paramIndex;
                    $paramIndex++;
                    $params[$paramName1] = $filter->values[0];
                    $params[$paramName2] = $filter->values[1];
                    return "{$field} BETWEEN {$paramName1} AND {$paramName2}";
                }
                break;
                
            case 'in':
                if (!empty($filter->values)) {
                    $placeholders = [];
                    foreach ($filter->values as $value) {
                        $paramName = ':param' . $paramIndex;
                        $params[$paramName] = $value;
                        $placeholders[] = $paramName;
                        $paramIndex++;
                    }
                    return "{$field} IN (" . implode(', ', $placeholders) . ")";
                }
                break;
        }
        
        return null;
    }

    /**
     * Convert to array for easy serialization
     * 
     * @return array
     */
    public function toArray(): array {
        $filters = [];
        foreach ($this->filters as $fieldName => $filter) {
            $filters[$fieldName] = [
                'field' => $filter->field,
                'operator' => $filter->operator,
                'matchMode' => $filter->matchMode,
                'sqlOperator' => $filter->sqlOperator,
                'values' => $filter->values
            ];
        }

        return [
            'filters' => $filters,
            'page' => $this->page,
            'first' => $this->first,
            'rows' => $this->rows,
            'pageCount' => $this->pageCount,
            'offset' => $this->getOffset(),
            'limit' => $this->getLimit()
        ];
    }
}