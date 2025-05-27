<?php

// Determine environment
$environment = getenv('APP_ENV') ?: 'production'; // Default to production for InfinityFree

// Base configuration
$config = [
    'development' => [
        'display_errors' => true,
        'db' => [
            'host' => 'localhost',
            'port' => '3306',
            'name' => 'chinook',
            'user' => 'your_dev_username',
            'pass' => 'your_dev_password'
        ]
    ],
    'production' => [
        'display_errors' => false,
        'db' => [
            'host' => 'sql205.infinityfree.com',
            'port' => '3306',
            'name' => 'if0_39010221_chinook',
            'user' => 'if0_39010221',
            'pass' => '0tQoXYmcRP'
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
define('BASE_URL', ''); // Empty for production
define('LOG_FILE', __DIR__ . '/../logs/api.log');

// Set default timezone
date_default_timezone_set('UTC'); 