<?php

require_once "../vendor/autoload.php";

use Diversen\Sendfile;

$s = new SendFile();
$s->setExpires(3600);

// 8096 bytes per 1 sec
$s->throttle(1, 8096);

// file
$file = './dummy.pdf';

// send the file
try {
    $s->send($file, true);
} catch (\Exception $e) {
    echo $e->getMessage();
}
