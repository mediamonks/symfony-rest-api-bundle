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

    public function testCursorPaginatedResponseGettersSetters()
    {
        $data     = 'data';
        $offset   = 1;
        $limit    = 2;
        $total    = 3;
        $response = new OffsetPaginatedResponse(null, 0, 0, 0);
        $response->setData($data);
        $response->setOffset($offset);
        $response->setLimit($limit);
        $response->setTotal($total);

        $this->assertEquals($data, $response->getData());
        $this->assertEquals($offset, $response->getOffset());
        $this->assertEquals($limit, $response->getLimit());
        $this->assertEquals($total, $response->getTotal());
    }
}
