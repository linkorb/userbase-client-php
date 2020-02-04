<?php

require_once('common.php');

try {
    $propertyName = 'test';
    $propertyValue = 'example';

    $users = $client->setAccountProperty($accountName, $propertyName, $propertyValue);
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
