<?php

/**
 * Request Validation
 *
 * @package Shin
 */

namespace EPOS_CRM\Src\App\Models;

defined('ABSPATH') or die();


class Zippy_Request_Validation
{
    /**
     * Validates a request against a schema.
     *
     * @param array $schema  Field validation rules.
     * @param array $request Incoming request data.
     * @return array List of error messages, or empty array if valid.
     */
    public static function validate_request(array $schema, object $request): array
    {
        $errors = [];

        foreach ($schema as $field => $rules) {
            $isRequired = $rules['required'] ?? false;
            $expectedType = $rules['data_type'] ?? 'string';
            // Check if required field is missing or empty
            if ($isRequired && (!isset($request[$field]))) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                continue;
            }

            if (!isset($request[$field])) {
                continue;
            }

            $value = $request[$field];

            // Type checking
            switch ($expectedType) {
                case 'string':
                    if (!is_string($value)) {
                        $errors[$field] = ucfirst($field) . ' must be a string.';
                    }
                    break;

                case 'integer':
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $errors[$field] = ucfirst($field) . ' must be an integer.';
                    }
                    break;
                case 'float':
                    if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $errors[$field] = ucfirst($field) . ' must be an float.';
                    }
                    break;

                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = ucfirst($field) . ' must be a valid email address.';
                    }
                    break;

                case 'boolean':
                    if (!is_bool($value) && !in_array($value, ['true', 'false', 1, 0, '1', '0'], true)) {
                        $errors[$field] = ucfirst($field) . ' must be a boolean.';
                    }
                    break;

                case 'array':
                    if (!is_array($value)) {
                        $errors[$field] = ucfirst($field) . ' must be a list.';
                    }
                    break;

                // Add more type checks as needed
                default:
                    $errors[$field] = 'Invalid data type rule for ' . $field . '.';
            }
        }

        return $errors;
    }
}
