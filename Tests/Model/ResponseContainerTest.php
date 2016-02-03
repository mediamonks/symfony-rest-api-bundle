<?php

namespace MediaMonks\RestApiBundle\Tests\Model;

use MediaMonks\RestApiBundle\Model\ResponseContainer;
use MediaMonks\RestApiBundle\Response\OffsetPaginatedResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAutoDetectException()
    {
        $exception = new \Exception('foo');
        $responseContainer = ResponseContainer::createAutoDetect($exception);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($exception, $responseContainer->getException());
        $this->assertNull($responseContainer->getLocation());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();
        $this->assertArrayHasKey('error', $responseContainerArray);
        $this->assertEquals($responseContainerArray['error']['code'], 'error');
        $this->assertEquals($responseContainerArray['error']['message'], 'foo');
    }

    public function testAutoDetectHttpException()
    {
        $exception = new NotFoundHttpException('foo');
        $responseContainer = ResponseContainer::createAutoDetect($exception);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($exception, $responseContainer->getException());
        $this->assertNull($responseContainer->getLocation());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();
        $this->assertArrayHasKey('error', $responseContainerArray);
        $this->assertEquals($responseContainerArray['error']['code'], 'error.http.not_found');
        $this->assertEquals($responseContainerArray['error']['message'], 'foo');
    }

    public function testAutoDetectPaginatedResponse()
    {
        $paginatedResponse = new OffsetPaginatedResponse('foo', 1, 2, 3);
        $responseContainer = ResponseContainer::createAutoDetect($paginatedResponse);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertInternalType('string', $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getLocation());
        $this->assertInternalType('array', $responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();
        $this->assertArrayHasKey('data', $responseContainerArray);
        $this->assertArrayHasKey('pagination', $responseContainerArray);
    }

    public function testAutoDetectEmptyResponse()
    {
        $responseContainer = ResponseContainer::createAutoDetect(null);
        $this->assertNull($responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getLocation());
        $this->assertNull($responseContainer->getPagination());
        $this->assertTrue($responseContainer->isEmpty());
    }

    public function testAutoDetectStringResponse()
    {
        $data = 'foo';
        $responseContainer = ResponseContainer::createAutoDetect($data);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertInternalType('string', $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getLocation());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectArrayResponse()
    {
        $data = ['foo', 'bar'];
        $responseContainer = ResponseContainer::createAutoDetect($data);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertInternalType('array', $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getLocation());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testDataGettersSetter()
    {
        $data = ['foo', 'bar'];
        $responseContainer = new ResponseContainer();
        $responseContainer->setData($data);
        $this->assertEquals($data, $responseContainer->getData());
    }

    public function testExeptionGettersSetter()
    {
        $exception = new \Exception;
        $responseContainer = new ResponseContainer();
        $responseContainer->setException($exception);
        $this->assertEquals($exception, $responseContainer->getException());
    }

    public function testLocationGettersSetter()
    {
        $location = 'http://www.mediamonks.com';
        $responseContainer = new ResponseContainer();
        $responseContainer->setLocation($location);
        $this->assertEquals($location, $responseContainer->getLocation());
    }

    public function testPaginationGettersSetter()
    {
        $pagination = ['limit' => 0, 'offset' => 0];
        $responseContainer = new ResponseContainer();
        $responseContainer->setPagination($pagination);
        $this->assertEquals($pagination, $responseContainer->getPagination());
    }

    public function testReturnStatusCodeGetterSetter()
    {
        $statusCode = Response::HTTP_NOT_MODIFIED;
        $responseContainer = new ResponseContainer();
        $responseContainer->setReturnStatusCode($statusCode);
        $this->assertEquals($statusCode, $responseContainer->getReturnStatusCode());
    }

    public function testStatusCodeGetterSetter()
    {
        $statusCode = Response::HTTP_NOT_MODIFIED;
        $responseContainer = new ResponseContainer();
        $responseContainer->setStatusCode($statusCode);
        $this->assertEquals($statusCode, $responseContainer->getStatusCode());
    }
}
