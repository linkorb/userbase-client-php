<?php

require_once('common.php');

try {
    $accountName = ''; //account name
    $userName = ''; // username
    $isAdmin = 'false'; // ture/false

    $users = $client->addAccountUser($accountName, $userName, $isAdmin);
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
