<?php

require_once "../vendor/autoload.php";

use Diversen\Sendfile;

// Send headers indicating that download may be cached
header('Cache-Control: public, max-age=3600');


$s = new SendFile();
$s->setExpires(3600);

// Bytes per 1 second
$s->throttle(1, 1024);

// file
$file = './dummy.pdf';

// send the file
try {
    $s->send($file, true);
} catch (\Exception $e) {
    echo $e->getMessage();
}
