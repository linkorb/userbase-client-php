<?php
require_once('common.php');

try {
    // all data are required //
    $jsonData = json_encode([
        "notificationType" => "",
        "sourceAccountName" => "",
        "subject" => "",
        "body" => "",
        "link" => ""
    ]);

    $users = $client->createNotification($accountName, $jsonData);
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
