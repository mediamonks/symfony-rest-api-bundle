<?php

namespace MediaMonks\RestApiBundle\Serializer;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use MediaMonks\RestApiBundle\Request\Format;

class JMSSerializer implements SerializerInterface
{
    use SerializerTrait;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var SerializationContext
     */
    private $context;

    /**
     * @param Serializer $serializer
     * @param SerializationContext|null $context
     */
    public function __construct(Serializer $serializer, SerializationContext $context = null)
    {
        $this->serializer = $serializer;
        $this->context = $context;
    }

    /**
     * @param $data
     * @param $format
     * @return mixed|string
     */
    public function serialize($data, $format)
    {
        return $this->serializer->serialize($data, $format, $this->context);
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        return [Format::FORMAT_JSON, Format::FORMAT_XML];
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return Format::FORMAT_JSON;
    }
}
