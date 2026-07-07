<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Exceptions\ValidationException;
use Illuminate\Container\Container;
use PDO;
use PDOException;

/**
 * A basic Request class that encapsulates data from the global
 * PHP superglobals. It allows easy retrieval of HTTP method, URI,
 * query parameters, POST data, files, etc.
 */
class Request
{
    /**
     * Arbitrary data storage for middleware/controllers.
     *
     * @var array<string,mixed>
     */
    public array $attributes = [];

    /**
     * @var array<string,mixed> $query   Typically from $_GET
     */
    private array $query;
    /**
     * @var array<string,mixed> $post    Typically from $_POST
     */
    private array $post;
    /**
     * @var array<string,mixed> $server  Typically from $_SERVER
     */
    private array $server;
    /**
     * @var array<string,mixed> $cookies Typically from $_COOKIE
     */
    private array $cookies;
    /**
     * @var array<string,mixed> $files   Typically from $_FILES
     */
    private array $files;

    /**
     * @var array<string,mixed> $request   Typically from $_REQUEST
     */
    public array $request;

    protected static self $instance;

    private Container $container;

    public function __construct(
        Container $container
    ) {
        $this->query = &$_GET;
        $this->post = &$_POST;
        $this->server = &$_SERVER;
        $this->cookies = &$_COOKIE;
        $this->files = &$_FILES;
        $this->request = &$_REQUEST;
        $this->container = $container;
        $this->mergeJsonBodyIntoPost();
    }

    /**
     * Merge JSON request bodies into $this->post so $request->all() / input() work for API clients.
     * Handles application/json with charset, vendor+json types, and JSON bodies with a missing Content-Type.
     */
    private function mergeJsonBodyIntoPost(): void
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        $contentType = strtolower(trim((string) ($this->header('Content-Type') ?? '')));
        if ($contentType !== '' && str_starts_with($contentType, 'multipart/form-data')) {
            return;
        }

        $raw = file_get_contents('php://input');
        if (!is_string($raw) || $raw === '') {
            return;
        }

        $trimmed = ltrim($raw);
        if ($trimmed === '' || ($trimmed[0] !== '{' && $trimmed[0] !== '[')) {
            return;
        }

        $treatAsJson = $contentType === ''
            || str_starts_with($contentType, 'application/json')
            || str_starts_with($contentType, 'text/json')
            || str_contains($contentType, '+json');
        if (!$treatAsJson) {
            return;
        }

