<?php

namespace MediaMonks\RestApiBundle\Tests\Response;

use MediaMonks\RestApiBundle\Response\CursorPaginatedResponse;

class CursorPaginatedResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCursorPaginatedResponse()
    {
        $data     = 'data';
        $before   = 1;
        $after    = 2;
        $limit    = 3;
        $total    = 4;
        $response = new CursorPaginatedResponse($data, $before, $after, $limit, $total);

        $this->assertEquals($data, $response->getData());
        $this->assertEquals($before, $response->getBefore());
        $this->assertEquals($after, $response->getAfter());
        $this->assertEquals($limit, $response->getLimit());
        $this->assertEquals($total, $response->getTotal());
    }
}
