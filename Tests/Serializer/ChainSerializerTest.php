<?php

namespace MediaMonks\RestApiBundle\Tests\Serializer;

use MediaMonks\RestApiBundle\Serializer\ChainSerializer;
use MediaMonks\RestApiBundle\Serializer\JsonSerializer;
use MediaMonks\RestApiBundle\Serializer\MsgpackSerializer;
use Mockery as m;

class ChainSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function test_supported_formats()
    {
        $serializer = new ChainSerializer();

        $jsonSerializer = m::mock('MediaMonks\RestApiBundle\Serializer\JsonSerializer');
        $jsonSerializer->shouldReceive('getSupportedFormats')->andReturn(['json']);
        $jsonSerializer->shouldReceive('getDefaultFormat')->andReturn('json');

        $serializer->addSerializer($jsonSerializer);
        $this->assertEquals(['json'], $serializer->getSupportedFormats());
        $this->assertEquals('json', $serializer->getDefaultFormat());
        $this->assertTrue($serializer->supportsFormat('json'));
        $this->assertFalse($serializer->supportsFormat('xml'));

        $msgpackSerializer = m::mock('MediaMonks\RestApiBundle\Serializer\MsgpackSerializer');
        $msgpackSerializer->shouldReceive('getSupportedFormats')->andReturn(['msgpack']);
        $msgpackSerializer->shouldReceive('getDefaultFormat')->andReturn('msgpack');

        $serializer->addSerializer($msgpackSerializer);
        $this->assertEquals(['json', 'msgpack'], $serializer->getSupportedFormats());
        $this->assertEquals('json', $serializer->getDefaultFormat());
        $this->assertTrue($serializer->supportsFormat('json'));
        $this->assertTrue($serializer->supportsFormat('msgpack'));
    }

    public function test_supported_formats_without_serializer()
    {
        $this->setExpectedException('MediaMonks\RestApiBundle\Exception\SerializerException');

        $serializer = new ChainSerializer();
        $serializer->getSupportedFormats();
    }

    public function test_default_format_without_serializer()
    {
        $this->setExpectedException('MediaMonks\RestApiBundle\Exception\SerializerException');

        $serializer = new ChainSerializer();
        $serializer->getDefaultFormat();
    }

    public function test_serialize_json()
    {
        $serializer = new ChainSerializer();

        $jsonSerializer = m::mock('MediaMonks\RestApiBundle\Serializer\JsonSerializer');
        $jsonSerializer->shouldReceive('getSupportedFormats')->andReturn(['json']);
        $jsonSerializer->shouldReceive('getDefaultFormat')->andReturn('json');
        $jsonSerializer->shouldReceive('serialize')->andReturn('json_output');
        $jsonSerializer->shouldReceive('supportsFormat')->withArgs(['json'])->andReturn(true);
        $jsonSerializer->shouldReceive('supportsFormat')->withArgs(['msgpack'])->andReturn(false);

        $serializer->addSerializer($jsonSerializer);

        $msgpackSerializer = m::mock('MediaMonks\RestApiBundle\Serializer\MsgpackSerializer');
        $msgpackSerializer->shouldReceive('getSupportedFormats')->andReturn(['msgpack']);
        $msgpackSerializer->shouldReceive('getDefaultFormat')->andReturn('msgpack');
        $msgpackSerializer->shouldReceive('serialize')->andReturn('msgpack_output');
        $msgpackSerializer->shouldReceive('supportsFormat')->withArgs(['json'])->andReturn(false);
        $msgpackSerializer->shouldReceive('supportsFormat')->withArgs(['msgpack'])->andReturn(true);

        $serializer->addSerializer($msgpackSerializer);
        $this->assertEquals('json_output', $serializer->serialize('foo', 'json'));
        $this->assertEquals('msgpack_output', $serializer->serialize('foo', 'msgpack'));
    }

    public function test_serialize_without_serializer()
    {
        $this->setExpectedException('MediaMonks\RestApiBundle\Exception\SerializerException');

        $serializer = new ChainSerializer();
        $serializer->serialize('foo', 'json');
    }

    public function test_serialize_without_serializer_with_unsupported_format()
    {
        $this->setExpectedException('MediaMonks\RestApiBundle\Exception\SerializerException');

        $serializer = new ChainSerializer();

        $jsonSerializer = m::mock('MediaMonks\RestApiBundle\Serializer\JsonSerializer');
        $jsonSerializer->shouldReceive('getSupportedFormats')->andReturn(['json']);
        $serializer->addSerializer($jsonSerializer);

        $serializer = new ChainSerializer();
        $serializer->serialize('foo', 'xml');
    }
}
