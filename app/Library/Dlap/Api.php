<?php
/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 9/7/15
 * Time: 5:34 PM
 */

namespace App\Library\Dlap;

class Api
{
    private $uri = "http://dlap.agilix.com/dlap.ashx";

    /**
     * HTTP GET cURL Call
     * Takes a string command and appends it to the DLAP API uri and sends transmission
     *
     * @param string $command
     * @return stdClass mixed
     */
    public function get($command)
    {
        return $this->transmit('get', $command);
    }

    /**
     * HTTP POST cURL Call
     * Takes a string JSON payload and sends transmission
     *
     * @param array $payload
     * @return stdClass mixed
     */
    public function post($payload)
    {
        return $this->transmit('post', null, json_encode($payload));
    }

    /**
     * Transmission Private Method
     * All cURL-based prior HTTP Method calls use this singular method to transmit calls
     *
     * @param string $method
     * @param string|null $command
     * @param string|null $payload
     * @return stdClass mixed
     */
    private function transmit($method, $command, $payload = null)
    {
        $ch = curl_init();

        $opt = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json; charset=utf-8'
            )
        );

        switch ($method) {
            case 'get':
                $opt[CURLOPT_HTTPGET] = true;
                $opt[CURLOPT_URL] = "{$this->uri}?$command";
                break;
            case 'post':
                $opt[CURLOPT_POST] = true;
                $opt[CURLOPT_URL] = $this->uri;
                $opt[CURLOPT_POSTFIELDS] = $payload;
                array_push($opt[CURLOPT_HTTPHEADER],"Content-Length: " . strlen($payload));
                break;
        }

        curl_setopt_array($ch, $opt);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result);
    }
}