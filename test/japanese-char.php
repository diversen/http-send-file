<?php

// git clone https://github.com/diversen/http-send-file
// cd test/
// php -S localhost:8080
// oppen browser and go to: 
// http://localhost:8080/japanese-char.php
// Should download file with japanese char in filename

include_once "../sendFile.php";

use diversen\sendFile;

$s = new sendfile();

// if you don't set type - we will try to guess it

// if you don't set disposition (file name user agent will see)
// we will make a file name from file
//$s->contentDisposition('test.epub');

// chunks of 40960 bytes per 0.1 secs
// if you don't set this then the values below are the defaults
// approx 409600 bytes per sec
$s->throttle(0.1, 40960);

// file
$file = './二天一流.txt';

// send the file
try {
    $s->send($file);
} catch (\Exception $e) {
    echo $e->getMessage();
}
 
