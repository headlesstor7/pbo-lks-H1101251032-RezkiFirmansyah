<?php

namespace App\Config;

use RuntimeException;

class Env
{
    private static array $vars = [];

    public static function load(string $file): void
    {
        if (!file_exists($file)) {
            throw new RuntimeException("File .env tidak ditemukan: $file");
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);

            // Lewati baris kosong atau komentar
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Pisahkan KEY=VALUE (maksimal 2 bagian)
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value, " \"'");

            self::$vars[$key] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$vars[$key] ?? $default;
    }
}