<?php

namespace MediaMonks\RestApiBundle\Serializer;

trait SerializerTrait
{
    /**
     * @param $format
     * @return bool
     */
    public function supportsFormat($format)
    {
        return in_array($format, $this->getSupportedFormats());
    }
}
