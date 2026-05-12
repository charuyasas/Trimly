<?php

namespace App\SMS\Support;

class PhoneNumberFormatter
{
    public static function format(string $localNumber): string
    {
        $cleaned = preg_replace('/\D/', '', $localNumber);
        $cleaned = is_string($cleaned) ? $cleaned : '';
        if ($cleaned === '') {
            return '';
        }
        if (str_starts_with($cleaned, '0')) {
            return '94'.substr($cleaned, 1);
        }

        return $cleaned;
    }
}
