<?php

require_once 'common.php';

try {
    $data = base64_encode(file_get_contents(__DIR__ . '/picture.png'));

    $res = $client->setAccountPicture($accountName, $data);
    print_r($res);
} catch (Exception $e) {
    echo 'Exception '.$e->getMessage()."\n";
}
