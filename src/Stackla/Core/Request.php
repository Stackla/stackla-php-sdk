<?php

namespace Stackla\Core;

use Guzzle\Http\EntityBodyInterface;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Stackla\Exception\ApiException;

class Request implements RequestInterface
{
    /**
     * Stackla domain name
     * @var string
     */
    protected $host;

    /**
     * Stackla stack name
     * @var string
     */
    protected $stack;

    /**
     * Stackla credentials
     * @var \Stackla\Core\Credentials
     */
    protected $credentials;

    /**
     * Response result placeholder
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;

    /**
     * Request placeholder
     * @var \Guzzle\Http\Message\Request
     */
    protected $request;

    /**
     * Log
     * @var \Monolog\Logger
     */
    protected $logger = null;

    private $querySeparator = '&';

    protected $apiKey;

    /**
     *
     * @var \Guzzle\Http\Message\Response
     */
    private $client;

    public function __construct(\Stackla\Core\Credentials $credentials, $host, $stack)
    {
        $this->host = $host;
        $this->stack = $stack;
        $this->credentials = $credentials;
        $this->client = new Client();
        if (class_exists("\\Monolog\\Logger")) {
            $this->logger = new \Monolog\Logger(get_class($this));
            $this->logger->pushHandler(new \Monolog\Handler\StreamHandler(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stackla-request.log', \Monolog\Logger::INFO));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setStack($stack)
    {
        $this->stack = $stack;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * Build a complete URI request by concanating the host name,
     * endpoint, API key and id
     *
     * @param string    $endpoint   Endpoint of API call
     *                              example: - /filters
     *                                       - /filters/[FILTER_ID]/content
     *                                       - /tags
     * @param array     $query      Query of data
     *
     * @return string   Complete URI
     */
    private function buildUri($endpoint, array $query = array())
    {
        if ($this->credentials->type === Credentials::TYPE_OAUTH2) {
            $query = array_merge(array('access_token' => $this->credentials->getToken(), 'stack' => $this->stack), $query);
        } else {
            $query = array_merge(array('api_key' => $this->credentials->getToken(), 'stack' => $this->stack), $query);
        }

        // prevent http_build_query ignore empty value
        $query = $this->preventValueBeenIgnore($query);

        return sprintf("%s/%s%s%s", rtrim($this->host, '/'), trim($endpoint, '/'), strpos($endpoint, '?') === false ? '?' : '&', http_build_query($query, '', $this->querySeparator));
    }

    public function preventValueBeenIgnore($data = array())
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $data[$key] = '';
            } elseif (gettype($value) == 'array') {
                $data[$key] = $this->preventValueBeenIgnore($value);
            }
        }
        return $data;
    }

    /**
     * Build Guzzle request option with $data as the body
     *
     * @param array     $data   Array of body data
     *
     * @return array    Request options
     */
    private function buildOptions(array $data = array())
    {
        $options = array();
        $options['body'] = $data;
        return $options;
    }

    /**
     * Making request using Guzzle
     *
     * @param string $method   Type of request, either POST, GET, PUT or DELETE
     * @param string $endpoint The operation / task for API
     * @param array  $data     The parameter need to be passed
     * @param array  $options  The options like header, body, etc
     * @return EntityBodyInterface|string
     * @throws \Exception
     */
    private function sendRequest($method, $endpoint, array $data = array(), array $options = array())
    {
        $uri = $this->buildUri($endpoint);
        if ($method === "GET" ||$method === "PUT") {
            $uri = $this->buildUri($endpoint, $data);
        }

        $body = null;
        if (isset($options['body']) && !empty($options['body'])) {
            $body = $options['body'];
        }
        unset($options['body']);

        try {
            try {
                switch ($method) {
                    case 'POST':
                        $this->request = $this->client->post($uri, $options, $data);
                        break;
                    case 'PUT':
                        $this->request = $this->client->put($uri, $options, $data);
                        break;
                    case 'DELETE':
                        $this->request = $this->client->delete($uri, $options, $data);
                        break;
                    case 'GET':
                        $this->request = $this->client->get($uri);
                        break;
                }
                if ($body) {
                    $this->request->setBody($body);
                }
                $this->response = $this->request->send();
            } catch (ClientErrorResponseException $e) {
                $this->request = $e->getRequest();
                $this->response = $this->request->getResponse();
            }
        } catch (BadResponseException $e) {
            $this->request = $e->getRequest();
            $this->response = $this->request->getResponse();
        }

        if ($this->response->getStatusCode() >= 400) {
            $bt = debug_backtrace();
            $caller = $bt[2];
            if (isset($caller['class']) && $caller['class'] === get_class(new \Stackla\Core\StacklaModel())) {
                $json = $this->response->getBody(true);
                if (\Stackla\Validation\JsonValidator::validate($json, true)) {
                    $content = json_decode($json, true);
                    if (isset($content['errors'])){
                        $caller['object']->fromArray($content);
                    }
                }
            }
            if ($this->logger) {
                $this->logger->addError('-> REQUEST [' . $this->request->getMethod() . ' - ' . $this->request->getUrl() . "]", array($this->request->getMethod() !== "GET" ? $this->request->getPostFields() : ""));
                $this->logger->addError('<- RESPONSE [' . $this->response->getStatusCode() . ':' . $this->response->getReasonPhrase() . "]", array($this->response->getBody(true)));
            }
        } else {
            if ($this->logger) {
                $this->logger->addInfo('-> REQUEST [' . $this->request->getMethod() . ' - ' . $this->request->getUrl() . "]", array($this->request->getMethod() !== "GET" ? $this->request->getPostFields() : ""));
                $this->logger->addInfo('<- RESPONSE [' . $this->response->getStatusCode() . ':' . $this->response->getReasonPhrase() . "]", array($this->response->json()));
            }
        }

        $statusCode = $this->response->getStatusCode();
        switch ($statusCode) {
            case 200:
                return $this->response->getBody(true);
            case 400:
                throw ApiException::create(
                    sprintf("Server return %s error code. Bad request: The request could not be understood. %s", $this->response->getStatusCode(), $this->response->getBody(true)),
                    $statusCode,
                    $this->response->getBody(true)
                );
            case 401:
                throw ApiException::create(
                    sprintf("Server return %s error code. Unauthorized: Authentication credentials invalid or not authorised to access resource", $this->response->getStatusCode()),
                    $statusCode,
                    $this->response->getBody(true)
                );
            case 403:
                throw ApiException::create(
                    sprintf("Server return %s error code. Rate limit exceeded: Too many requests in the current time window", $this->response->getStatusCode()),
                    $statusCode,
                    $this->response->getBody(true)
                );
            case 404:
                throw ApiException::create(
                    sprintf("Server return %s error code. Invalid resource: Invalid resource specified or resource not found", $this->response->getStatusCode()),
                    $statusCode,
                    $this->response->getBody(true)
                );
            default:
                throw ApiException::create(
                    sprintf("Server return %s error code.Server error: An error on the server prohibited a successful response; please contact support. %s", $this->response->getStatusCode(), $this->response->getBody(true)),
                    $statusCode,
                    $this->response->getBody(true)
                );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function sendGet($endpoint, array $data = array(), array $options = array())
    {
        return $this->sendRequest('GET', $endpoint, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function sendPost($endpoint, array $data = array(), array $options = array())
    {
        return $this->sendRequest('POST', $endpoint, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function sendPut($endpoint, array $data = array(), array $options = array())
    {
        return $this->sendRequest('PUT', $endpoint, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function sendDelete($endpoint, array $data = array(), array $options = array())
    {
        return $this->sendRequest('DELETE', $endpoint, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function status()
    {
        if ($this->response) {
            return $this->response->getStatusCode();
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }
}
