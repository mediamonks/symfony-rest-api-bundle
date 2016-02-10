<?php

namespace MediaMonks\RestApiBundle\Tests\Response;

use MediaMonks\RestApiBundle\Response\CursorPaginatedResponse;

class CursorPaginatedResponseTest extends \PHPUnit_Framework_TestCase
{
    const DATA = 'data';
    const BEFORE = 1;
    const AFTER = 2;
    const LIMIT = 3;
    const TOTAL = 4;

    /**
     * @return CursorPaginatedResponse
     */
    public function createCursorPaginatedResponse()
    {
        return new CursorPaginatedResponse(self::DATA, self::BEFORE, self::AFTER, self::LIMIT, self::TOTAL);
    }

    public function testCursorPaginatedResponse()
    {
        $response = $this->createCursorPaginatedResponse();
        $this->assertEquals(self::DATA, $response->getData());
        $this->assertEquals(self::BEFORE, $response->getBefore());
        $this->assertEquals(self::AFTER, $response->getAfter());
        $this->assertEquals(self::LIMIT, $response->getLimit());
        $this->assertEquals(self::TOTAL, $response->getTotal());
    }

    public function testCursorPaginatedResponseGettersSetters()
    {
        $response = new CursorPaginatedResponse(null, 0, 0, 0, 0);

        $response->setData(self::DATA);
        $response->setBefore(self::BEFORE);
        $response->setAfter(self::AFTER);
        $response->setLimit(self::LIMIT);
        $response->setTotal(self::TOTAL);

        $this->assertEquals(self::DATA, $response->getData());
        $this->assertEquals(self::BEFORE, $response->getBefore());
        $this->assertEquals(self::AFTER, $response->getAfter());
        $this->assertEquals(self::LIMIT, $response->getLimit());
        $this->assertEquals(self::TOTAL, $response->getTotal());
    }

    public function testCursorPaginatedResponseToArray()
    {
        $response = $this->createCursorPaginatedResponse();
        $data = $response->toArray();

        $this->assertEquals(self::BEFORE, $data['before']);
        $this->assertEquals(self::AFTER, $data['after']);
        $this->assertEquals(self::LIMIT, $data['limit']);
        $this->assertEquals(self::TOTAL, $data['total']);
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
