<?php

include_once "../vendor/autoload.php";

use diversen\sendfile;

$s = new sendfile();
$s->contentDisposition('test.txt');

// if you don't set type - we will try to guess it

// if you don't set disposition (file name user agent will see)
// we will make a file name from file
//$s->contentDisposition('test.epub');

// 2048 bytes per 2 sec
$s->throttle(1, 2048);

// file
$file = './large_file.txt';

// send the file
try {
    $s->send($file);
} catch (\Exception $e) {
    echo $e->getMessage();
}
 