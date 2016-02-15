<?php

require_once ('common.php');

try {
    $account = $client->getAccountByName('joost');
    print_r($account);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
