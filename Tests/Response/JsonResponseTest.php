<?php

namespace MediaMonks\RestApiBundle\Tests\Response;

use MediaMonks\RestApiBundle\Response\JsonResponse;

class JsonResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonResponse()
    {
        $data     = ['foo', 'bar'];
        $response = new JsonResponse($data);
        $this->assertEquals($data, $response->getContent());
    }

    public function testJsonResponseSetter()
    {
        $data     = ['foo', 'bar'];
        $response = new JsonResponse();
        $response->setContent($data);
        $this->assertEquals($data, $response->getContent());
    }
}
