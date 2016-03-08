<?php

namespace MediaMonks\RestApiBundle\Tests\Response;

use MediaMonks\RestApiBundle\Response\OffsetPaginatedResponse;

class OffsetPaginatedResponseTest extends \PHPUnit_Framework_TestCase
{
    const DATA = 'data';
    const OFFSET = 1;
    const LIMIT = 2;
    const TOTAL = 3;

    /**
     * @return OffsetPaginatedResponse
     */
    public function createOffsetPaginatedResponse()
    {
        return new OffsetPaginatedResponse(self::DATA, self::OFFSET, self::LIMIT, self::TOTAL);
    }

    public function testOffsetPaginatedResponse()
    {
        $response = $this->createOffsetPaginatedResponse();

        $this->assertEquals(self::DATA, $response->getData());
        $this->assertEquals(self::OFFSET, $response->getOffset());
        $this->assertEquals(self::LIMIT, $response->getLimit());
        $this->assertEquals(self::TOTAL, $response->getTotal());
    }

    public function testOffsetPaginatedResponseGettersSetters()
    {
        $response = new OffsetPaginatedResponse(null, 0, 0, 0);

        $response->setData(self::DATA);
        $response->setOffset(self::OFFSET);
        $response->setLimit(self::LIMIT);
        $response->setTotal(self::TOTAL);

        $this->assertEquals(self::DATA, $response->getData());
        $this->assertEquals(self::OFFSET, $response->getOffset());
        $this->assertEquals(self::LIMIT, $response->getLimit());
        $this->assertEquals(self::TOTAL, $response->getTotal());
    }

    public function testOffsetPaginatedResponseToArray()
    {
        $response = $this->createOffsetPaginatedResponse();
        $data     = $response->toArray();

        $this->assertEquals(self::OFFSET, $data['offset']);
        $this->assertEquals(self::LIMIT, $data['limit']);
        $this->assertEquals(self::TOTAL, $data['total']);
    }
}
