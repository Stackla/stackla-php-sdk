<?php

namespace Stackla\Api;

use Stackla\Core\Credentials;

class Stack
{
    /**
     * Config
     *
     * @var array
     */
    protected $configs;

    /**
     * Instantiated Stackla object
     *
     * @param Credentials $credentials
     * @param string $host
     * @param string $stack
     */
    public function __construct(Credentials $credentials, $host, $stack)
    {
        $this->configs = array(
            'credentials' => $credentials,
            'host' => $host,
            'stack' => $stack
        );
    }

    /**
     * Instantiated new object
     *
     * @param string $objectName Tile|Term|Tag|Filter
     * @param string|array $objectId Id of new object
     * @param bool $fetch Do get request to populate the field / property
     *
     * @return object
     */
    public function instance($objectName, $objectId = null, $fetch = true)
    {
        $class = "\\Stackla\\Api\\" . ucfirst($objectName);

        if (!class_exists($class)) {
            return null;
        }

        return new $class($this->configs, $objectId, $fetch);
    }

}
