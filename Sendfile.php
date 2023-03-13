<?php

namespace Diversen;

use Exception;

/**
 * Sends a file to a client, with support for (multiple) range requests. 
 * It is also able to throttle the download.", 
 */
class Sendfile
{
    /**
     * if false we set content disposition from file that will be sent 
     */
    private string $disposition = '';

    /**
     * throttle speed in secounds
     */
    private float $sec = 0.1;

    /**
     * bytes per $sec
     */
    private int $bytes = 40960;

    /**
     * if contentType is false we try to guess it
     */
    private string $content_type = '';

    /**
     * set content disposition. Name of file that will be sent to client
     * if empty we try to guess it from file path
     */
    public function setContentDisposition(string $file_name = '')
    {
        $this->disposition = $file_name;
    }

    /**
     * set throttle speed
     */
    public function throttle(float $sec = 0.1, int $bytes = 40960)
    {
        $this->sec = $sec;
        $this->bytes = $bytes;
    }

    /**
     * set content content type if empty we try to guess it
     */
    public function setContentType(string $content_type = '')
    {
        $this->content_type = $content_type;
    }

    /**
     * get name from path info
     */
    private function getBaseName(string $file)
    {
        $info = pathinfo($file);
        return $info['basename'];
    }

    /**
     * Setup headers and starts transfering bytes
     */
    public function send(string $file_path, bool $with_disposition = true)
    {

        if (!is_readable($file_path)) {
            throw new Exception('File not found or inaccessible!');
        }

        $size = filesize($file_path);
        if (empty($this->disposition)) {
            $this->disposition = $this->getBaseName($file_path);
        }

        if (empty($this->content_type)) {
            $this->content_type = $this->getContentType($file_path);
        }

        //turn off output buffering to decrease cpu usage
        $this->cleanAll();

        // required for IE, otherwise Content-Disposition may be ignored
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        header('Content-Type: ' . $this->content_type);
        if ($with_disposition) {
            header('Content-Disposition: attachment; filename="' . rawurlencode($this->disposition) . '"');
        }
        header('Accept-Ranges: bytes');

        // The three lines below basically make the
        // download non-cacheable 
        header("Cache-control: private");
        header('Pragma: private');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        // multipart-download and download resuming support
        if (isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(",", $range, 2);
            list($range, $range_end) = explode("-", $range);
            $range = intval($range);
            if (!$range_end) {
                $range_end = $size - 1;
            } else {
                $range_end = intval($range_end);
            }

            $new_length = $range_end - $range + 1;
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range-$range_end/$size");
        } else {
            $new_length = $size;
            header("Content-Length: " . $size);
        }

        // output the file itself 
        $chunksize = $this->bytes;
        $bytes_send = 0;

        $file = @fopen($file_path, 'rb');
        if ($file) {
            if (isset($_SERVER['HTTP_RANGE'])) {
                fseek($file, $range);
            }

            while (!feof($file) && (!connection_aborted()) && ($bytes_send < $new_length)) {
                $buffer = fread($file, $chunksize);
                echo ($buffer);
                flush();
                usleep($this->sec * 1000000);
                $bytes_send += strlen($buffer);
            }
            fclose($file);
        } else {
            throw new Exception('Error - can not open file.');
        }
    }

    /**
     * method for getting mime type of a file
     */
    private function getContentType(string $path)
    {
        $result = false;
        if (is_file($path) === true) {
            if (function_exists('finfo_open') === true) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if (is_resource($finfo) === true) {
                    $result = finfo_file($finfo, $path);
                }
                finfo_close($finfo);
            } else if (function_exists('mime_content_type') === true) {
                $result = preg_replace('~^(.+);.*$~', '$1', mime_content_type($path));
            } else if (function_exists('exif_imagetype') === true) {
                $result = image_type_to_mime_type(exif_imagetype($path));
            }
        }
        return $result;
    }

    /**
     * clean all buffers
     */
    private function cleanAll()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }
}
