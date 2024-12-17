<?php

namespace App\Services;

class CsvService
{
    public static function getFieldValue(string $field): mixed
    {
        $value = trim($field);
        $value = str_replace('"', '', $value);

        if (strtolower($value) === 'true') {
            $value = true;
        } elseif (strtolower($value) === 'false') {
            $value = false;
        } elseif (is_numeric($value)) {
            $value = floatval($value);
        }

        return $value;
    }
}
