<?php
// ETag generation logic for the resource
// It could be a hash of the content, a version number, or any unique string
$etag = '"some-unique-identifier-for-the-resource"';

// Send the ETag header
header("ETag: $etag");

var_dump($_SERVER);

// Check the If-None-Match header sent by the client
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
    // Client's cached version is up to date; send 304 Not Modified
    header("HTTP/1.1 304 Not Modified");
    exit; // Stop further execution
}

// If the ETag doesn't match, or If-None-Match header is not present,
// proceed to send the actual content
echo "This is the resource content.";
?>
