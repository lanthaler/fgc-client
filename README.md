# file_get_contents HTTP client

[![Build Status](https://secure.travis-ci.org/lanthaler/fgc-client.png?branch=master)](http://travis-ci.org/lanthaler/fgc-client)

This is a HTTPlug-conformant HTTP client based on `file_get_contents`.


## Installation

The easiest way to use `fgc-client` is to integrate it as a dependency in your project's
[composer.json](http://getcomposer.org/doc/00-intro.md) file:

```json
{
    "require": {
        "ml/fgc-client": "^1.0"
    }
}
```

Installing is then a matter of running composer

    php composer.phar install

... and including Composer's autoloader to your project

```php
require('vendor/autoload.php');
```

## Usage

### Using [php-http/utils](https://packagist.org/packages/php-http/utils):

```php
use Http\Client\Utils\MessageFactory\GuzzleMessageFactory;
use Http\Client\Utils\StreamFactory\GuzzleStreamFactory;
use ML\FgcClient\FgcHttpClient;

$messageFactory = new GuzzleMessageFactory();
$client = new FgcHttpClient($messageFactory, new GuzzleStreamFactory());

$request = $messageFactory->createRequest('GET', 'http://example.com/');
$response = $client->sendRequest($request);
```
