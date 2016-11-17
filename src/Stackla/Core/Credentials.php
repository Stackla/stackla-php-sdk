<?php

namespace Stackla\Core;

/**
 * Credentials
 *
 * @package Stackla\Core
 *
 * @property    string $host
 * @property    string $stack
 * @property    string $key
 */
class Credentials
{
    /**
     * Credentials Type
     */
    const TYPE_OAUTH2 = 'oauth2';
    const TYPE_APIKEY = 'app_key';

    /**
     * Stackla domain name
     * @var string
     */
    public $host;

    /**
     * Stackla stack name
     * @var string
     */
    public $stack;

    /**
     * Authentication type
     * @var string
     */
    public $type;

    /**
     * OAuth2 Access token / normal API key
     * @var string
     */
    public $token;

    /**
     * OAuth2 refresh token
     * @var string
     */
    public $refreshToken;

    /**
     * OAuth2 type of token
     * @var string
     */
    public $tokenType;

    /**
     * OAuth2 access token expiring time
     * @var int
     */
    public $expiredIn;

    /**
     * OAuth2 state
     */
    private $state = "stackla-php-sdk";
    private $apiKey;

    /**
     * Constructor
     *
     * @param string $host API Host
     * @param string $token API OAuth2 access token or normal Stackla API
     *                          key
     * @param string $stack API stack
     * @param string $type Authentication type, either TYPE_OAUTH2 or TYPE_APIKEY
     */
    public function __construct($host = '', $token = '', $stack = '', $type = Credentials::TYPE_OAUTH2)
    {
        $this->host = $host;
        $this->token = $token;
        $this->stack = $stack;
        $this->type = $type;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getKey()
    {
        return $this->apiKey;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getStack()
    {
        return $this->stack;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setStack($stack)
    {
        $this->stack = $stack;
    }

    /**
     * Generate access_token
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_code
     * @param string $redirect_uri
     *
     * @return $this
     * @throws \Exception
     */
    public function generateToken($client_id, $client_secret, $access_code, $redirect_uri)
    {
        $endpoint = "oauth2/token";

        $request = new Request($this, $this->host, $this->stack);

        $data = array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'authorization_code',
            'code' => $access_code,
            'redirect_uri' => $redirect_uri
        );

        $json = $request->sendPost($endpoint, $data);
        $response = json_decode($json, true);

        if (isset($response['error']) && count($response['error'])) {
            throw new \Exception("Error occurred: " .
                $response['error_description']);
        }

        $this->token = $response['access_token'];
        $this->refreshToken = $response['refresh_token'];
        $this->tokenType = $response['token_type'];
        $this->expiredIn = $response['expires_in'];
        return $this;
    }

    /**
     * Generate access uri
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $redirect_uri
     *
     * @return string
     */
    public function getAccessUri($client_id, $client_secret, $redirect_uri)
    {
        $endpoint = "oauth2/authenticate";

        $query = array(
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'state' => $this->state
        );

        return sprintf("%s%s?%s", $this->host, $endpoint, http_build_query($query));
    }
}
