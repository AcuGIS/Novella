<?php

namespace GeoLibre\Validator;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'required') !== false && (!isset($data[$field]) || empty($data[$field]))) {
                $this->errors[$field] = ucfirst($field) . ' is required';
                continue;
            }

            if (isset($data[$field]) && !empty($data[$field])) {
                if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = 'Invalid email format';
                }

                if (strpos($rule, 'url') !== false && !filter_var($data[$field], FILTER_VALIDATE_URL)) {
                    $this->errors[$field] = 'Invalid URL format';
                }

                if (strpos($rule, 'numeric') !== false && !is_numeric($data[$field])) {
                    $this->errors[$field] = ucfirst($field) . ' must be a number';
                }

                if (strpos($rule, 'min:') !== false) {
                    preg_match('/min:(\d+)/', $rule, $matches);
                    $min = (int)$matches[1];
                    if (strlen($data[$field]) < $min) {
                        $this->errors[$field] = ucfirst($field) . " must be at least {$min} characters";
                    }
                }

                if (strpos($rule, 'max:') !== false) {
                    preg_match('/max:(\d+)/', $rule, $matches);
                    $max = (int)$matches[1];
                    if (strlen($data[$field]) > $max) {
                        $this->errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
} 