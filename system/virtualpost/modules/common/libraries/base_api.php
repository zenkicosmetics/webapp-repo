<?php defined('BASEPATH') or exit('No direct script access allowed');

class base_api  extends Core_BaseClass
{
    public static function getArrayParams(array $paramNames, array $paramValues)
    {
        if (count($paramNames) != count($paramValues)) {
            throw new Exception("The length of two arrays does not match!");
        }
        $params = array();
        foreach ($paramNames as $index => $paramName) {
            $params[$paramName] = $paramValues[$index];
        }

        return $params;
    }
}