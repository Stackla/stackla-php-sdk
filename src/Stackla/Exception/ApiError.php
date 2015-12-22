<?php

namespace Stackla\Exception;

class ApiError
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * @param string $message
     * @param int $code
     * @param string $messageId
     * @param int $errorCode
     */
    public function __construct($message, $code, $messageId, $errorCode)
    {
        $this->message = $message;
        $this->code = $code;
        $this->messageId = $messageId;
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @deprecated Please use getErrorCode() instead
     * @see getErrorCode()
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

}
