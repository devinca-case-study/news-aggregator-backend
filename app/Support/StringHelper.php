<?php

namespace App\Support;

class StringHelper
{
    public static function clean(?string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', (string)$text));
    }

    public static function cleanUrl(?string $url): string
    {
        return trim((string) $url);
    }
}