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
$cli = new \Curl\Request("http://example.org");
/** @var \Curl\Response $result */
$result = $cli->result();

//echo $result->getHeader();
//echo $result->getStatusCode();
echo $result->getResponse();
```

#### POST

```php
$cli = new \Curl\Request("http://example.org")
/** @var \Curl\Response $result */
$result = $cli->post(array("username" => "foo", "password" => "bar"))
    ->addReferer("http://www.google.com/?q=example")
    ->setOpt(CURLOPT_USERAGENT, "firefox/2.0.16")
    ->result();
```

Also available put and delete method

#### Runing Multiple Curl Requests
```php
$clients = array();
$clients[] = new \Curl\Request("http://example1.org");
$clients[] = new \Curl\Request("http://example2.org");
$clients[] = new \Curl\Request("http://example3.org");
$runner = new \Curl\Runner();
/** @var Array $result contains \Curl\Response */
$result = $runner->runMultiple($cli);
``

#### More Complex Example

```php
$curl = new \Curl\Request();
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