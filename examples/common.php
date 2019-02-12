<?php

require_once __DIR__.'/../vendor/autoload.php';

use UserBase\Client\Client;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$url = getenv('USERBASE_CLIENT_URL');
$username = getenv('USERBASE_CLIENT_USERNAME');
$password = getenv('USERBASE_CLIENT_PASSWORD');

if (!$url) {
    echo "Environment variables not yet properly configured\n";
    exit();
}

$client = new Client($url, $username, $password);
