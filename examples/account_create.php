<?php
require_once('common.php');

if (count($argv)!=3) {
    echo "Please pass 2 parameters: accountname and accounttype (user or organization)\n";
    exit();
}

try {
    $accountName = $argv[1];
    $accountType = $argv[2];
    switch ($accountType) {
        case 'user':
        case 'organization':
            break;
        default:
            exit("Invalid type name: " . $accountType . "\n");
    }

    if ($client->createAccount($accountName, $accountType)) {
        echo "Created...\n";
    } else {
        echo "Unexpected failure\n";
    }
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
