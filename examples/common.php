<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use UserBase\Client\Client;

$baseUrl = getenv('USERBASE_CLIENT_BASEURL');
$username = getenv('USERBASE_CLIENT_USERNAME');
$password = getenv('USERBASE_CLIENT_PASSWORD');

if (!$username || !$password || !$baseUrl) {
    echo "Environment variables not yet properly configured\n";
    exit();
}

$client = new Client($baseUrl, $username, $password);
