<?php

namespace Stackla\Validation;

use Stackla\Core\StacklaModel;

/**
 * Class ModelAccessorValidator
 *
 * @package Stackla\Validation
 */
class ModelAccessorValidator
{
    /**
     * Helper method for validating if the class contains accessor methods (getter and setter) for a given attribute
     *
     * @param StacklaModel $class An object of StacklaModel
     * @param string $attributeName Attribute name
     * @return bool
     */
    public static function validate(StacklaModel $class, $attributeName)
    {
        //Check if $attributeName is string
        if (gettype($attributeName) !== 'string') {
            return false;
        }
        //If the mode is disabled, bypass the validation
        foreach (array('set' . $attributeName, 'get' . $attributeName) as $methodName) {
            if (get_class($class) == get_class(new StacklaModel())) {
                // Silently return false on cases where you are using StacklaModel instance directly
                return false;
            } //Check if both getter and setter exists for given attribute
            elseif (!method_exists($class, $methodName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Helper method for validating if the class contains accessor property (protected property with $_)
     *
     * @param StacklaModel $class An object of StacklaModel
     * @param string $attributeName
     *
     * @return boolean
     */
    public static function property(StacklaModel $class, $attributeName)
    {
        //Check if $attributeName is string
        if (gettype($attributeName) !== 'string') {
            return false;
        }

        $propertyName = $attributeName;
        //If the mode is disabled, bypass the validation
        if (get_class($class) == get_class(new StacklaModel())) {
            // Silently return false on cases where you are using StacklaModel instance directly
            return false;
        } //Check if both getter and setter exists for given attribute
        elseif (!property_exists($class, $propertyName)) {
            //Delegate the error based on the choice
            return false;
        }

        return true;
    }
}
