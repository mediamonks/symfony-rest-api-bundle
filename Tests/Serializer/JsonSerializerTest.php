<?php

namespace MediaMonks\RestApiBundle\Tests\Serializer;

use MediaMonks\RestApiBundle\Serializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function test_formats()
    {
        $serializer = new JsonSerializer();
        $this->assertInternalType('array', $serializer->getSupportedFormats());
        $this->assertEquals(['json'], $serializer->getSupportedFormats());
        $this->assertInternalType('string', $serializer->getDefaultFormat());
        $this->assertEquals('json', $serializer->getDefaultFormat());
    }

    public function test_supports()
    {
        $serializer = new JsonSerializer();
        $this->assertTrue($serializer->supportsFormat('json'));
        $this->assertFalse($serializer->supportsFormat('xml'));
        $this->assertFalse($serializer->supportsFormat('msgpack'));
    }

    public function test_serialize()
    {
        $serializer = new JsonSerializer();
        $output = $serializer->serialize('foo', 'json');
        $this->assertEquals('"foo"', $output);
    }
}
