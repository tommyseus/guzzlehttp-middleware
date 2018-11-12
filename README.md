# guzzlehttp-middleware

[![Latest Stable Version](https://poser.pugx.org/tommyseus/guzzlehttp-middleware/v/stable)](https://packagist.org/packages/tommyseus/guzzlehttp-middleware)
[![License](https://poser.pugx.org/tommyseus/guzzlehttp-middleware/license)](https://packagist.org/packages/tommyseus/guzzlehttp-middleware)
[![Build Status](https://travis-ci.org/tommyseus/guzzlehttp-middleware.svg?branch=master)](https://travis-ci.org/tommyseus/guzzlehttp-middleware)
[![Coverage Status](https://coveralls.io/repos/github/tommyseus/guzzlehttp-middleware/badge.svg?branch=master)](https://coveralls.io/github/tommyseus/guzzlehttp-middleware?branch=master)

The library provides several middleware classes for the guzzle http client.

## Installation

### Requirements

- PHP 7.1+

### Composer installation

```bash
$ composer require tommyseus/guzzlehttp-middleware
```

## Encoding Middleware

The encoding middleware converts the response to the expected encoding. It will modify the body and the content-type header of the response.
It's is possible to to add the encoding middleware to the handler stack or add it to the promise then method.

```php
$stack = \GuzzleHttp\HandlerStack::create();
$stack->push(\GuzzleHttp\Middleware::mapResponse(new \Seus\GuzzleHttp\Middleware\Encoding('UTF-8')));

return new \GuzzleHttp\Client([
    'handler' => $stack,
]);
```

```php
/* @var $client \GuzzleHttp\Client */

$promise = $client->requestAsync('GET', '......');
$promise->then(new \Seus\GuzzleHttp\Middleware\Encoding('UTF-8'));
$promise->then(
    function (\Psr\Http\Message\ResponseInterface $res) {
        echo $res->getStatusCode();
    }
);

$promise->wait();
```

## Run tests

```bash
$ docker-compose run guzzlehttp-middleware-php72 composer check

$ docker-compose run guzzlehttp-middleware-php71 composer check
```
