<?php
namespace Oksuz\Curl;

class Response
{
    private
        $statusCode,
        $response,
        $header,
        $headerSize,
        $url;

    public function getHeader()
    {
        return $this->header;
    }

    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    public function getHeaderSize()
    {
        return $this->headerSize;
    }

    public function setHeaderSize($headerSize)
    {
        $this->headerSize = $headerSize;
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}