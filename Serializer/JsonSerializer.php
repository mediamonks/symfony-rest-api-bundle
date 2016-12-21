<?php

namespace MediaMonks\RestApiBundle\Serializer;

use MediaMonks\RestApiBundle\Request\Format;

class JsonSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * @param $data
     * @param $format
     * @return mixed|string
     */
    public function serialize($data, $format)
    {
        return json_encode($data);
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        return [Format::FORMAT_JSON];
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return Format::FORMAT_JSON;
    }
}
