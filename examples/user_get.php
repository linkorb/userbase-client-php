<?php

require_once ('common.php');

try {
    $user = $client->getUserByUsername('joost');
    print_r($user);
    foreach ($user->getAccounts() as $account) {
        echo " * Account: " . $account->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
