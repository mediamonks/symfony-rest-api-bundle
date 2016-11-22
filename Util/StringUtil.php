<?php

namespace MediaMonks\RestApiBundle\Util;

class StringUtil
{
    /**
     * @param $class
     * @param string $trim
     * @return string
     */
    public static function classToSnakeCase($class, $trim = null)
    {
        $reflect = new \ReflectionClass($class);
        $name = $reflect->getShortName();
        if (!is_null($trim)) {
            $name = str_replace($trim, '', $name);
        }

        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $name)), '_');
    }
}
