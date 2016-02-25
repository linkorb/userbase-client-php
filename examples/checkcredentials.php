<?php

require_once ('common.php');

$username = 'username';
$password = 'password';

if ($client->checkCredentials($username, $password)) {
    echo "CREDENTIALS OK\n";
} else {
    echo "CREDENTIALS INCORRECT\n";
}
