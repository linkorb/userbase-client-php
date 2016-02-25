<?php

require_once (__DIR__ . '/../vendor/autoload.php');

use UserBase\Client\Client;

$baseUrl = 'http://127.0.0.1:8888/api/v1';
$username = getenv('USERBASE_CLIENT_USERNAME');
$password = getenv('USERBASE_CLIENT_PASSWORD');

if (!$username || !$password) {
    echo "Environment variables not yet properly configured\n";
    exit();
}

$client = new Client($baseUrl, $username, $password);
