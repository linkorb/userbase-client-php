<?php

require_once ('common.php');

if (count($argv)!=2) {
    echo "Please pass 1 parameter: username\n";
    exit();
}
try {
    $username = $argv[1];
    $user = $client->getUserByUsername($username);
    print_r($user);
    foreach ($user->getAccounts() as $account) {
        echo " * Account: " . $account->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
