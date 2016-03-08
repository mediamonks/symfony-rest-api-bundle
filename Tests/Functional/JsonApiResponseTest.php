<?php

namespace MediaMonks\RestApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JsonApiResponseTest extends WebTestCase
{
    public function testEmptyResponse()
    {
        $response = $this->doTest('empty', 'GET', Response::HTTP_NO_CONTENT);
        $this->assertEquals([], $response);
    }

    public function testStringResponse()
    {
        $response = $this->doTest('string', 'GET', Response::HTTP_OK);
        $this->assertInternalType('string', $response['data']);
        $this->assertEquals('foobar', $response['data']);
    }

    public function testIntegerResponse()
    {
        $response = $this->doTest('integer', 'GET', Response::HTTP_OK);
        $this->assertInternalType('integer', $response['data']);
        $this->assertEquals(42, $response['data']);
    }

    public function testArrayResponse()
    {
        $response = $this->doTest('array', 'GET', Response::HTTP_OK);
        $this->assertInternalType('array', $response['data']);
        $this->assertEquals(['foo', 'bar'], $response['data']);
    }

    public function testObjectResponse()
    {
        $response = $this->doTest('object', 'GET', Response::HTTP_OK);
        $this->assertInternalType('array', $response['data']);
    }

    public function testSymfonyResponse()
    {
        $response = $this->doTest('symfony', 'GET', Response::HTTP_CREATED);
        $this->assertInternalType('string', $response['data']);
        $this->assertEquals('foobar', $response['data']);
    }

    /**
     * @param string $path
     * @param string $method
     * @param int $httpCode
     * @return string
     */
    protected function doTest($path, $method, $httpCode)
    {
        $client = static::createClient();
        $client->request($method, sprintf('/api/%s', $path));
        $this->assertEquals($httpCode, $client->getResponse()->getStatusCode());
        return json_decode($client->getResponse()->getContent(), true);
    }

}
