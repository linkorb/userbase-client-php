<?php

require_once 'common.php';

try {
    $accountName = 'xxx';
    $filename = base64_encode(file_get_contents('/image/path'));

    $res = $client->setAccountPicture($accountName, $filename);
    print_r($res);
} catch (Exception $e) {
    echo 'Exception '.$e->getMessage()."\n";
}
