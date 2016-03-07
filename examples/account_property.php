<?php

require_once('common.php');

try {
    $accountName = 'Maruti';
    $propertyName = 'suv';
    $propertyValue = 'x cross';

    $users = $client->setAccountProperty($accountName, $propertyName, $propertyValue);
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
