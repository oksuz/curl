# A Simple Php Curl Library

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oksuz/curl/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oksuz/curl/?branch=master)

## Installation

[Composer](http://getcomposer.org) is recommended for installation.

In one command line :
```
composer require oksuz/curl dev-master
```

Or via editting your `composer.json`
```json
{
    "require": {
        "oksuz/curl": "dev-master"
    }
}
```

```
composer update
```

## Examples

### Single Requests

#### GET
```php
$cli = new \Oksuz\Curl\Request("http://example.org");
/** @var \Oksuz\Curl\Response $result */
$result = $cli->result();

//echo $result->getHeader();
//echo $result->getStatusCode();
echo $result->getResponse();
```

#### POST

```php
$cli = new \Oksuz\Curl\Request("http://example.org")
/** @var \Oksuz\Curl\Response $result */
$result = $cli->post(array("username" => "foo", "password" => "bar"))
    ->addReferer("http://www.google.com/?q=example")
    ->setOpt(CURLOPT_USERAGENT, "firefox/2.0.16") // you can add php's CURL_CONSTANTS
    ->result();
```

Also available put and delete method

#### Runing Multiple Curl Requests
```php
$clients = array();
$clients[] = new \Oksuz\Curl\Request("http://example1.org");
$clients[] = new \Oksuz\Curl\Request("http://example2.org");
$clients[] = new \Oksuz\Curl\Request("http://example3.org");
$runner = new \Oksuz\Curl\Runner();
/** @var Array $result contains \Oksuz\Curl\Response */
$result = $runner->runMultiple($cli);
``` 

#### More Complex Example

```php
$curl = new \Oksuz\Curl\Request();
$response = $curl->url("http://www.example.org/login")
    ->post(array("username" => "user", "password" => "password"))
    ->addCookieSupport()
    //OR ->addCookieSupport("/path/to/cookiejar_cookiefile.txt")
    ->result();
    
if (200 == $response->getStatusCode()) {
    $resp = $curl->url("http://www.example.com/private_area")
    ->get(array("page" => 1, "do" => "show")) // http://www.example.com/private_area?page=1&do=show
    ->addHeader(array("HTTP_X_REQUESTED_WITH" => "XMLHttpRequest"))
    ->addCookie(array("cookie_name" => "cookie_value"))
    ->setBasicAuth("username", "password")
    ->result();
    
    var_dump($resp);
}

``` 

#### Default Values
```php
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_USERAGENT => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36",
CURLOPT_HEADER => true,
CURLOPT_TIMEOUT => 10,
CURLOPT_RETURNTRANSFER => true,
```