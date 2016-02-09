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

    public function testCursorPaginatedResponseGettersSetters()
    {
        $data     = 'data';
        $before   = 1;
        $after    = 2;
        $limit    = 3;
        $total    = 4;
        $response = new CursorPaginatedResponse(null, 0, 0, 0, 0);
        $response->setData($data);
        $response->setBefore($before);
        $response->setAfter($after);
        $response->setLimit($limit);
        $response->setTotal($total);

        $this->assertEquals($data, $response->getData());
        $this->assertEquals($before, $response->getBefore());
        $this->assertEquals($after, $response->getAfter());
        $this->assertEquals($limit, $response->getLimit());
        $this->assertEquals($total, $response->getTotal());
    }

    public function testToArrayNullTotal()
    {
        $subject = new CursorPaginatedResponse(null, 1, 2, 3);

        $expected = ['before' => 1, 'after' => 2, 'limit' => 3];
        $this->assertEquals($expected, $subject->toArray());
    }

    public function testToArrayNotNullTotal()
    {
        $subject = new CursorPaginatedResponse(null, 1, 2, 3, 4);

        $expected = ['before' => 1, 'after' => 2, 'limit' => 3, 'total' => 4];
        $this->assertEquals($expected, $subject->toArray());
    }
}
