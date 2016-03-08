<?php

namespace MediaMonks\RestApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiControllerTest extends WebTestCase
{
    public function testEmptyResponse()
    {
        $response = $this->requestGet('empty', Response::HTTP_NO_CONTENT);
        $this->assertEquals([], $response);
    }

    public function testStringResponse()
    {
        $response = $this->requestGet('string');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('string', $response['data']);
        $this->assertEquals('foobar', $response['data']);
    }

    public function testIntegerResponse()
    {
        $response = $this->requestGet('integer');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('integer', $response['data']);
        $this->assertEquals(42, $response['data']);
    }

    public function testArrayResponse()
    {
        $response = $this->requestGet('array');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('array', $response['data']);
        $this->assertEquals(['foo', 'bar'], $response['data']);
    }

    public function testObjectResponse()
    {
        $response = $this->requestGet('object');
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('array', $response['data']);
    }

    public function testSymfonyResponse()
    {
        $response = $this->requestGet('symfony', Response::HTTP_CREATED);
        $this->assertArrayHasKey('data', $response);
        $this->assertInternalType('string', $response['data']);
        $this->assertEquals('foobar', $response['data']);
    }

    public function testOffsetPaginated()
    {
        $response = $this->requestGet('paginated/offset');
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

    public function testCursorPaginated()
    {
        $response = $this->requestGet('paginated/cursor');
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

    public function testSymfonyRedirect()
    {
        $response = $this->requestGet('redirect', Response::HTTP_SEE_OTHER);
        $this->assertArrayHasKey('location', $response);
        $this->assertEquals('http://www.mediamonks.com', $response['location']);
    }

    public function testException()
    {
        $response = $this->requestGet('exception', Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertErrorResponse($response);
    }

    public function testExceptionInvalidHttpStatusCode()
    {
        $response = $this->requestGet('exception-invalid-http-status-code', Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertErrorResponse($response);
    }

    public function testExceptionValidCode()
    {
        $response = $this->requestGet('exception-valid-http-status-code', Response::HTTP_BAD_REQUEST);
        $this->assertErrorResponse($response);
    }

    public function testSymfonyNotFoundException()
    {
        $response = $this->requestGet('exception-not-found', Response::HTTP_NOT_FOUND);
        $this->assertErrorResponse($response);
    }

    public function testEmptyFormValidationException()
    {
        $response = $this->requestGet('exception-empty-form', Response::HTTP_BAD_REQUEST);
        $this->assertErrorResponse($response, true);
    }

    public function testFormValidationException()
    {
        $response = $this->requestPost('exception-form', ['email' => 'foo'], Response::HTTP_BAD_REQUEST);
        $this->assertErrorResponse($response, true, [
            [
                'field' => 'name',
                'code' => 'error.form.validation.not_blank',
                'message' => 'This value should not be blank.'
            ],
            [
                'field' => 'email',
                'code' => 'error.form.validation.email',
                'message' => 'This value is not a valid email address.'
            ],
            [
                'field' => 'email',
                'code' => 'error.form.validation.length',
                'message' => 'This value is too short. It should have 5 characters or more.'
            ]
        ]);
    }

    public function testValidationException()
    {
        $response = $this->requestGet('exception-validation', Response::HTTP_BAD_REQUEST);
        $this->assertErrorResponse($response, true);
    }

    public function testMethodNotAllowedException()
    {
        $response = $this->requestGet('post-restricted', Response::HTTP_METHOD_NOT_ALLOWED);
        $this->assertErrorResponse($response);
        $this->assertEquals('error.http.method_not_allowed', $response['error']['code']);
        $this->assertEquals('No route found for "GET /api/post-restricted": Method Not Allowed (Allow: POST)', $response['error']['message']);
    }

    public function testNotFoundHttpException()
    {
        $response = $this->requestGet('non-existing-path', Response::HTTP_NOT_FOUND);
        $this->assertErrorResponse($response);
        $this->assertEquals('error.http.not_found', $response['error']['code']);
        $this->assertEquals('No route found for "GET /api/non-existing-path"', $response['error']['message']);
    }

    /**
     * @param $response
     * @param bool $fields
     * @param array $fieldData
     */
    protected function assertErrorResponse($response, $fields = false, $fieldData = [])
    {
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertInternalType('string', $response['error']['code']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertInternalType('string', $response['error']['message']);

        if($fields) {
            $this->assertArrayHasKey('fields', $response['error']);
            $this->assertInternalType('array', $response['error']['fields']);

            $i = 0;
            foreach($response['error']['fields'] as $field) {
                $this->assertArrayHasKey('field', $field);
                $this->assertInternalType('string', $field['field']);
                if(!empty($fieldData[$i]['field'])) {
                    $this->assertEquals($fieldData[$i]['field'], $field['field']);
                }
                $this->assertArrayHasKey('code', $field);
                $this->assertInternalType('string', $field['code']);
                if(!empty($fieldData[$i]['code'])) {
                    $this->assertEquals($fieldData[$i]['code'], $field['code']);
                }
                $this->assertArrayHasKey('message', $field);
                $this->assertInternalType('string', $field['message']);
                if(!empty($fieldData[$i]['message'])) {
                    $this->assertEquals($fieldData[$i]['message'], $field['message']);
                }

                $i++;
            }
        }
    }

    /**
     * @param $path
     * @param $httpCode
     * @return mixed
     */
    protected function requestGet($path, $httpCode = Response::HTTP_OK)
    {
        return $this->request('GET', $path, [], $httpCode);
    }

    protected function requestPost($path, $data = [], $httpCode = Response::HTTP_CREATED)
    {
        return $this->request('POST', $path, $data, $httpCode);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param int $httpCode
     * @return mixed
     */
    protected function request($method, $path, array $data = [], $httpCode = Response::HTTP_OK)
    {
        $client = static::createClient();
        $client->request($method, sprintf('/api/%s', $path), $data);
        $this->assertEquals($httpCode, $client->getResponse()->getStatusCode());
        return json_decode($client->getResponse()->getContent(), true);
    }
}
