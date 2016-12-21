<?php

namespace
{
    if (!function_exists('msgpack_pack')) {
        function msgpack_pack($value)
        {
            return 'bar';
        }
    }
}

namespace MediaMonks\RestApiBundle\Tests\Serializer
{

    use MediaMonks\RestApiBundle\Serializer\MsgpackSerializer;

    class MsgpackSerializerTest extends \PHPUnit_Framework_TestCase
    {
        public function test_formats()
        {
            $serializer = new MsgpackSerializer();
            $this->assertInternalType('array', $serializer->getSupportedFormats());
            $this->assertEquals(['msgpack'], $serializer->getSupportedFormats());
            $this->assertInternalType('string', $serializer->getDefaultFormat());
            $this->assertEquals('msgpack', $serializer->getDefaultFormat());
        }

        public function test_supports()
        {
            $serializer = new MsgpackSerializer();
            $this->assertFalse($serializer->supportsFormat('json'));
            $this->assertFalse($serializer->supportsFormat('xml'));
            $this->assertTrue($serializer->supportsFormat('msgpack'));
        }

        public function test_serialize()
        {
            $serializer = new MsgpackSerializer();
            $output = $serializer->serialize('foo', 'msgpack');
            $this->assertEquals('bar', $output);
        }
    }
}