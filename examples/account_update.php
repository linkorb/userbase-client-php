<?php
require_once('common.php');

if (count($argv)!=2) {
    echo "Please pass 1 parameter: accountname\n";
    exit();
}

try {
    $accountName = $argv[1];
    
    $properties = [];
    $properties['displayName'] = 'LinkORB Account';
    $properties['email'] = 'test@example.com';
    $properties['mobile'] = '0987654321';
    $properties['about'] = 'This is the new about message';

    $res = $client->updateAccount($accountName, $properties);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
