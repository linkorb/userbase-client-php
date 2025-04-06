<?php

require_once ('common.php');

try {
    $user = $client->getUserByUsername($accountName);
    echo "Username: " . $user->getName() . "\n";
    echo "DisplayName: " . $user->getDisplayName() . "\n";
    echo "Email: " . $user->getEmail() . "\n";
    echo "PictureUrl: " . $user->getPictureUrl() . "\n";
    
    foreach ($user->getAccounts() as $account) {
        echo " * Account: " . $account->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
