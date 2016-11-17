<?php

namespace Stackla\Core;

use Stackla\Api\Tag;
use Stackla\Validation\JsonValidator;
use Stackla\Validation\ModelAccessorValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class StacklaModel implements \IteratorAggregate, \Countable
{
    /**
     * Stackla configs contains credentials, host, and stack
     * @var array
     */
    protected $configs;

    /**
     * Stackla domain name
     * @var string
     */
    protected $_host;

    /**
     * Stackla stack name
     * @var string
     */
    protected $_stack;

    /**
     * Endpoints
     * @var string
     */
    protected $endpoint;

    /**
     * Object properties
     *
     * @var array
     */
    protected $_propMap = array();

    /**
     * Updated object properties
     *
     * @var array
     */
    protected $_propMapUpdated = array();

    /**
     * Indicator if the object is only has a placeholder data or not
     *
     * @var bool
     */
    protected $isPlaceholder = false;

    /**
     * Request object
     *
     * @var \Stackla\Core\Request
     */
    protected $request;

    /**
     * Error logs from request
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Query result as array
     *
     * @var array
     */
    protected $records = array();

    /**
     * Types
     * @var array
     */
    protected $type_keywords = array(null, 'string', 'str', 'integer', 'int', 'float', 'double', 'array', 'mixed', 'boolean', 'bool');

    /**
     * Flag to keep the properties value
     * @var bool
     */
    protected $no_tracking = false;

    /**
     *
     * @var Credentials
     */
    protected $credentials;

    /**
     * Constructor
     *
     * @param array $configs [
     *                                          'credentials' => \Stackla\Core\Credentials,
     *                                          'host' => API_HOST,
     *                                          'stack' => 'your_stack'
     *                                      ]
     * @param string|array $data
     * @param bool $fetch Do get request to populate the field / property
     */
    public function __construct($configs = array(), $data = null, $fetch = true)
    {
        $this->configs = array_merge(array(
            'credentials' => null,
            'host' => '',
            'stack' => ''
        ), $configs);

        $this->_host = $this->configs['host'];
        $this->_stack = $this->configs['stack'];

        if ($this->configs['credentials']) {
            $this->credentials = $this->configs['credentials'];
        }

        switch (gettype($data)) {
            case "NULL":
                break;
            case "string":
            case "integer":
                if (JsonValidator::validate($data) && gettype($data) !== 'integer') {
                    $this->fromJson($data);
                } elseif (!empty($data)) {
                    if ($fetch) {
                        $this->getById($data);
                    } else {
                        $this->fromArray(array('id' => $data));
                        $this->isPlaceholder = true;
                    }
                }
                break;
            case "array":
                $this->fromArray($data);
                break;
            default:
        }
    }

    /**
     * Magic isSet Method
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->_propMap[$key]);
    }

    /**
     * Magic Get Method
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        $camelCase = $this->convertToCamelCase($key);
        if ($this->__isset($key)) {
            return $this->_propMap[$key];
        } elseif (ModelAccessorValidator::validate($this, $camelCase)) {
            $annots = ReflectionUtil::propertyAnnotations($this, $camelCase, 'set');
            if (isset($annots['uses'])) {
                $getter = "get{$camelCase}";
                return call_user_func(array($this, $getter));
            }

        }
        return null;
    }

    /**
     * Magic Set Method
     *
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        $validKey = false;
        $camelCase = $this->convertToCamelCase($key);
        $readonly = false;
        $trace = debug_backtrace();

        $property = null;
        if (ModelAccessorValidator::property($this, '_' . lcfirst($camelCase))) {
            $property = '_' . lcfirst($camelCase);
        }

        if (ModelAccessorValidator::validate($this, $camelCase)) {
            $annots = ReflectionUtil::propertyAnnotations($this, $camelCase, 'set');
            $setter = "set{$camelCase}";
            $ref = new \ReflectionMethod($this, $setter);
            $permitedCaller = isset($trace[1]) && isset($trace[1]['function']) && $trace[1]['function'] === $setter;
            if (!$ref->isPublic() && !$permitedCaller) {
                $readonly = true;
            } else {
                if ($property) {
                    $this->$property = $value;
                }
                if (isset($trace[1]) && $trace[1]['function'] != $setter && isset($annots['uses'])) {
                    call_user_func(array($this, $setter), $value);
                } else {
                    $validKey = true;
                }
            }
        } elseif ($property) {
            $validKey = true;

            $annotations = ReflectionUtil::mapper($this, $property);

            // Allow to assign value if it's comming from assignValue
            $caller = 'assignValue';
            $permitedCaller = isset($trace[1]) && isset($trace[1]['function']) && $trace[1]['function'] === $caller;

            if (ReflectionUtil::propertyAccess($this, $key) === 'read' && !$permitedCaller) {
                $readonly = true;
            } else {
                $clazz = isset($annotations['var']) ? $annotations['var'] : null;
                $clazz = preg_replace("/(\\[\\]|\\(\\))$/", "", $clazz);
                if (!empty($value) && !in_array($clazz, $this->type_keywords)) {
                    switch ($clazz) {
                        case 'StacklaDateTime':
                        case 'Stackla\Core\StacklaDateTime':
                            $value = $this->initDate($value);
                            break;
                        case 'Tag':
                        case 'Stackla\Api\Tag':
                            $value = $this->initTags($value);
                            break;
                        default:
                            $o = new $clazz();
                            $setter = sprintf("set%s", $camelCase);
                            if (method_exists($o, $setter)) {
                                $o->$setter($value);
                                $value = $o;
                            } elseif (method_exists($o, 'formArray')) {
                                $o->fromArray($value);
                                $value = $o;
                            }
                            break;
                    }
                }
                $this->$property = $value;
            }
        }

        // Throw an error if user try to override/assign the value
        if ($readonly) {
            throw new \Exception("Attempted to write readonly property '{$key}' of " . get_class($this) . " class.");
        }

        // assign value to property mapper
        if ($validKey) {
            $old_value = $this->__isset($key) ? $this->__get($key) : null;
            if ($value === null) {
                $this->__unset($key);
            } else {
                if (!$this->no_tracking && $old_value !== $value) {
                    $this->_propMapUpdated[] = $key;
                }
                $this->_propMap[$key] = $value;
            }
        }
    }

    /**
     * Magic Unset Method
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->_propMap[$key]);
    }

    /**
     * Mapping Annotations
     *
     * @param $class
     * @param $property
     *
     * @return array|null
     */
    protected function mapAnnotations($class, $property)
    {
        $ref = new \ReflectionProperty($class, $property);
        if (!preg_match_all('~\@([^\s@\(]+)[\t ]*(?:\(?([^\n@]+)\)?)?~i', $ref->getDocComment(), $matchAnnots, PREG_PATTERN_ORDER)) {
            return null;
        }

        $annotations = array();
        foreach ($matchAnnots[1] as $i => $annot) {
            $annotations[$annot] = empty($matchAnnots[2][$i]) ? true : rtrim($matchAnnots[2][$i], " \t\n\r");
        }

        return $annotations;
    }

    /**
     * Check if property is public or not
     *
     * @param string $key Property name
     *
     * @return bool
     */
    public function isPublic($key)
    {
        $camelCase = $this->convertToCamelCase($key);
        $setter = "set{$camelCase}";

        // if class doesn't have setter method for that property, we threat it as public property
        if (!method_exists($this, $setter)) {
            return true;
        }

        $ref = new \ReflectionMethod($this, $setter);
        if ($ref->isPublic()) {
            return true;
        }

        return false;
    }

    /**
     * Reset properties tracking for update perpose
     *
     * @return $this
     */
    protected function resetTracking()
    {
        $this->_propMapUpdated = array();

        return $this;
    }

    /**
     * Check if property is been updated or not
     *
     * @param string $property
     *
     * @return bool
     */
    protected function isUpdated($property)
    {
        // Check if property is been updated
        // Check if property is public
        // Check if property name start with '_'
        if (in_array($property, $this->_propMapUpdated) && $this->isPublic($property) && !preg_match("/^_/", $property)) {
            return true;
        }

        return false;
    }

    /**
     * Converts Params to Array
     *
     * @param mixed $param
     * @param bool $only_updated
     *
     * @return array
     */
    protected function _convertToArray($param, $only_updated = false)
    {
        $ret = array();
        foreach ($param as $k => $v) {
            // exclude protected properties to be exported to array
            if ($only_updated && !$this->isUpdated($k)) {
                continue;
            }

            if ($v instanceof StacklaModel) {
                $ret[$k] = $v->toArray($only_updated);
            } else if ($v instanceof StacklaDateTime) {
                $ret[$k] = $v->getTimestamp();
            } else {
                $ret[$k] = $v;
            }
        }
        // If the array is empty, which means an empty object,
        // we need to convert array to StdClass object to properly
        // represent JSON String
        if (sizeof($ret) <= 0) {
            $ret = array();//new StacklaModel();
        }
        return $ret;
    }

    /**
     * Returns array representation of object
     *
     * @param bool $only_updated Boolean value to identifier iether to include or exclude updated properties
     *
     * @return array
     */
    public function toArray($only_updated = false)
    {
        return $this->_convertToArray($this->_propMap, $only_updated);
    }

    /**
     * Returns object JSON representation
     *
     * @param int $options http://php.net/manual/en/json.constants.php
     * @param bool $only_updated option to encode updated properties only
     * @return string
     */
    public function toJSON($options = 0, $only_updated = false)
    {
        // Because of PHP Version 5.3, we cannot use JSON_UNESCAPED_SLASHES option
        // Instead we would use the str_replace command for now.
        // TODO: Replace this code with return json_encode($this->toArray(), $options | 64); once we support PHP >= 5.4
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($this->toArray($only_updated), $options | 64);
        }
        return str_replace('\\/', '/', json_encode($this->toArray($only_updated), $options));
    }

    /**
     * Magic Method for toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJSON(128);
    }

    /**
     * Converts the input key into a valid Setter Method Name
     *
     * @param $key
     * @return mixed
     */
    private function convertToCamelCase($key)
    {
        return str_replace(' ', '', ucwords(str_replace(array('_', '-'), ' ', $key)));
    }

    /**
     * Converts the input key into a valid non Setter Method Name
     *
     * @param $key
     * @return mixed
     */
    private function convertToNonCamelCase($key)
    {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $key));
    }

    /**
     * Fills object value from Array list
     *
     * @param array $arr Array of object data
     *
     * @return $this
     * @throws \Exception
     */
    public function fromArray($arr)
    {
        // don't track the changes for fields
        $this->resetTracking();
        $this->no_tracking = true;

        if (!empty($arr)) {
            if (isset($arr['data']) && isset($arr['errors'])) {
                $this->setErrors($arr['errors']);
                $this->setData($arr['data']);
            } else {
                // Iterate over each element in array
                foreach ($arr as $k => $v) {
                    // If the value is an array, it means, it is an object after conversion
                    if (is_array($v) && !empty($v)) {
                        // Determine the class of the object
                        if (($clazz = ReflectionUtil::getPropertyClass(get_class($this), $k)) != null && !in_array($clazz, $this->type_keywords)) {
                            // If the value is an associative array, it means, its an object. Just make recursive call to it.
                            if (ArrayUtil::isAssocArray($v)) {
                                /** @var self $o */
                                $o = new $clazz();
                                $o->fromArray($v);
                                $this->assignValue($k, $o);
                            } else {
                                // Else, value is an array of object/data
                                $arr = array();
                                // Iterate through each element in that array.
                                foreach ($v as $nk => $nv) {
                                    if (is_array($nv)) {
                                        $o = new $clazz();
                                        $o->fromArray($nv);
                                        $arr[$nk] = $o;
                                    } else {
                                        $arr[$nk] = $nv;
                                    }
                                }
                                $this->assignValue($k, $arr);
                            }
                        } else {
                            $this->assignValue($k, $v);
                        }
                    } else {
                        $this->assignValue($k, $v);
                    }
                }
            }
        } elseif ($arr === false) {
            $reqRes = $this->request ? $this->request->getResponse() : null;
            if ($reqRes) {
                $responseBody = $reqRes->getBody(true);
                $msg = array();
                if ($responseBody != '') {
                    $response = json_decode($responseBody, true);
                    if (count($response['errors'])) {
                        foreach ($response['errors'] as $error) {
                            $msg[] = isset($error['message']) ? "[" . $error['code'] . "] " . $error['message'] : json_encode($error);
                        }
                    }
                }
                $this->setErrors($msg);
                throw new \Exception(implode("\r\n", $msg), $reqRes->getStatusCode());
            } else {
                throw new \Exception('Error while adding data to ' . get_class($this) . ' object');
            }

        }
        $this->no_tracking = false;
        $this->isPlaceholder = false;
        return $this;
    }

    private function assignValue($key, $value)
    {
        // If we find the getter setter, use that, otherwise use magic method.
        if (ModelAccessorValidator::validate($this, $this->convertToCamelCase($key))) {
            $setter = "set" . $this->convertToCamelCase($key);
            $this->$setter($value);
        } else {
            $this->__set($key, $value);
        }
    }

    private function setData($data)
    {
        if (!ArrayUtil::isAssocArray($data)) {
            $class = get_class($this);
            foreach ($data as $item) {
                $this->records[] = new $class($this->configs, $item, false);
            }
        } elseif (!empty($data)) {
            $this->records[] = $this->fromArray($data);
        }
    }

    /**
     * Request error
     *
     * @param array $errors
     *
     * @return $this
     */
    private function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Error
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Fills object value from Json string/object
     *
     * @param string /object     $json
     * @return $this
     */
    public function fromJson($json)
    {
        if (is_string($json)) {
            $json = json_decode($json, true);
        }

        return $this->fromArray($json);
    }

    protected function initRequest($force = false)
    {
        if (!$this->request || $force) {
            $this->request = new Request($this->credentials, $this->_host, $this->_stack);
        }
    }

    /**
     * API credentials setter
     *
     * @param \Stackla\Core\Credentials
     *
     * @return $this
     */
    public function setCredentials(Credentials $credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * API Credentials getter
     *
     * @return Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * StacklaDateTime
     *
     * @param integer|string|StacklaDateTime $datetime
     *
     * @return null|StacklaDateTime
     */
    protected function initDate($datetime)
    {
        if (gettype($datetime) === 'string') {
            $d = new StacklaDateTime($datetime);
        } elseif (gettype($datetime) === 'integer' && intval($datetime) > 0) {
            $d = new StacklaDateTime();
            $d->setTimestamp($datetime);
        } else {
            $d = null;
        }
        return $d;
    }

    /**
     * Tags
     *
     * @param string|int|string[]|int[] $tags
     *
     * @return Tag[]
     */
    public function initTags($tags)
    {
        $theTags = array();
        if (gettype($tags) === 'string') {
            $_tags = explode(',', $tags);
            foreach ($_tags as $_tag_id) {
                $tag = new Tag($this->configs, $_tag_id, false);
                $theTags[] = $tag;
            }
        } elseif (gettype($tags) === 'integer' && intval($tags) > 0) {
            $tag = new Tag($this->configs, $tags, false);
            $theTags[] = $tag;
        } elseif (is_array($tags)) {
            foreach ($tags as $key => $tag) {
                if (gettype($tag) !== 'object') {
                    $tags[$key] = new Tag($this->configs, $tag, false);
                }
            }
            $theTags = $tags;
        }

        return $theTags;
    }

    /**
     * This method will create new content in Stackla
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    public function create()
    {
        $endpoint = sprintf("%s", $this->endpoint);

        $this->initRequest();

        $json = $this->request->sendPost($endpoint, $this->toArray());

        $this->fromJson($json);

        return $json === false ? false : $this;
    }

    /** @noinspection PhpDocSignatureInspection */
    /**
     * This method will return content
     *
     * @param integer $limit default value is 25
     * @param integer $page default value is 1
     * @param array $options optional data
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    public function get()
    {
        $arg_list = func_get_args();

        $endpoint = sprintf("%s", $this->endpoint);

        $options = isset($arg_list[2]) ? $arg_list[2] : array();

        $data = array_merge(
            array(
                'limit' => isset($arg_list[0]) ? $arg_list[0] : 25,
                'page' => isset($arg_list[1]) ? $arg_list[1] : 1
            ),
            $options
        );

        $this->initRequest();

        $json = $this->request->sendGet($endpoint, $data);

        $this->fromJson($json);
        return $json === false ? false : $this;
    }

    /**
     * This method will return content by provided valid ID
     *
     * @param integer $id Content id
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    public function getById($id)
    {
        $endpoint = sprintf("%s/%s", $this->endpoint, $id);

        $this->initRequest();

        $json = $this->request->sendGet($endpoint);

        $this->fromJson($json);
        return $json === false ? false : $this;
    }

    /**
     * This method will update content in Stackla
     *
     * @param bool $force Indicator to force update even the object is using placeholder
     *                          instead of data from API
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     * @throws \Exception
     */
    public function update($force = false)
    {
        if ($this->isPlaceholder && !$force) {
            throw new \Exception("This is placeholder object, it doesn't have a uptodate data. If you still want to update this object with provided property(ies), you can pass 'true' value to the first parameter of this method");
        }

        $endpoint = sprintf("%s/%s", $this->endpoint, $this->id);

        $this->initRequest();

        $data = $this->toArray(true);
        unset($data['id']);

        $json = $this->request->sendPut($endpoint, $data);

        $this->fromJson($json);
        return $json === false ? false : $this;
    }

    /**
     * This method will delete content from Stackla
     *
     * @return mixed    Will return FALSE if the API connection is failed
     *                  otherwise will return json object
     */
    protected function delete()
    {
        $endpoint = sprintf("%s/%s", $this->endpoint, $this->id);

        $this->initRequest();

        $json = $this->request->sendDelete($endpoint);

        $this->fromJson($json);
        return $json === false ? false : $this;
    }

    public function getValidations()
    {
        $validations = array();
        /** @var Validation $validator */
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        $class = get_class($this);
        $metadata = $validator->getMetadataFor(new $class());
        $constrainedProperties = $metadata->getConstrainedProperties();

        // loop for all properties' that has constraints / rules
        foreach ($constrainedProperties as $constrainedProperty) {
            $propertyMetadata = $metadata->getPropertyMetadata($constrainedProperty);
            $constraints = $propertyMetadata[0]->constraints;
            $outputConstraintsCollection = array();

            // loop for all constraints / rules
            foreach ($constraints as $constraint) {
                $class = new \ReflectionObject($constraint);
                $constraintName = $class->getShortName();
                switch ($constraintName) {
                    case "NotBlank":
                        $param = "notBlank";
                        break;
                    case "Type":
                        $param = $constraint->type;
                        break;
                    case "Choice":
                        $param = $constraint->choices;
                        break;
                    case "Url":
                        $param = $constraint->protocols;
                        break;
                    default:
                        $param = $constraint;
                        break;
                }

                $outputConstraintsCollection[$constraintName] = $param;
            }
            $sourceProp = preg_replace('/^_/', '', $this->convertToNonCamelCase($constrainedProperty));

            $validations[$sourceProp] = $outputConstraintsCollection;
        }

        return $validations;
    }

    public function validate()
    {
        $validations = array();

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $_validations = $validator->validate($this);

        foreach ($_validations as $_validation) {
            $prop = preg_replace('/^_/', '', $_validation->getPropertyPath());
            $sourceProp = $this->convertToNonCamelCase($prop);

            /**
             * @todo need to properly convert camel case back to normal case (source)
             */
            $prop = $sourceProp;
            $validations[] = array(
                'property' => $prop,
                'message' => $_validation->getMessage()
            );
        }

        return $validations;
    }

    public function getResponseCode()
    {
        return $this->request->status();
    }

    /**
     * Get the total number of item
     *
     * @return int
     */
    public function count()
    {
        return count($this->records);
    }

    /**
     * Returns all Tags
     *
     * @return array
     */
    public function getResults()
    {
        return $this->records;
    }

    /**
     * Returns the items for iteration
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->records);
    }

}
