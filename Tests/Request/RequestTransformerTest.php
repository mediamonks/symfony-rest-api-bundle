<?php

namespace MediaMonks\RestApiBundle\Tests\Request;

use MediaMonks\RestApiBundle\Request\Format;
use MediaMonks\RestApiBundle\Request\RequestTransformer;
use MediaMonks\RestApiBundle\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Mockery as m;

class RequestTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return RequestTransformer
     */
    public function getSubject()
    {
        $serializer = m::mock('MediaMonks\RestApiBundle\Serializer\SerializerInterface');
        $serializer->shouldReceive('getSupportedFormats')->andReturn(['json', 'xml']);
        $serializer->shouldReceive('getDefaultFormat')->andReturn('json');

        return new RequestTransformer($serializer);
    }

    public function testTransformChangesRequestParameters()
    {
        $subject = $this->getSubject();
        $content = ['Hello', 'World!'];
        $request = $this->getRequest($content);

        $subject->transform($request);

        $this->assertEquals($content, iterator_to_array($request->request->getIterator()));
    }

    public function testTransformChangesRequestFormatDefault()
    {
        $subject = $this->getSubject();
        $request = $this->getRequest([]);

        $subject->transform($request);

        $this->assertEquals('json', $request->getRequestFormat());
    }

    public function testTransformChangesRequestFormatGiven()
    {
        $subject = $this->getSubject();
        $request = $this->getRequest([]);
        $request->initialize(['_format' => 'xml']);

        $subject->transform($request);

        $this->assertEquals('xml', $request->getRequestFormat());
    }

    public function testTransformChangesRequestFormatUnknown()
    {
        $subject = $this->getSubject();
        $request = $this->getRequest([]);
        $request->initialize(['_format' => 'csv']);

        $subject->transform($request);

        $this->assertEquals(Format::getDefault(), $request->getRequestFormat());
    }

    protected function getRequest($content)
    {
        $request = Request::create('/');
        $request->initialize([], [], [], [], [], [], json_encode($content));
        $request->headers->add(['Content-type' => 'application/json']);
        return $request;
    }
}
