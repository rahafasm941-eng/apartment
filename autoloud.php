<?php
spl_autoload_register(function ($class) {
    $prefixes = [
        'Google\\' => __DIR__ . '/google-api-php-client/src/',
        'Google\\Auth\\' => __DIR__ . '/google-auth-library-php/src/',
        'GuzzleHttp\\' => __DIR__ . '/guzzle/src/',
        'Psr\\Log\\' => __DIR__ . '/psr-log/Psr/Log/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($class, $prefix, strlen($prefix)) === 0) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    }
});
