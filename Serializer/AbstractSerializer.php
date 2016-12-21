<?php

namespace MediaMonks\RestApiBundle\Serializer;

abstract class AbstractSerializer
{
    /**
     * @param $format
     * @return bool
     */
    public function supportsFormat($format)
    {
        return in_array($format, $this->getSupportedFormats());
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        return [];
    }
}
