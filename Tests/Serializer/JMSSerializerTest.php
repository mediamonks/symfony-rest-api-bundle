<?php

namespace MediaMonks\RestApiBundle\Tests\Serializer;

use MediaMonks\RestApiBundle\Serializer\JMSSerializer;
use Mockery as m;

class JMSSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function test_formats()
    {
        $jmsSerializer = m::mock('JMS\Serializer\Serializer');

        $serializer = new JMSSerializer($jmsSerializer);
        $this->assertInternalType('array', $serializer->getSupportedFormats());
        $this->assertEquals(['json', 'xml'], $serializer->getSupportedFormats());
        $this->assertInternalType('string', $serializer->getDefaultFormat());
        $this->assertEquals('json', $serializer->getDefaultFormat());
    }

    public function test_supports()
    {
        $jmsSerializer = m::mock('JMS\Serializer\Serializer');

        $serializer = new JMSSerializer($jmsSerializer);
        $this->assertTrue($serializer->supportsFormat('json'));
        $this->assertTrue($serializer->supportsFormat('xml'));
        $this->assertFalse($serializer->supportsFormat('msgpack'));
    }

    public function test_serialize()
    {
        $jmsSerializer = m::mock('JMS\Serializer\Serializer');
        $jmsSerializer->shouldReceive('serialize')->once()->withArgs(['foo', 'json', null])->andReturn('"foo"');

        $serializer = new JMSSerializer($jmsSerializer);
        $output = $serializer->serialize('foo', 'json');
        $this->assertEquals('"foo"', $output);
    }

    public function test_serialize_xml()
    {
        $jmsSerializer = m::mock('JMS\Serializer\Serializer');
        $jmsSerializer->shouldReceive('serialize')->once()->withArgs(['foo', 'xml', null])->andReturn('<foo>');

        $serializer = new JMSSerializer($jmsSerializer);
        $output = $serializer->serialize('foo', 'xml');
        $this->assertEquals('<foo>', $output);
    }
}
