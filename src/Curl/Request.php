<?php
namespace Oksuz\Curl;

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

    public function get(array $params = array())
    {
        $this->customRequest("GET");
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
                "?" . (http_build_query($queryArray));

            $this->url($newUrl);
            return $this;
        }

        if (!isset($parsedUrl["path"])) {
            $newUrl = $url . "/?" . http_build_query($params);
            $this->url($newUrl);
        } elseif (isset($parsedUrl["path"])) {
            $newUrl = $url . "?" . http_build_query($params);
            $this->url($newUrl);
        }

        return $this;
    }

    public function put($params)
    {
        $this->customRequest("PUT", $params);
        return $this;
    }

    public function post($params)
    {
        $this->customRequest("POST", $params);
        return $this;
    }

    public function delete($params = null)
    {
        $this->customRequest("DELETE", $params);
        return $this;
    }

    public function addCookieSupport($cookieFile = null)
    {

        if (null === $cookieFile) {
            $tempCookieFile = tempnam(sys_get_temp_dir(), "curl_request");
        } else {
            if (!is_file($cookieFile) || is_writable($cookieFile)) {
                throw new \ErrorException("{$cookieFile} not exist or isn't writable");
            }
            $tempCookieFile = $cookieFile;
        }

        $this->setOpt(CURLOPT_COOKIEFILE, $tempCookieFile);
        $this->setOpt(CURLOPT_COOKIEJAR, $tempCookieFile);
        return $this;
    }

    public function addReferer($referer)
    {
        $this->setOpt(CURLOPT_REFERER, $referer);
        return $this;
    }

    public function setBasicAuth($username, $password)
    {
        $this->setOpt(CURLOPT_USERPWD, sprintf("%s:%s", $username, $password));
        return $this;
    }

    public function addCookie(array $cookies)
    {
        $cookie = array();
        array_walk($cookies, function($el, $k) use (&$cookie){
            $cookie[] = sprintf("%s=%s", $k, $el);
        });
        $this->addCookieSupport();
        $cookie = implode(";", $cookie);
        $this->setOpt(CURLOPT_COOKIE, $cookie);
    }

    public function addHeader(array $headers)
    {
        $header = array();
        array_walk($headers, function($el, $k) use (&$header){
            $header[] = sprintf("%s: %s", $k, $el);
        });
        $this->setOpt(CURLOPT_HTTPHEADER, $header);
    }

    /**
     * @return Response
     */
    public function result()
    {
        $runner = new Runner();
        return $runner->runSingle($this);
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

    private function customRequest($requestType, array $params = array())
    {
        if (!empty($params)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $params);
        }
        $this->setOpt(CURLOPT_CUSTOMREQUEST, $requestType);

    }
}
