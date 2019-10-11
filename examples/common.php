<?php

require_once __DIR__.'/../vendor/autoload.php';

use Envoi\Envoi;
use Symfony\Component\Dotenv\Dotenv;
use UserBase\Client\Client;


// .env validate
$envFilename = __DIR__.'/../.env';
$envMetaFile = __DIR__.'/../.env.yaml';
Envoi::init($envFilename, $envMetaFile);

$url = getenv('USERBASE_CLIENT_URL');
//$username = getenv('USERBASE_CLIENT_USERNAME');
//$password = getenv('USERBASE_CLIENT_PASSWORD');

if (!$url) {
    echo "Environment variables not yet properly configured\n";
    exit();
}

$client = new Client($url);
