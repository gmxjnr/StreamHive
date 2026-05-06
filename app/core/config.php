<?php

$envPath = __DIR__ . '/../../.env';

if (!file_exists($envPath)) {
    die('.env file not found.');
}

$env = parse_ini_file($envPath);

if ($env === false) {
    die('Could not read .env file. Check your .env syntax.');
}

define('DB_HOST', $env['DB_HOST'] ?? '');
define('DB_NAME', $env['DB_NAME'] ?? '');
define('DB_USER', $env['DB_USER'] ?? '');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('ASSET_URL', '/assets/');