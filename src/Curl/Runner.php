<?php
namespace Oksuz\Curl;

class Runner
{
    public function runSingle(Request $c)
    {
        return $this->getCurlResponse($c, curl_exec($c->getCurl()));
    }

    public function runMultiple(Array $curl)
    {
        $multiInit = curl_multi_init();
        $chs = array();
        /** @var Request $curlInstance */
        foreach ($curl as $curlInstance)
        {
            if ($curlInstance instanceof Request) {
                $ch = $curlInstance->getCurl();
                $chs[] = $curlInstance;
                curl_multi_add_handle($multiInit, $ch);
            }
        }

        $running = null;
        do {
            curl_multi_exec($multiInit, $running);
        } while ($running > 0);

        $retVal = array();
        /** @var Request $c */
        foreach ($chs as $url => &$c) {
            $resp = curl_multi_getcontent($c->getCurl());
            $retVal[] = $this->getCurlResponse($c, $resp);
        }

        return $retVal;
    }

    protected function getCurlResponse(Request $curl, $source)
    {
        $cresp = new Response();
        $header = substr($source, 0, $curl->getHeaderSize());
        $source = substr($source, $curl->getHeaderSize());

        $cresp->setHeader($header)
            ->setUrl($curl->getLastUrl())
            ->setHeaderSize($curl->getHeaderSize())
            ->setResponse($source)
            ->setStatusCode($curl->getStatusCode());
        return $cresp;
    }
}