<?php

// Load environment variables from .env file
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

// Load .env file from project root
loadEnv(__DIR__ . '/../.env');

// Determine environment
$environment = getenv('APP_ENV') ?: 'development';

// Base configuration
$config = [
    'development' => [
        'display_errors' => true,
        'db' => [
            'host' => getenv('DEV_DB_HOST') ?: 'localhost',
            'port' => getenv('DEV_DB_PORT') ?: '3306',
            'name' => getenv('DEV_DB_NAME') ?: 'chinook',
            'user' => getenv('DEV_DB_USER') ?: 'root',
            'pass' => getenv('DEV_DB_PASS') ?: ''
        ]
    ],
    'production' => [
        'display_errors' => false,
        'db' => [
            'host' => getenv('PROD_DB_HOST') ?: '',
            'port' => getenv('PROD_DB_PORT') ?: '3306',
            'name' => getenv('PROD_DB_NAME') ?: '',
            'user' => getenv('PROD_DB_USER') ?: '',
            'pass' => getenv('PROD_DB_PASS') ?: ''
        ]
    ]
];

// Set the configuration based on environment
$currentConfig = $config[$environment];

// Configure error reporting
ini_set('display_errors', $currentConfig['display_errors']);
ini_set('display_startup_errors', $currentConfig['display_errors']);
error_reporting($currentConfig['display_errors'] ? E_ALL : 0);

// Define database constants
define('DB_HOST', $currentConfig['db']['host']);
define('DB_PORT', $currentConfig['db']['port']);
define('DB_NAME', $currentConfig['db']['name']);
define('DB_USER', $currentConfig['db']['user']);
define('DB_PASS', $currentConfig['db']['pass']);

// API settings
define('BASE_URL', '/PHPMandatory2'); // Set base URL for XAMPP subdirectory
define('LOG_FILE', __DIR__ . '/../logs/api.log');

// Set default timezone
date_default_timezone_set('UTC'); 