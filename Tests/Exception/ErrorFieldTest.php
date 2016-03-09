<?php

namespace MediaMonks\RestApiBundle\Tests\Exception;

use MediaMonks\RestApiBundle\Exception\ErrorField;

class ErrorFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testSetFields()
    {
        $errorField = new ErrorField('field', 'code', 'message');
        $this->assertEquals('field', $errorField->getField());
        $this->assertEquals('code', $errorField->getCode());
        $this->assertEquals('message', $errorField->getMessage());
        $this->assertEquals([
            'field' => 'field',
            'code' => 'code',
            'message' => 'message'
        ], $errorField->toArray());
    }
}
