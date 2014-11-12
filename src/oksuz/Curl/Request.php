<?php
namespace Curl;

class Request
{
    private $curl;

    public function __construct($url)
    {
        $this->curl = curl_init($url);
        $this->prepare();
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

    public function getLastUrl()
    {
        $ch = $this->getCurl();
        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }

    public function getStatusCode()
    {
        $ch = $this->getCurl();
        return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

    public function getHeaderSize()
    {
        $ch = $this->getCurl();
        return curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    }

    public function &getCurl()
    {
        return $this->curl;
    }

    /**
     * @param $key Request constant
     * @param $value
     */
    public function addOption($key, $value)
    {
        $ch = $this->getCurl();
        curl_setopt($ch, $key, $value);
    }
}