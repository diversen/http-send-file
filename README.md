# HTTP send file

Sends a file with support for (multiple) range requests. 
It is able to throttle the download and adds an etag to the request.
It is quite small and simple.

# Install

With composer add to your "require" section: 

    composer require diversen/http-send-file

Usage example: 

~~~php

use Diversen\Sendfile;
$s = new Sendfile();
        
// if you don't set type - we will try to guess it
$s->setContentType('application/epub+zip');
        
// if you don't set disposition (file name user agent will see)
// we will make a file name from file
$s->setContentDisposition('test.epub');

// Expires header. Default is a date in the past
$s->setExpires(3600);
        
// chunks of 40960 bytes per 0.1 secs
// if you don't set this then the values below are the defaults
// approx 40960 bytes per sec
$s->throttle(0.1, 40960);

// file
$file = '/some/dir/test.epub';

// send the file
try {
    $s->send($file);
} catch (\Exception $e) {
    echo $e->getMessage();
}

~~~

So you could just do like this: 

~~~php

use Diversen\Sendfile;
$s = new Sendfile();

// file
$file = '/some/dir/test.epub';

// send the file
try {
    $s->send($file);
} catch (\Exception $e) {
    echo $e->getMessage();
}

// but check the headers if it is not
// working as expected as the guessing
// of content-type does not always work
// correctly. 

~~~

Without sending content-disposition header: 

~~~php

// without sending content-disposition header
// 2. param = false
try {
    $s->send($file, false);
} catch (\Exception $e) {
    echo $e->getMessage();
}

~~~

# Test notes

Build image:

    docker build -t php-apache2 .

Run the docker image:

    docker run -d -p 8080:80 -v $(pwd):/var/www/html --name test-send-file php-apache2

Enable autoloading: 

    composer install

Go to the browser at: http://localhost:8080/test

Or use curl, e.g.: 

    curl -v -L -O -C - http://localhost:8080/test/send_large_file.php

# Credits 

Much of the code is taken (and rewritten) from here: 

<http://w-shadow.com/blog/2007/08/12/how-to-force-file-download-with-php/>

The process is nicely explained here: 

<http://www.media-division.com/the-right-way-to-handle-file-downloads-in-php/>

MIT © [Dennis Iversen](https://github.com/diversen)
