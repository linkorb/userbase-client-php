<?php
require_once('common.php');

try {
    $jsonData = json_encode([
        "notificationType" => "",
        "status" => "",
    ]);

    $users = $client->getNotifications($accountName, $jsonData);
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
