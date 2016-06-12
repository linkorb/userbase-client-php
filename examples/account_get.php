<?php

require_once ('common.php');

if (count($argv)!=2) {
    echo "Please pass 1 parameter: username\n";
    exit();
}

try {
    $accountName = $argv[1];
    $account = $client->getAccountByName($accountName);
    if (!$account) {
        echo "No such account\n";
    } else {
        print_r($account);
    }
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
