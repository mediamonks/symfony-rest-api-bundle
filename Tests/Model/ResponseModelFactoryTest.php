<?php

namespace MediaMonks\RestApiBundle\Tests\Model;

use MediaMonks\RestApiBundle\Exception\ValidationException;
use MediaMonks\RestApiBundle\Model\ResponseModel;
use MediaMonks\RestApiBundle\Model\ResponseModelFactory;
use MediaMonks\RestApiBundle\Response\OffsetPaginatedResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testAutoDetectException()
    {
        $exception         = new \Exception('foo');
        $responseContainer = $this->createResponseModel($exception);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($exception, $responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();
        $this->assertArrayHasKey('error', $responseContainerArray);
        $this->assertEquals($responseContainerArray['error']['code'], 'error');
        $this->assertEquals($responseContainerArray['error']['message'], 'foo');
    }

    public function testAutoDetectHttpException()
    {
        $notFoundHttpException = new NotFoundHttpException('foo');
        $responseContainer     = $this->createResponseModel($notFoundHttpException);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($notFoundHttpException, $responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
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
        $responseContainer = $this->createResponseModel($paginatedResponse);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertInternalType('string', $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
        $this->assertEquals($paginatedResponse, $responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();
        $this->assertArrayHasKey('data', $responseContainerArray);
        $this->assertArrayHasKey('pagination', $responseContainerArray);
    }

    public function testAutoDetectEmptyResponse()
    {
        $responseContainer = $this->createResponseModel(null);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertTrue($responseContainer->isEmpty());
    }

    public function testAutoDetectStringResponse()
    {
        $stringData        = 'foo';
        $responseContainer = $this->createResponseModel($stringData);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertInternalType('string', $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectArrayResponse()
    {
        $arrayData         = ['foo', 'bar'];
        $responseContainer = $this->createResponseModel($arrayData);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertInternalType('array', $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectRedirectResponse()
    {
        $uri               = 'http://www.mediamonks.com';
        $redirect          = new RedirectResponse($uri, Response::HTTP_MOVED_PERMANENTLY);
        $responseContainer = $this->createResponseModel($redirect);

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getException());
        $this->assertEquals($redirect, $responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $data = $responseContainer->toArray();

        $this->assertEquals($uri, $data['location']);
    }

    public function testAutoDetectSymfonyResponse()
    {
        $data              = 'foo';
        $response          = new Response($data);
        $responseContainer = $this->createResponseModel($response);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertEquals($data, $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertEquals($response, $responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectMediaMonksResponse()
    {
        $data              = ['foo'];
        $response          = new \MediaMonks\RestApiBundle\Response\Response($data);
        $responseContainer = $this->createResponseModel($response);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertEquals($data, $responseContainer->getData());
        $this->assertNull($responseContainer->getException());
        $this->assertEquals($response, $responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectValidationExceptionResponse()
    {
        $exception         = new ValidationException([]);
        $responseContainer = $this->createResponseModel($exception);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($exception, $responseContainer->getException());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    /**
     * @param $content
     * @return ResponseModel
     */
    protected function createResponseModel($content)
    {
        return ResponseModelFactory::createFactory()->createFromContent($content);
    }
}
