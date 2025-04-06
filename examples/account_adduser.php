<?php

require_once('common.php');

if (count($argv)!=4) {
    echo "Please pass 3 parameters: accountname, username and isadmin (true or false)\n";
    exit();
}

try {
    $accountName = $argv[1];
    $userName = $argv[2];
    $isAdmin = $argv[3];

    $res = $client->addAccountUser($accountName, $userName, $isAdmin);
    if ($res) {
        echo "User added to account successfully\n";
    } else {
        echo "Unexpected failure\n";
    }
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
