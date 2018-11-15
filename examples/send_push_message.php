<?php

require_once 'common.php';

if (3 != count($argv)) {
    echo "Please pass 1 parameter: username\n";
    echo "Please pass 2 parameter: message\n";
    exit();
}
try {
    $username = $argv[1];
    $message = $argv[2];
    $data = [
        'word' => 'hello',
        'key' => 'test',
    ];

    $result = $client->sendPushMessage($username, $message, $data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception '.$e->getMessage()."\n";
}
