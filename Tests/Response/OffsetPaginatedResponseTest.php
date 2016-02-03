<?php

namespace MediaMonks\RestApiBundle\Tests\Response;

use MediaMonks\RestApiBundle\Response\OffsetPaginatedResponse;

class OffsetPaginatedResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCursorPaginatedResponse()
    {
        $data     = 'data';
        $offset   = 1;
        $limit    = 2;
        $total    = 3;
        $response = new OffsetPaginatedResponse($data, $offset, $limit, $total);

        $this->assertEquals($data, $response->getData());
        $this->assertEquals($offset, $response->getOffset());
        $this->assertEquals($limit, $response->getLimit());
        $this->assertEquals($total, $response->getTotal());
    }
}
