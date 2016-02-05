<?php

namespace MediaMonks\RestApiBundle\Request;

class Format
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    /**
     * @return string
     */
    public static function getDefault()
    {
        return self::FORMAT_JSON;
    }
}
