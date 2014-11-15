<?php
namespace Curl;

class Request
{
    private $curl;

    public function __construct($url = null)
    {
        if (!empty($url)) {
            $this->validateUrl($url);
        }
        
        $this->curl = curl_init($url);
        $this->prepare();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function url($url)
    {
        $this->validateUrl($url);
        $this->setOpt(CURLOPT_URL, $url);
        return $this;
    }

    public function get(Array $params = array())
    {
        $this->setOpt(CURLOPT_POST, false);
        if (empty($params)) {
            return $this;
        }

        $url = $this->getLastUrl();
        if (empty($url)) {
            throw new \UnexpectedValueException("Unknown url for parameter concat");
        }

        $parsedUrl = parse_url($url);
        if (!empty($parsedUrl["query"])) {
            $queryArray = array();
            parse_str($parsedUrl["query"], $queryArray);
            array_map(function($el) use(&$queryArray, $params){
                $queryArray[$el] = $params[$el];
            }, array_keys($params));

            $newUrl = $parsedUrl["scheme"] . "://" .
                $parsedUrl["host"] .
                ((isset($parsedUrl["path"])) ? $parsedUrl["path"] : "/") .
                "?" . http_build_query($queryArray);

            $this->url($newUrl);
            return $this;
        }

        if (!isset($parsedUrl["path"])) {
            $newUrl = $url . "/?" . http_build_query($params);
            $this->url($newUrl);
        } elseif (isset($parsedUrl["path"]) && "/" == $parsedUrl["path"]) {
            $newUrl = $url . "?" . http_build_query($params);
            $this->url($newUrl);
        }

        return $this;
    }

    /**
     * @return \Curl\Response
     */
    public function result()
    {
        $runner = new Runner();
        return $runner->runSingle($this);
    }

    public function post($params)
    {
        $params = (is_array($params)) ? $params : json_encode($params);
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $params);
        return $this;
    }

    public function addCookieSupport()
    {
        //@FIXME: find and use system temp directory
        //@FIXME: cookiefile naming
        $this->setOpt(CURLOPT_COOKIEFILE, "/tmp/curl_request_testcookie.dat");
        $this->setOpt(CURLOPT_COOKIEJAR, "/tmp/curl_request_testcookie.dat");
        return $this;
    }

    public function addReferer($referer)
    {
        $this->setOpt(CURLOPT_REFERER, $referer);
        return $this;
    }

    public function addCookie(Array $cookies)
    {
        $cookie = array();
        array_walk($cookies, function($el, $k) use (&$cookie){
            $cookie[] = sprintf("%s=%s", $k, $el);
        });
        $this->addCookieSupport();
        $cookie = implode(";", $cookie);
        $this->setOpt(CURLOPT_COOKIE, $cookie);
    }

    public function addHeader(Array $headers)
    {
        $header = array();
        array_walk($headers, function($el, $k) use (&$header){
            $h[] = sprintf("%s: %s", $k, $el);
        });
        $this->setOpt(CURLOPT_HTTPHEADER, $header);
    }


    public function getLastUrl()
    {
        return curl_getinfo($this->getCurl(), CURLINFO_EFFECTIVE_URL);
    }

    public function getStatusCode()
    {
        return curl_getinfo($this->getCurl(), CURLINFO_HTTP_CODE);
    }

    public function getHeaderSize()
    {
        return curl_getinfo($this->getCurl(), CURLINFO_HEADER_SIZE);
    }

    public function setOpt($opt, $value)
    {
        curl_setopt($this->getCurl(), $opt, $value);
        return $this;
    }

    public function getCurl()
    {
        return $this->curl;
    }

    public function close()
    {
        $curl = $this->getCurl();
        if (is_resource($curl)) {
            curl_close($curl);
        }
    }

    private function validateUrl($url)
    {
        if (!preg_match("@^(http|https)://.+$@", $url)) {
            throw new \UnexpectedValueException("Unknown url format");
        }
    }

    private function prepare()
    {
        $ch = $this->getCurl();
        curl_setopt_array($ch, array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36",
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
        ));
    }
}