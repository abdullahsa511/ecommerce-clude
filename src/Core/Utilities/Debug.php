<?php

namespace App\Core\Utilities;

class Debug
{
    public static function dd($data, $exit = true): void
    {
        if (!defined('ENVIRONMENT') || ENVIRONMENT === 'development') {
            echo "<pre style='background: #070303ff; padding: 10px; border: 1px solid #ccccccff; color: white;'>";
            print_r($data);
            echo "</pre>";
            if ($exit) {
                exit;
            }
        }
    }
}
