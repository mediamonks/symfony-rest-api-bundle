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
        $paginatedResponse = new OffsetPaginatedResponse('foo', 1, 2, 3, 4);
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
        //$this->assertEquals(Response::HTTP_NO_CONTENT, $responseContainer->getStatusCode());
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
}
