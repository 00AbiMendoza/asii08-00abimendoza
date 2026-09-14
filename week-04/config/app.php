<?php

declare(strict_types=1);

$environmentFile = dirname(__DIR__) . '/.env';

if (is_file($environmentFile)) {
    $lines = file(
        $environmentFile,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value);

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }
}

return [
    'app_env' => getenv('APP_ENV') ?: 'development',
    'db_driver' => getenv('DB_DRIVER') ?: 'sqlite',
    'db_path' => dirname(__DIR__) . '/' . (getenv('DB_PATH') ?: 'database/micro_his.sqlite'),
];
