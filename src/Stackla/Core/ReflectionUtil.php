<?php

namespace Stackla\Core;

use Stackla\Exception\StacklaConfigurationException;

/**
 * Class ReflectionUtil
 *
 * @package Stackla\Core
 */
class ReflectionUtil
{
    /**
     * Reflection Class
     *
     * @var \ReflectionClass[]
     */
    private static $classRefl = array();

    /**
     * Class definition
     *
     * @var array
     */
    private static $classDefs = array();

    /**
     * Reflection Methods
     *
     * @var \ReflectionMethod[]
     */
    private static $methodsRefl = array();

    /**
     * Reflection Methods return type
     *
     * @var \ReflectionMethod[]
     */
    private static $methodsType = array();

    /**
     * Reflection Properties
     *
     * @var \ReflectionProperty[]
     */
    private static $propertiesRefl = array();

    /**
     * Properties Type
     *
     * @var string[]
     */
    private static $propertiesType = array();


    /**
     * Gets Property Class of the given property.
     * If the class is null, it returns null.
     * If the property is not found, it returns null.
     *
     * @param $class
     * @param $propertyName
     * @return null|string
     * @throws StacklaConfigurationException
     */
    public static function getPropertyClass($class, $propertyName)
    {
        if ($class == get_class(new StacklaModel())) {
            // Make it generic if StacklaModel is used for generating this
            return get_class(new StacklaModel());
        }

        // If the class doesn't exist, or the method doesn't exist, return null.
        if (!class_exists($class) || !method_exists($class, self::getter($class, $propertyName))) {
            return null;
        }

        if (($annotations = self::propertyAnnotations($class, $propertyName)) && isset($annotations['return'])) {
            $param = $annotations['return'];
        }

        if (isset($param)) {
            $anno = preg_split("/[\\s\\[\\]]+/", $param);
            return $anno[0];
        } else {
            throw new StacklaConfigurationException("Getter function for '$propertyName' in '$class' class should have a proper return type.");
        }
    }

    /**
     * Retrieves Annotations of each property
     *
     * @param $class
     * @param $propertyName
     * @param string $getset
     * @throws \RuntimeException
     * @return mixed
     */
    public static function propertyAnnotations($class, $propertyName, $getset = 'get')
    {
        $class = is_object($class) ? get_class($class) : $class;
        if (!class_exists('ReflectionProperty')) {
            throw new \RuntimeException("Property type of " . $class . "::{$propertyName} cannot be resolved");
        }

        if ($annotations =& self::$methodsType[$class][$propertyName]) {
            return $annotations;
        }

        if (!($refl =& self::$methodsRefl[$class][$propertyName])) {
            if ($getset == 'set') {
                $method = self::setter($class, $propertyName);
            } else {
                $method = self::getter($class, $propertyName);
            }
            $refl = new \ReflectionMethod($class, $method);
            self::$methodsRefl[$class][$propertyName] = $refl;
        }

        $annots = self::parse($refl->getDocComment());

        if (!$annots) return null;

        foreach ($annots[1] as $i => $annot) {
            $annotations[strtolower($annot)] = empty($annots[2][$i]) ? TRUE : rtrim($annots[2][$i], " \t\n\r)");
        }

        return $annotations;
    }

    public static function propertyAccess($class, $property)
    {
        $default = null;
        $class = is_object($class) ? get_class($class) : $class;
        if (!class_exists('ReflectionProperty')) {
            throw new \RuntimeException("Property type of " . $class . "::{$property} cannot be resolved");
        }

        if (!($refl =& self::$classRefl[$class][$property])) {
            $refl = new \ReflectionClass($class);
            self::$classRefl[$class][$property] = $refl;
        }

        if (!($defs =& self::$classDefs[$class])) {
            $annots = self::parse($refl->getDocComment());
            if (!$annots) return null;

            $annotations = array();
            foreach ($annots[3] as $i => $annot) {
                if (empty($annot)) continue;
                $annotations[$annot] = empty($annots[1][$i]) ? $default : preg_replace("~^property((-|)([\w]+|))~", "$3", rtrim($annots[1][$i], " \t\n\r"));
                if ($annotations[$annot] == "") $annotations[$annot] = $default;
            }
            $defs = $annotations;
        }
        if (isset($defs[$property])) {
            return $defs[$property];
        }
        return $default;
    }

    public static function mapper($class, $property)
    {
        $class = is_object($class) ? get_class($class) : $class;
        if (!class_exists('ReflectionProperty')) {
            throw new \RuntimeException("Property type of " . $class . "::{$property} cannot be resolved");
        }

        if (!($refl =& self::$propertiesRefl[$class][$property])) {
            $refl = new \ReflectionProperty($class, $property);
            self::$propertiesRefl[$class][$property] = $refl;
        }

        $annots = self::parse($refl->getDocComment());

        if (!$annots) return null;

        $annotations = array();
        foreach ($annots[1] as $i => $annot) {
            $annotations[$annot] = empty($annots[2][$i]) ? true : rtrim($annots[2][$i], " \t\n\r");
        }

        return $annotations;

    }

    // todo: smarter regexp
    private static function parse($docComment)
    {
        if (!preg_match_all(
            '~\@([^\s@\(]+)[\t ]*(?:\(?([^\n\s@]+)\)?)?[\t ]*(\$(?:\(?([^\$\n\s@]+)\)?)?[\t ]*(?:\(?([^\n@]+)\)?))?~i',
            $docComment,
            $annots,
            PREG_PATTERN_ORDER)
        ) {
            return null;
        }
        return $annots;
    }

    /**
     * preg_replace_callback callback function
     *
     * @param $match
     * @return string
     */
    private static function replace_callback($match)
    {
        return ucwords($match[2]);
    }

    /**
     * Returns the properly formatted getter function name based on class name and property
     * Formats the property name to a standard getter function
     *
     * @param string $class
     * @param string $propertyName
     * @return string getter function name
     */
    public static function setter($class, $propertyName)
    {
        return method_exists($class, "set" . ucfirst($propertyName)) ?
            "set" . ucfirst($propertyName) :
            "set" . preg_replace_callback("/([_\\-\\s]?([a-z0-9]+))/", "self::replace_callback", $propertyName);
    }

    /**
     * Returns the properly formatted getter function name based on class name and property
     * Formats the property name to a standard getter function
     *
     * @param string $class
     * @param string $propertyName
     * @return string getter function name
     */
    public static function getter($class, $propertyName)
    {
        return method_exists($class, "get" . ucfirst($propertyName)) ?
            "get" . ucfirst($propertyName) :
            "get" . preg_replace_callback("/([_\\-\\s]?([a-z0-9]+))/", 'self::replace_callback', $propertyName);
    }
}
