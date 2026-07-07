<?php

namespace App\Core\Validation;

class FieldValidator
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function phone(string $phone): bool
    {
        return preg_match('/^\+?[0-9\s\-]{7,20}$/', $phone) === 1;
    }

    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) continue;

            switch ($rule) {
                case 'email':
                    if (!self::email($data[$field])) {
                        $errors[$field] = "Invalid email address";
                    }
                    break;

                case 'phone':
                    if (!self::phone($data[$field])) {
                        $errors[$field] = "Invalid phone number";
                    }
                    break;
            }
        }

        return $errors;
    }
}
