<?php

function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0 || empty(trim($line))) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/../.env');

$environment = getenv('APP_ENV') ?: 'development';

$config = [
    'development' => [
        'display_errors' => true,
        'db' => [
            'host' => getenv('DEV_DB_HOST') ?: '',
            'port' => getenv('DEV_DB_PORT') ?: '',
            'name' => getenv('DEV_DB_NAME') ?: '',
            'user' => getenv('DEV_DB_USER') ?: '',
            'pass' => getenv('DEV_DB_PASS') ?: ''
        ]
    ],
    'production' => [
        'display_errors' => false,
        'db' => [
            'host' => getenv('PROD_DB_HOST') ?: '',
            'port' => getenv('PROD_DB_PORT') ?: '',
            'name' => getenv('PROD_DB_NAME') ?: '',
            'user' => getenv('PROD_DB_USER') ?: '',
            'pass' => getenv('PROD_DB_PASS') ?: ''
        ]
    ]
];

$currentConfig = $config[$environment];

ini_set('display_errors', $currentConfig['display_errors']);
ini_set('display_startup_errors', $currentConfig['display_errors']);
error_reporting($currentConfig['display_errors'] ? E_ALL : 0);

define('DB_HOST', $currentConfig['db']['host']);
define('DB_PORT', $currentConfig['db']['port']);
define('DB_NAME', $currentConfig['db']['name']);
define('DB_USER', $currentConfig['db']['user']);
define('DB_PASS', $currentConfig['db']['pass']);

define('BASE_URL', '/PHPMandatory2');
define('LOG_FILE', __DIR__ . '/../logs/api.log');

date_default_timezone_set('UTC'); 