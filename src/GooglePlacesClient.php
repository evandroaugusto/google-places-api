<?php

namespace evandroaugust\GooglePlaces;


class GooglePlacesClient
{
    protected $proxy;
    protected $ssl_verify_peer;


    public function __construct($proxy = false, $ssl = false)
    {
        $this->proxy = $proxy;
        $this->ssl_verify_peer = $ssl;
    }


    /**
     * curl call to a specified url
     *
     * @param  string $url 	url to curl to
     * @return string
     */
    public function get($url)
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verify_peer,
            CURLOPT_RETURNTRANSFER => true,
        );

        curl_setopt_array($curl, $options);

        if ($this->proxy) {
            $this->setCurlProxy($curl);
        }

        $response = curl_exec($curl);

        if ($error = curl_error($curl)) {
            throw new \Exception('CURL Error: ' . $error);
        }

        curl_close($curl);
        return $response;
    }


    /**
     * Adds proxy to cUrl
     * @param $ch
     */
    protected function setCurlProxy($curl)
    {
        $proxy_url = $this->proxy['host'];

        if (isset($this->proxy['port'])) {
            $proxy_url .= ':' . $this->proxy['port'];
        }

        curl_setopt($curl, CURLOPT_PROXY, $proxy_url);

        if (isset($this->proxy['username']) && isset($this->proxy['password'])) {
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->proxy['username'] . ':' . $this->proxy['password']);
        }
    }
}
