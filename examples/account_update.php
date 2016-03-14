<?php
require_once('common.php');

try {
    $accountName = ''; //account name
    $displayName = '';
    $email = '';
    $mobile = '';
    $about = '';

    $acount = $client->updateAccount($accountName, $displayName, $email, $mobile, $about);
    print_r($acount);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
