<?php

namespace MediaMonks\RestApiBundle\Serializer;

use MediaMonks\RestApiBundle\Request\Format;

class MsgpackSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * @param $data
     * @param $format
     * @return mixed|string
     */
    public function serialize($data, $format)
    {
        return msgpack_pack($data);
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        return [Format::FORMAT_MSGPACK];
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return Format::FORMAT_MSGPACK;
    }
}
