<?php

namespace Stackla\Api;

use Stackla\Api\Tile;
use Stackla\Api\Term;
use Stackla\Api\Filter;
use Stackla\Api\Tag;

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
     * @param \Stackla\Core\Credetials  $credentials
     * @param string                    $host
     * @param string                    $stack
     *
     * @return $this
     */
    public function __construct(\Stackla\Core\Credentials $credentials, $host, $stack)
    {
        $this->configs = array(
            'credentials' => $credentials,
            'host' => $host,
            'stack' => $stack
        );

        return $this;
    }

    /**
     * Instantiated new object
     *
     * @param string        $objectName     Tile|Term|Tag|Filter
     * @param string|array  $objectId       Id of new object
     * @param bool          $fetch          Do get request to populate the field / property
     *
     * @return object
     */
    public function instance($objectName, $objectId = null, $fetch = true)
    {
        $class = "\\Stackla\\Api\\".ucfirst($objectName);

        if (!class_exists($class)) {
            return null;
        }

        return new $class($this->configs, $objectId, $fetch);
    }

}
