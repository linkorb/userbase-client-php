<?php

require_once('common.php');

try {
    $accountName = 'qwe';
    $filename = 'test.png';

    $res = $client->setAccountPicture($accountName, $filename);
    print_r($res);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
