<?php
require_once('common.php');

try {
    $accountName = ''; //account name
    $eventName = ''; // eventname
    $data = ''; // pass as query string x=y&hello=world

    $users = $client->addEvent($accountName, $eventName, $data);
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
