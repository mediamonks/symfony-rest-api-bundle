<?php

namespace MediaMonks\RestApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JsonApiResponseTest extends WebTestCase
{
    public function testEmptyResponse()
    {
        $response = $this->doTest('empty', Response::HTTP_NO_CONTENT);
        $this->assertEquals([], $response);
    }

    public function testStringResponse()
    {
        $response = $this->doTest('string');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('string', $response['data']);
        $this->assertEquals('foobar', $response['data']);
    }

    public function testIntegerResponse()
    {
        $response = $this->doTest('integer');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('integer', $response['data']);
        $this->assertEquals(42, $response['data']);
    }

    public function testArrayResponse()
    {
        $response = $this->doTest('array');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('array', $response['data']);
        $this->assertEquals(['foo', 'bar'], $response['data']);
    }

    public function testObjectResponse()
    {
        $response = $this->doTest('object');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('array', $response['data']);
    }

    public function testSymfonyResponse()
    {
        $response = $this->doTest('symfony', Response::HTTP_CREATED);
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('string', $response['data']);
        $this->assertEquals('foobar', $response['data']);
    }

    public function testOffsetPaginatedAction()
    {
        $response = $this->doTest('paginated/offset');
        $this->assertInternalType('string', $response['data']);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals('foobar', $response['data']);
        $this->assertArrayHasKey('pagination', $response);
        $this->assertArrayHasKey('offset', $response['pagination']);
        $this->assertEquals(1, $response['pagination']['offset']);
        $this->assertArrayHasKey('limit', $response['pagination']);
        $this->assertEquals(2, $response['pagination']['limit']);
        $this->assertArrayHasKey('total', $response['pagination']);
        $this->assertEquals(3, $response['pagination']['total']);
    }

    public function testCursorPaginatedAction()
    {
        $response = $this->doTest('paginated/cursor');
        $this->assertInternalType('string', $response['data']);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals('foobar', $response['data']);
        $this->assertArrayHasKey('pagination', $response);
        $this->assertArrayHasKey('before', $response['pagination']);
        $this->assertEquals(1, $response['pagination']['before']);
        $this->assertArrayHasKey('after', $response['pagination']);
        $this->assertEquals(2, $response['pagination']['after']);
        $this->assertArrayHasKey('limit', $response['pagination']);
        $this->assertEquals(3, $response['pagination']['limit']);
        $this->assertArrayHasKey('total', $response['pagination']);
        $this->assertEquals(4, $response['pagination']['total']);
    }

    public function testSymfonyRedirectAction()
    {
        $response = $this->doTest('redirect', Response::HTTP_SEE_OTHER);
        $this->assertArrayHasKey('location', $response);
        $this->assertEquals('http://www.mediamonks.com', $response['location']);
    }

    public function testExceptionAction()
    {
        $response = $this->doTest('exception', Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);
    }

    public function testExceptionInvalidHttpStatusCodeAction()
    {
        $response = $this->doTest('exception-invalid-http-status-code', Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);
    }

    public function testExceptionValidCodeAction()
    {
        $response = $this->doTest('exception-valid-http-status-code', Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);
    }

    public function testSymfonyNotFoundExceptionAction()
    {
        $response = $this->doTest('exception-not-found', Response::HTTP_NOT_FOUND);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);
    }

    public function testFormValidationExceptionAction()
    {
        $response = $this->doTest('exception-form', Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);
        $this->assertArrayHasKey('fields', $response['error']);
        $this->assertInternalType('array', $response['error']['fields']);

        foreach($response['error']['fields'] as $field) {
            $this->assertArrayHasKey('field', $field);
            $this->assertInternalType('string', $field['field']);
            $this->assertArrayHasKey('code', $field);
            $this->assertInternalType('string', $field['field']);
            $this->assertArrayHasKey('message', $field);
            $this->assertInternalType('string', $field['field']);
        }
    }

    public function testValidationExceptionAction()
    {
        $response = $this->doTest('exception-validation', Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);
        $this->assertArrayHasKey('fields', $response['error']);
        $this->assertInternalType('array', $response['error']['fields']);
    }

    /**
     * @param $path
     * @param int $httpCode
     * @param string $method
     * @return mixed
     */
    protected function doTest($path, $httpCode = Response::HTTP_OK, $method = 'GET')
    {
        $client = static::createClient();
        $client->request($method, sprintf('/api/%s', $path));
        $this->assertEquals($httpCode, $client->getResponse()->getStatusCode());
        return json_decode($client->getResponse()->getContent(), true);
    }

}
