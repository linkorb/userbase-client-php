<?php

require_once ('common.php');

try {
    $accounts = $client->getAccountsWithDetails();
    print_r($accounts);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
