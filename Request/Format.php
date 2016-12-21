<?php

namespace MediaMonks\RestApiBundle\Request;

class Format
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_MSGPACK = 'msgpack';

    /**
     * @return string
     */
    public static function getDefault()
    {
        return self::FORMAT_JSON;
    }

    /**
     * @return array
     */
    public static function getAvailable()
    {
        return [self::FORMAT_JSON, self::FORMAT_XML];
    }
}
