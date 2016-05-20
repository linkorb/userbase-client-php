<?php
require_once('common.php');

try {
    $accountName = ''; //account name
    $accountType = ''; // username

    $acount = $client->createAccount($accountName, $accountType);
    print_r($acount);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
