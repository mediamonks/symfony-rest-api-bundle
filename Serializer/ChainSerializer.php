<?php

namespace MediaMonks\RestApiBundle\Serializer;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use MediaMonks\RestApiBundle\Exception\SerializerException;
use MediaMonks\RestApiBundle\Request\Format;

class ChainSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * @var SerializerInterface[]
     */
    private $serializers;

    /**
     * @var array
     */
    private $formats = [];

    /**
     * @param SerializerInterface $serializer
     */
    public function addSerializer(SerializerInterface $serializer)
    {
        $this->serializers[] = $serializer;
        $this->formats = array_merge($this->formats, $serializer->getSupportedFormats());
    }

    /**
     * @param $data
     * @param $format
     * @return mixed
     * @throws SerializerException
     */
    public function serialize($data, $format)
    {
        $this->assertHasSerializer();

        foreach($this->serializers as $serializer) {
            if ($serializer->supportsFormat($format)) {
                return $serializer->serialize($data, $format);
            }
        }

        throw new SerializerException(sprintf('No serializer found to support format "%s"', $format));
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        $this->assertHasSerializer();

        return $this->formats;
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        $this->assertHasSerializer();

        return $this->serializers[0]->getDefaultFormat();
    }

    /**
     * @throws SerializerException
     */
    private function assertHasSerializer()
    {
        if (count($this->serializers) === 0) {
            throw new SerializerException('No serializer was configured');
        }
    }
}
