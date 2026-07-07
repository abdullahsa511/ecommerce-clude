<?php 

declare(strict_types=1);

namespace App\Core\Validation;

abstract class Validation
{
    protected bool $isValidData = true;
    protected array $errors = [];
    public array $rawData = [];
    protected array $requiredFields = [];
    protected array $textFields = [];
    public bool $isExistingData = false;

    public abstract function toArray(): array;

    public function __construct(array $requiredFields = [], array $textFields = [])
    {
        $this->requiredFields = $requiredFields;
        $this->textFields = $textFields;
    }

    protected function generateSlugFromName(string $value, string $field): string
    {
        $value = $this->fixTextEncoding($value, $field);
        if (!$value) return '';
        return $this->validateSlug($value, $field) ?? '';
    }

    protected function validateInteger($value, string $field, ?int $default = null, bool $isMandatory = false): ?int
    {
        $value = $this->fixTextEncoding($value, $field);
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return $default; }
        if (!is_numeric($value)) { $this->addError($field, 'must be a valid integer'); return $default; }
        $int = (int)$value;
        if ($int < 0) { $this->addError($field, 'must be a positive integer'); return $default; }
        if(in_array($field, $this->requiredFields) && (($value == '') || $value === null)){
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $int;
    }

    protected function validateString($value, string $field, int $maxLength, bool $isMandatory = false): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
        $s = trim($value);
        if (strlen($s) > $maxLength) { $s = substr($s, 0, $maxLength); }
        if(in_array($field, $this->requiredFields) && (($value == '') || $value === null)){
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $s;
    }

    protected function validateSlug($value, string $field): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
        $slug = strtolower(trim($value));
        // $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        // $slug = preg_replace('/-+/', '-', $slug);
        // spaces → -
        $slug = preg_replace('/\s+/', '-', $slug);
        // remove all special characters except -
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        // remove multiple -
        $slug = preg_replace('/-+/', '-', $slug);
        // trim - from start & end
        $slug = trim($slug, '-');
        if(in_array($field, $this->requiredFields) && (($value == '') || $value === null)){
            $this->addError($field, 'is mandatory');
            return null;
        }
        return substr($slug, 0, 191);
    }

    protected function validateJson($value, string $field, string $path = ''): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($value === '' || $value === null) { return '[]'; }
        $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);
        if (!$this->isValidJson($value)) { 
            // If not JSON, create a simple JSON structure for products
            if (!str_contains($value, $path)) { $value = "{$path}/{$value}"; }
            $data = [[ 'id'=>null,'file'=>['name'=>basename($value),'size'=>0,'type'=>'image/jpeg','error'=>0,'tmp_name'=>$value,'full_path'=>basename($value)],'name'=>basename($value),'size'=>0,'type'=>'image/jpeg','image'=>$value,'status'=>['name'=>'Uploaded','severity'=>'success'],'media_id'=>null,'objectURL'=>$value,'created_at'=>'','description'=>'','product_image_id'=>null ]];
            return json_encode($data) ?: '[]';
        }
        return $value;
    }

    protected function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function validateFloat($value, string $field, ?float $default = null, bool $isMandatory = false): ?float
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') {
            return $default;
        }
        if (!is_numeric($value)) {
            $this->addError($field, 'must be a valid number');
            return $default;
        }
        $float = (float)$value;
        if ($float < 0) {
            $this->addError($field, 'must be a positive number');
            return $default;
        }
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $float;
    }

    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
        $this->isValidData = false;
    }

    public function validate(): bool|self
    {
        if (!$this->isValidData) { return false; }
        return $this;
    }

    public function getErrors($format = false): array
    {
        if ($format) {
            return $this->validationErrors($format);
        }
        return $this->errors;
    }

    public function validationErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $field => $message) {
            if (is_array($message)) {
                $errors[$field] = $message;
            } else {
                $errors[$field] = ["The {$field} field is required."];
            }
        }
        return $errors;
    }

    public function getUniqueIdentifier(): string
    {
        return 'unique_' . uniqid();
    }


    protected function fixTextEncoding(string|int|float|null $value, string $field): string|int|float|null
    {
        $textFields = $this->textFields;
        if(in_array($field, $textFields)){
            if (isset($value) && is_string($value) && $value !== '') {
                if (mb_check_encoding($value, 'UTF-8')) {
                    $replacements = [
                        "\x92" => "'",
                        "\x93" => '"',
                        "\x94" => '"',
                        "\x96" => "–",
                        "\x97" => "—",
                        "\x85" => "…",
                        "\x91" => "'",
                        "\x82" => ",",
                        "\x84" => "„",
                        "\x8B" => "‹",
                        "\x9B" => "›",
                    ];
                    $value = strtr($value, $replacements);
                }
            }
            // Only pass strings or arrays to mb_convert_encoding; cast numeric values to string
            if (is_string($value) || is_array($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            } elseif (is_int($value) || is_float($value)) {
                $value = (string)$value;
            }
        }
        return $value;
    }

    // valid email
    protected function validateEmail(string $value, string $field, int $maxLength): ?string
    {
        $value = $this->fixTextEncoding($value, $field);

        // If empty, prefer required-field message; otherwise treat as optional and return null.
        if ($value === '' || $value === null) {
            if (in_array($field, $this->requiredFields)) {
                $this->addError($field, 'is mandatory');
                return null;
            }
            return null;
        }

        if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) { $this->addError($field, 'must be a valid email'); return null; }
        if (strlen($value) > $maxLength) { $value = substr($value, 0, $maxLength); }
        return $value;
    }

    // valid url
    protected function validateUrl(string $value, string $field): ?string
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) { $this->addError($field, 'must be a valid url'); return null; }
        return $value;
    }

    protected function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }
    
    protected function validateText($value, string $field, bool $isMandatory = false): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return trim($value);
    }

    protected function validateDate($value, $field, $maxLength = null)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if ($maxLength !== null && strlen($value) > $maxLength) {
            throw new \Exception("The {$field} exceeds the maximum allowed length of {$maxLength} characters.");
        }

        // Accepted formats
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
            'd-m-Y'
        ];

        $dateObj = null;

        foreach ($formats as $format) {
            $temp = \DateTime::createFromFormat($format, $value);
            if ($temp && $temp->format($format) === $value) {
                $dateObj = $temp;
                break;
            }
        }

        if (!$dateObj) {
            throw new \Exception("The {$field} must be a valid date. Accepted formats: Y-m-d, d/m/Y, m/d/Y, Y/m/d, d-m-Y.");
        }

        // Normalize to Y-m-d for DB
        return $dateObj->format('Y-m-d');
    }

}