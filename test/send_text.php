<?php

require_once "../vendor/autoload.php";

use Diversen\Sendfile;

$s = new SendFile();
// $s->setExpires(3600);

// Bytes per 1 second
$s->throttle(1, 16 * 1024);

// file
$file = './text_file.txt';

// send the file
try {
    $s->send($file, false);
} catch (\Exception $e) {
    echo $e->getMessage();
}
