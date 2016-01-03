# file_get_contents HTTP client

[![Build Status](https://secure.travis-ci.org/lanthaler/fgc-client.png?branch=master)](http://travis-ci.org/lanthaler/fgc-client)
[![Code Coverage](https://scrutinizer-ci.com/g/lanthaler/fgc-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lanthaler/fgc-client/?branch=master)

This is a HTTPlug-conformant HTTP client based on `file_get_contents`.


## Installation

The easiest way to use `fgc-client` is to integrate it as a dependency in
your project's [composer.json](http://getcomposer.org/doc/00-intro.md) file:

    php composer.phar require ml/fgc-client ^1.0

Please note that HTTPlug hasn't released a stable version yet. Thus, for the
time being, you need to set the minimum stability in your `composer.json`
file to `beta`:

```
{
    ...
    "minimum-stability": "beta",
    "require": {
        ...
}
```

## Usage

After having installed the desired dependencies

    php composer.phar require guzzlehttp/psr7 ^1.0 php-http/message ^0.2

issuing an HTTP request is quite straightforward:

```php
require_once('vendor/autoload.php');

use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use ML\FgcClient\FgcHttpClient;

$messageFactory = new GuzzleMessageFactory();
$client = new FgcHttpClient($messageFactory, new GuzzleStreamFactory());

$request = $messageFactory->createRequest('GET', 'http://example.com/');
$response = $client->sendRequest($request);

echo $response->getStatusCode();
```
