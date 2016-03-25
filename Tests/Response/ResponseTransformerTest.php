<?php

namespace MediaMonks\RestApiBundle\Tests\Response;


use MediaMonks\RestApiBundle\Model\ResponseModelFactory;
use MediaMonks\RestApiBundle\Request\Format;
use MediaMonks\RestApiBundle\Response\ResponseTransformer;
use MediaMonks\RestApiBundle\Tests\TestCase;
use \Mockery as m;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseTransformerTest extends TestCase
{
    protected function getSubject($options = [])
    {
        $serializer = m::mock('JMS\Serializer\Serializer');
        $serializer->shouldReceive('serialize');

        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('render');

        return new ResponseTransformer($serializer, $twig, $options);
    }

    public function testConstructSetsOptions()
    {
        $origin  = 'postmsgorigin';
        $subject = $this->getSubject(['post_message_origin' => $origin]);

        $this->assertEquals($origin, $subject->getPostMessageOrigin());
    }

    public function testSetOptions()
    {
        $subject = $this->getSubject();
        $origin  = 'postmsgorigin';

        $subject->setOptions(['post_message_origin' => $origin]);

        $this->assertEquals($origin, $subject->getPostMessageOrigin());
    }

    public function testSetOptionsWithoutPostMessageOrigin()
    {
        $subject = $this->getSubject();
        $origin  = 'postmsgorigin';

        $subject->setOptions(['someotherkey' => $origin]);

        $this->assertNull($subject->getPostMessageOrigin());
    }

    public function testSetPostMessageOrigin()
    {
        $subject = $this->getSubject();
        $origin  = 'postmsgorigin';

        $subject->setPostMessageOrigin($origin);

        $this->assertEquals($origin, $subject->getPostMessageOrigin());
    }

    public function testTransformLateFalsePreconditions()
    {
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_XML);;

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('setCallback');

        $subject->transformLate($request, $response);

        try {
            $response->shouldNotHaveReceived('setCallback');
            $response->shouldNotHaveReceived('setContent');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testTransformLateWrapperPostMessage()
    {
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);

        $request->query = m::mock('\Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(true);
        $request->query->shouldReceive('get')->andReturn(ResponseTransformer::WRAPPER_POST_MESSAGE);

        $response = m::mock('MediaMonks\RestApiBundle\Response\JsonResponse');
        $response->shouldReceive('setContent')->andReturnSelf();

        $response->headers = m::mock('\Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        $subject->transformLate($request, $response);

        try {
            $response->shouldHaveReceived('setContent')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testTransformLateWrapperCallback()
    {
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);
        $request->query = m::mock('\Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(true);
        $request->query->shouldReceive('get');

        $response = m::mock('MediaMonks\RestApiBundle\Response\JsonResponse');
        $response->shouldReceive('setCallback');

        $subject->transformLate($request, $response);

        try {
            $response->shouldHaveReceived('setCallback')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testTransformEarlyWResponseModelHappyPath()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(false);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $responseModel = m::mock('MediaMonks\RestApiBundle\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($responseModel);
        $response->shouldReceive('setStatusCode');

        // Perform operation
        $actualResponse = $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($response) {
            $response->shouldHaveReceived('setStatusCode')->with(Response::HTTP_CONFLICT);
        });
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $actualResponse);
    }

    public function testTransformEarlyForceHttpOk()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $responseModel = m::mock('MediaMonks\RestApiBundle\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($responseModel);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        // Perform operation
        $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($response) {
            $response->shouldHaveReceived('setStatusCode')->with(Response::HTTP_OK);
        });
    }

    public function testTransformEarlySerializeXml()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_XML);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $responseModel = m::mock('MediaMonks\RestApiBundle\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($responseModel);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('setContent');

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        // Perform operation
        $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($response) {
            $response->shouldHaveReceived('setStatusCode')->with(Response::HTTP_OK);
            $response->shouldHaveReceived('setContent');
        });
    }

    public function testTransformEarlyWOResponseModel()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $content = 'some content';

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_XML);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($content);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('setContent');

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        $responseModel = m::mock('MediaMonks\RestApiBundle\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        $factory = m::mock('MediaMonks\RestApiBundle\Model\ResponseModelFactory');
        $factory->shouldReceive('createFromContent')->andReturn($responseModel);

        $subject->setResponseModelFactory($factory);

        // Perform operation
        $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($factory, $content) {
            $factory->shouldHaveReceived('createFromContent');
        });
    }

    public function testSetGetResponseModelFactory()
    {
        $subject = $this->getSubject();

        $factory = m::mock('MediaMonks\RestApiBundle\Model\ResponseModelFactory');

        $subject->setResponseModelFactory($factory);

        $this->assertEquals($factory, $subject->getResponseModelFactory());
    }

    public function testGetResponseModelFactoryDefault()
    {
        $subject = $this->getSubject();

        $factory = $subject->getResponseModelFactory();

        $this->assertInstanceOf('MediaMonks\RestApiBundle\Model\ResponseModelFactory', $factory);
    }
}
