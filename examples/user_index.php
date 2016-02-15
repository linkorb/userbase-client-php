<?php

require_once('common.php');

try {
    $users = $client->getUsersWithDetails();
    print_r($users);
} catch (Exception $e) {
    echo "Exception " . $e->getMessage() . "\n";
}
