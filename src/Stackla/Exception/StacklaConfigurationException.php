<?php

namespace Stackla\Exception;

/**
 * Class StacklaConfigurationException
 *
 * @package Stackla\Exception
 */
class StacklaConfigurationException extends \Exception
{

    /**
     * Default Constructor
     *
     * @param string|null $message
     * @param int $code
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
