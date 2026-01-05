<?php

$envPath = dirname(__DIR__) . '/.env';

if (!file_exists($envPath)) {
    die('.env file not found');
}

$env = parse_ini_file($envPath);

if (!$env) {
    die('Failed to load .env');
}
