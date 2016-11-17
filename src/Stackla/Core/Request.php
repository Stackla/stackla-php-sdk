<?php

namespace Stackla\Core;

use GuzzleHttp\Client;
use GuzzleHttp\EntityBodyInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientErrorResponseException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Stackla\Exception\ApiException;
use Stackla\Validation\JsonValidator;

class Request implements RequestInterface
{
    /**
     * Stackla domain name
     *
     * @var string
     */
    protected $host;

    /**
     * Stackla stack name
     *
     * @var string
     */
    protected $stack;

    /**
     * Stackla credentials
     *
     * @var Credentials
     */
    protected $credentials;

    /**
     * Response result placeholder
     *
     * @var \GuzzleHttp\Message\Response
     */
    protected $response;

    /**
     * Request placeholder
     *
     * @var \GuzzleHttp\Message\Request
     */
    protected $request;

    /**
     * Log
     *
     * @var Logger
     */
    protected $logger = null;

    private $querySeparator = '&';

    protected $apiKey;

    /**
     * @var \GuzzleHttp\Message\Response
     */
    private $client;

    public function __construct(Credentials $credentials, $host, $stack)
    {
        $this->host = $host;
        $this->stack = $stack;
        $this->credentials = $credentials;
        // We prevent exception for being catched by the guzzle client
        $this->client = new Client(['defaults' => ['exceptions' => false]]);
        if (class_exists("\\Monolog\\Logger")) {
            $this->logger = new Logger(get_class($this));
            $this->logger->pushHandler(new StreamHandler(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stackla-request.log', Logger::INFO));
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
     * @param string $endpoint Endpoint of API call
     *                              example: - /filters
     *                                       - /filters/[FILTER_ID]/content
     *                                       - /tags
     * @param array $query Query of data
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

        return sprintf(
            "%s/%s%s%s",
            rtrim($this->host, '/'),
            trim($endpoint, '/'),
            strpos($endpoint, '?') === false ? '?' : '&',
            http_build_query($query, '', $this->querySeparator)
        );
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
     * Making request using Guzzle
     *
     * @param string $method Type of request, either POST, GET, PUT or DELETE
     * @param string $endpoint The operation / task for API
     * @param array $data The parameter need to be passed
     * @param array $options The options like header, body, etc
     *
     * @return EntityBodyInterface|string
     * @throws \Exception
     */
    private function sendRequest($method, $endpoint, array $data = array(), array $options = array())
    {
        $uri = $this->buildUri($endpoint);
        if ($method === "GET" || $method === "PUT") {
            $uri = $this->buildUri($endpoint, $data);
        } elseif (count($data)) {
            $options['body'] = $data;
        }

        $this->request = $this->client->createRequest($method, $uri, $options);
        $this->response = $this->client->send($this->request);

        if ($this->response->getStatusCode() >= 400) {
            $bt = debug_backtrace();
            $caller = $bt[2];
            if (isset($caller['class']) && $caller['class'] === get_class(new StacklaModel())) {
                $json = (string)$this->response->getBody();
                if (JsonValidator::validate($json, true)) {
                    $content = json_decode($json, true);
                    if (isset($content['errors'])) {
                        $caller['object']->fromArray($content);
                    }
                }
            }
            if ($this->logger) {
                $this->logger->addError(
                    '-> REQUEST [' . $this->request->getMethod() . ' - ' . $this->request->getUrl() . "]",
                    array($this->request->getMethod() !== "GET" ? (string)$this->request->getBody() : "")
                );
                $this->logger->addError(
                    '<- RESPONSE [' . $this->response->getStatusCode() . ':' . $this->response->getReasonPhrase() . "]",
                    array((string)$this->response->getBody())
                );
            }
        } else {
            if ($this->logger) {
                $this->logger->addInfo(
                    '-> REQUEST [' . $this->request->getMethod() . ' - ' . $this->request->getUrl() . "]",
                    array($this->request->getMethod() !== "GET" ? (string)$this->request->getBody() : "")
                );
                $this->logger->addInfo(
                    '<- RESPONSE [' . $this->response->getStatusCode() . ':' . $this->response->getReasonPhrase() . "]",
                    array($this->response->json())
                );
            }
        }

        $statusCode = $this->response->getStatusCode();
        switch ($statusCode) {
            case 200:
                return (string)$this->response->getBody();
            case 400:
                throw ApiException::create(
                    sprintf(
                        "Server return %s error code. Bad request: The request could not be understood. %s",
                        $this->response->getStatusCode(),
                        (string)$this->response->getBody()
                    ),
                    $statusCode,
                    (string)$this->response->getBody()
                );
            case 401:
                throw ApiException::create(
                    sprintf(
                        "Server return %s error code. Unauthorized: Authentication credentials invalid or not authorised to access resource",
                        $this->response->getStatusCode()
                    ),
                    $statusCode,
                    (string)$this->response->getBody()
                );
            case 403:
                throw ApiException::create(
                    sprintf(
                        "
                  Server return %s error code. Rate limit exceeded: Too many requests in the current time window",
                        $this->response->getStatusCode()
                    ),
                    $statusCode,
                    (string)$this->response->getBody()
                );
            case 404:
                throw ApiException::create(
                    sprintf(
                        "Server return %s error code. Invalid resource: Invalid resource specified or resource not found",
                        $this->response->getStatusCode()
                    ),
                    $statusCode,
                    (string)$this->response->getBody()
                );
            default:
                throw ApiException::create(
                    sprintf(
                        "Server return %s error code.Server error: An error on the server prohibited a successful response; please contact support. %s",
                        $this->response->getStatusCode(),
                        (string)$this->response->getBody()
                    ),
                    $statusCode,
                    (string)$this->response->getBody()
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
