<?php

namespace Stackla\Core;

/**
 * Interfave for Stackla API
 */
interface RequestInterface
{
    /**
     * Host setter
     *
     * @param string $host API host name
     */
    public function setHost($host);

    /**
     * API key setter
     *
     * @param string $apiKey Client API key
     */
    public function setApiKey($apiKey);

    /**
     * Stack setter
     *
     * @param string $stack Client stack name
     */
    public function setStack($stack);

    /**
     * This method will do GET request to Stackla API
     *
     * @param string $endpoint The operation / task for API
     * @param array $data The parameter need to be passed
     *
     * @return \GuzzleHttp\Message\Response     This will return FALSE if something wrong happened
     *                                          or return json object
     */
    public function sendGet($endpoint, array $data = array());

    /**
     * This method will do POST request to Stackla API
     *
     * @param string $endpoint The operation / task for API
     * @param array $data The parameter need to be passed
     *
     * @return \GuzzleHttp\Message\Response     This will return FALSE if something wrong happened
     *                                          or return json object
     */
    public function sendPost($endpoint, array $data = array());

    /**
     * This method will do PUT request to Stackla API
     *
     * @param string $endpoint The operation / task for API
     * @param array $data The parameter need to be passed
     *
     * @return \GuzzleHttp\Message\Response     This will return FALSE if something wrong happened
     *                                          or return json object
     */
    public function sendPut($endpoint, array $data = array());

    /**
     * This method will do DELETE request to Stackla API
     *
     * @param string $endpoint The operation / task for API
     * @param array $data The parameter need to be passed
     *
     * @return \GuzzleHttp\Message\Response     This will return FALSE if something wrong happened
     *                                          or return json object
     */
    public function sendDelete($endpoint, array $data = array());

    /**
     * This method will return the request HTTP status
     */
    public function status();

    /**
     * This method will return the request object
     * 
     * @return \GuzzleHttp\Message\Request
     */
    public function getRequest();

    /**
     * This method will return the response object
     * 
     * @return \GuzzleHttp\Message\Response
     */
    public function getResponse();
}
