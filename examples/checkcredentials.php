<?php

require_once ('common.php');

if (count($argv)!=3) {
    echo "Please pass 2 parameters to check: username and password\n";
    exit();
}
$username = $argv[1];
$password = $argv[2];

if ($client->checkCredentials($username, $password)) {
    echo "CREDENTIALS OK\n";
} else {
    echo "CREDENTIALS INCORRECT\n";
}