        $decodedJson = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedJson)) {
            return;
        }

        $this->post = array_merge($this->post, $decodedJson);
    }

    public static function getInstance(): Request
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Factory method to create a Request from global PHP superglobals.
     */
    public static function capture(): self
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Retrieve the HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Retrieve the request URI (path only, without query string).
     */
    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        // Remove any query string part (e.g. '?foo=bar')
        $uri = explode('?', $uri)[0];
        // Ensure we have a leading slash
        if ($uri === '') {
            $uri = '/';
        }
        return $uri;
    }

    /**
     * Retrieve a POST input value by key, with optional default.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Return an associative array of all POST data.
     *
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->post;
    }

    /**
     * Retrieve a query (GET) parameter by key, with optional default.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function query(string|null $key = null, mixed $default = null): mixed
    {
        return $key ? $this->query[$key] ?? $default : $this->query;
    }

    /**
     * Return all query parameters as an associative array.
     *
     * @return array<string,mixed>
     */
    public function allQuery(): array
    {
        return $this->query;
    }

    /**
     * Retrieve a cookie value by key, with optional default.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Retrieve a header by name (e.g., "Content-Type").
     * Note: HTTP headers are typically in $_SERVER as 'HTTP_<NAME>'.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function header(string $name, mixed $default = null): mixed
    {
        // Convert a header name like "Content-Type" to "HTTP_CONTENT_TYPE"
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? $default;
    }

    /**
     * Retrieve a file array (if present).
     *
     * @param  string $key
     * @return array<string,mixed>|null
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function files(): ?array
    {
        return $this->files;
    }

    /**
     * Retrieve the raw server array if needed.
     *
     * @return array<string,mixed>
     */
    public function getServerParams(): array
    {
        return $this->server;
    }

    /**
     * Return an associative array of all HTTP headers.
     * E.g. ['Host' => 'example.com', 'Authorization' => 'Bearer xyz', ...].
     */
    public function getHeaders(): array
    {
        $headers = [];

        // Loop through $this->server looking for HTTP_ keys.
        foreach ($this->server as $key => $value) {
            // Common approach: check if it starts with "HTTP_" (typical for HTTP headers in $_SERVER).
            if (str_starts_with($key, 'HTTP_')) {
                // Convert HTTP_HEADER_NAME to Header-Name
                $headerName = str_replace(
                    ' ',
                    '-',
                    ucwords(strtolower(str_replace('_', ' ', substr($key, 5))))
                );
                $headers[$headerName] = $value;
            }
        }

        // Handle a few special cases like "CONTENT_TYPE" and "CONTENT_LENGTH", which
        // don't come prefixed with "HTTP_"
        if (isset($this->server['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $this->server['CONTENT_TYPE'];
        }
        if (isset($this->server['CONTENT_LENGTH'])) {
            $headers['Content-Length'] = $this->server['CONTENT_LENGTH'];
        }

        return $headers;
    }
    /**
     * Get an attribute by name.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set an attribute by name.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Validates the request input against the given rules.
     *
     * @param array<string, string> $rules Validation rules for the input data.
     * @return array<string, mixed> The validated data.
     * @throws ValidationException If validation fails.
     */
    public function validate(array $rules, array $values = []): array
    {
        $validatedData = [];
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            // If the field contains a dot, split it and check the associated key in $values
            if (strpos($field, '.') !== false) {
                [$parent, $child] = explode('.', $field, 2);
                // If values not given, fallback to POST
                $searchBase = count($values) ? $values : $this->post;
                if (isset($searchBase[$parent]) && is_array($searchBase[$parent]) && array_key_exists($child, $searchBase[$parent])) {
                    $value = $searchBase[$parent][$child];
                    $fieldKey = $child;
                    $values[$fieldKey] = $value;
                } else {
                    $values[$field] = null;
                }
            }else{
                $value = $values[$field] ?? $this->input($field);
                $fieldKey = $field;
            }

            $value = is_string($value) ? trim($value) : $value;
            $rulesArray = explode('|', $ruleString);

            if ($this->canSkipValidation($field, $rulesArray)) {
                $validatedData[$field] = $value;
                continue;
            }

            foreach ($rulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $isValid = match ($ruleName) {
                    'required' => $value !== null && $value !== '',
                    'string' => is_string($value),
                    'int' => filter_var($value, FILTER_VALIDATE_INT) !== false,
                    'numeric' => $this->passesNumericRule($value),
                    'decimal' => $this->passesDecimalRule($value, $ruleParam),
                    'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
                    'phone' => preg_match('/^\+?[0-9\s\-]{7,20}$/', $value??'') === 1,
                    'url' => filter_var($value, FILTER_VALIDATE_URL) !== false,
                    'min' => is_string($value) && strlen($value) >= (int) $ruleParam || is_int($value) && strlen((string) $value) >= (int) $ruleParam,
                    'max' => $this->passesMaxRule($value, $ruleParam, $rulesArray),
                    'same' => $value === $this->input($ruleParam) || $this->canSkipValidation($field, $rulesArray),
                    'array' => is_array($value) || $this->canSkipValidation($field, $rulesArray),
                    'exists' => $this->exists($value, $ruleParam) || $this->canSkipValidation($field, $rulesArray),
                    'notExists' => $this->notExists($value, $ruleParam) || $this->canSkipValidation($field, $rulesArray),
                    default => true, // Unknown rule, ignore
                };

                if (!$isValid) {
                    if($ruleName === 'required'){
                        $errors[$field][] = "The $field field is required.";
                    } else if($ruleName === 'exists'){
                        $errors[$field][] = "A record with the $field having $value already exists.";
                    } else if($ruleName === 'decimal'){
                        $errors[$field][] = $this->buildDecimalErrorMessage($field, $ruleParam);
                    } else if($ruleName === 'max'){
                        $errors[$field][] = $this->buildMaxErrorMessage($field, $ruleParam, $value, $rulesArray);
                    }else{
                        $errors[$field][] = "Invalid $field input. $field must be $ruleName";
                    }
                }
            }
            $values = count($values)?$values:$this->post;
            if (!isset($errors[$field]) && array_key_exists($fieldKey, $values)) {
                $validatedData[$fieldKey] = $value;
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $validatedData;
    }
    private function passesNumericRule(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return is_numeric($value);
    }

    private function passesMaxRule(mixed $value, ?string $ruleParam, array $rules): bool
    {
        if ($ruleParam === null || $ruleParam === '') {
            return true;
        }

        $limit = (int) $ruleParam;
        $treatAsNumeric = (
            in_array('int', $rules, true)
            || in_array('numeric', $rules, true)
        ) && $this->passesNumericRule($value);

        if ($treatAsNumeric) {
            return (float) $value <= $limit;
        }

        if (is_string($value)) {
            return strlen($value) <= $limit;
        }

        if (is_array($value)) {
            return count($value) <= $limit;
        }

        return true;
    }

    private function passesDecimalRule(mixed $value, ?string $ruleParam): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (!is_numeric($value)) {
            return false;
        }

        if ($ruleParam === null || $ruleParam === '') {
            return true;
        }

        $parts = array_map('trim', explode(',', $ruleParam));
        $precision = isset($parts[0]) ? (int) $parts[0] : 0;
        $scale = isset($parts[1]) ? (int) $parts[1] : 0;

        $numericString = (string) $value;

        if (!preg_match('/^-?(?:\d+|\d*\.\d+)$/', $numericString)) {
            return false;
        }

        $unsigned = ltrim($numericString, '+-');
        if (str_contains($unsigned, '.')) {
            [$integerPart, $decimalPart] = explode('.', $unsigned, 2);
        } else {
            $integerPart = $unsigned;
            $decimalPart = '';
        }

        $normalizedInteger = ltrim($integerPart, '0');
        if ($integerPart !== '' && $normalizedInteger === '') {
            $integerDigits = 1;
        } elseif ($integerPart === '') {
            $integerDigits = 0;
        } else {
            $integerDigits = strlen($normalizedInteger);
        }

        $decimalDigits = strlen($decimalPart);
        $totalDigits = $integerDigits + $decimalDigits;

        if ($precision > 0 && $totalDigits > $precision) {
            return false;
        }

        if ($scale > 0 && $decimalDigits > $scale) {
            return false;
        }

        return true;
    }

    private function buildDecimalErrorMessage(string $field, ?string $ruleParam): string
    {
        if ($ruleParam === null || $ruleParam === '') {
            return "Invalid $field input. $field must be a decimal number.";
        }

        $parts = array_map('trim', explode(',', $ruleParam));
        $precision = isset($parts[0]) ? (int) $parts[0] : null;
        $scale = isset($parts[1]) ? (int) $parts[1] : null;

        if ($precision !== null && $scale !== null) {
            return "Invalid $field input. $field must be a decimal number with up to $precision digits and $scale decimal places.";
        }

        return "Invalid $field input. $field must be a decimal number.";
    }

    private function buildMaxErrorMessage(string $field, ?string $ruleParam, mixed $value, array $rules): string
    {
        if ($ruleParam === null || $ruleParam === '') {
            return "Invalid $field input. $field exceeds the allowed maximum.";
        }

        $limit = (int) $ruleParam;
        $treatAsNumeric = (
            in_array('int', $rules, true)
            || in_array('numeric', $rules, true)
        ) && $this->passesNumericRule($value);

        if ($treatAsNumeric) {
            return "Invalid $field input. $field must not be greater than $limit.";
        }

        if (is_string($value)) {
            return "Invalid $field input. $field must not be longer than $limit characters.";
        }

        if (is_array($value)) {
            return "Invalid $field input. $field must not contain more than $limit items.";
        }

        return "Invalid $field input. $field exceeds the allowed maximum of $limit.";
    }
    /**
     * Check if a value exists in a table.
     *
     * @param string|int|mixed $value The value to check.
     * @param string $table The table to check.
     * @return bool True if the value exists, false otherwise.
     * @throws PDOException If there is an error executing the query.
     */
    public function exists(mixed $value, string $ruleParam): bool
    {
        [$table, $column] = explode(',', $ruleParam);
        // Sanitize table and column names to prevent SQL injection
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

        $table = '`'.$table.'`';
        $column = '`'.$column.'`';

        try {
            $db = $this->container->make(PDO::class);
            $query = "SELECT COUNT(*) FROM $table WHERE $column = ?";
            // Prepare the statement
            $stmt = $db->prepare($query);

            // Bind the parameter
            $stmt->bindParam(1, $value);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetchColumn();

            return $result > 0;
        } catch (PDOException $e) {
            // Log the error or handle it as needed
            error_log($e->getMessage());
            return false;
        }
    }
    /**
     * Check if a value exists in a table.
     *
     * @param string|int|mixed $value The value to check.
     * @param string $table The table to check.
     * @return bool True if the value exists, false otherwise.
     * @throws PDOException If there is an error executing the query.
     */
    public function notExists(mixed $value, string $ruleParam): bool
    {
        [$table, $column,$idColumn,$id] = explode(',', $ruleParam);
        // Sanitize table and column names to prevent SQL injection
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $idColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $idColumn);

        $table = '`'.$table.'`';
        $column = '`'.$column.'`';
        $idColumn = '`'.$idColumn.'`';

        try {
            $db = $this->container->make(PDO::class);
            $query = "SELECT COUNT(*) FROM $table WHERE $column = ?";
            if ($idColumn && $id) {
                $query .= " AND $idColumn != ?";
            }
            // Prepare the statement
            $stmt = $db->prepare($query);

            // Bind the parameter
            $stmt->bindParam(1, $value);
            if ($idColumn && $id) {
                $stmt->bindParam(2, $id);
            }

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetchColumn();

            return $result === 0;
        } catch (PDOException $e) {
            // Log the error or handle it as needed
            error_log($e->getMessage());
            return false;
        }
    }
    public function canSkipValidation(string $field, array $rulesArray): bool
    {
        return (!in_array('required', $rulesArray) && !array_key_exists($field, $this->post))
                || (in_array('nullable', $rulesArray) && array_key_exists($field, $this->post));
    }
}
