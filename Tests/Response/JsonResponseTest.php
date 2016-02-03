<?php

namespace MediaMonks\RestApiBundle\Tests\Response;

use MediaMonks\RestApiBundle\Response\JsonResponse;

class JsonResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCursorPaginatedResponse()
    {
        $data     = ['foo', 'bar'];
        $response = new JsonResponse($data);
        $this->assertEquals($data, $response->getContent());
    }
}
