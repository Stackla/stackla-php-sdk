<?php

namespace Stackla\Exception;

use Exception;

class ApiException extends Exception
{

    /**
     * @var int
     */
    protected $httpStatusCode;

    /**
     * @var string
     */
    protected $httpResponseBody;

    /**
     * @var ApiError[]
     */
    protected $_errors;

    public static function create($message, $httpStatusCode, $httpResponseBody, Exception $previous = null)
    {
        $exception = new static($message, 0, $previous);
        $exception->httpStatusCode = $httpStatusCode;
        $exception->httpResponseBody = $httpResponseBody;
        return $exception;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @return string
     */
    public function getHttpResponseBody()
    {
        return $this->httpResponseBody;
    }

    /**
     * @return ApiError[]
     */
    public function getErrors()
    {
        if (!is_array($this->_errors)) {
            $errors = array();
            $json = $this->getHttpResponseBody();
            $response = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $errors = $this->ifx($response, 'errors');
                if (is_array($errors)) {
                    foreach ($errors as $error) {
                        if (is_array($error)) {
                            $errors[] = new ApiError(
                                (string)$this->ifx($error, 'message'),
                                (string)$this->ifx($error, 'code'),
                                (string)$this->ifx($error, 'message_id'),
                                (int)$this->ifx($error, 'error_code')
                            );
                        }
                    }
                }
            }
            $this->_errors = $errors;
        }
        return $this->_errors;
    }

    /**
     * @param int $errorCode
     * @return bool
     */
    public function containsErrorByErrorCode($errorCode)
    {
        $errors = $this->getErrors();
        foreach ($errors as $error) {
            if ($error->getErrorCode() === $errorCode) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $messageId
     * @return bool
     */
    public function containsErrorByMessageId($messageId)
    {
        $errors = $this->getErrors();
        foreach ($errors as $error) {
            if ($error->getMessageId() === $messageId) {
                return true;
            }
        }
        return false;
    }

    final protected function ifx($arr, $key, $default = null)
    {
        if (!is_array($arr)) {
            return $default;
        } elseif (array_key_exists($key, $arr)) {
            return $arr[$key];
        } else {
            return $default;
        }
    }
}
