<?php

namespace MediaMonks\RestApiBundle\Tests\Exception;

use MediaMonks\RestApiBundle\Exception\ErrorField;
use MediaMonks\RestApiBundle\Exception\ValidationException;

class ValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetFields()
    {
        $error = new ErrorField('foo', 'bar', 'foobar');
        $validationException = new ValidationException(
            [
                $error
            ]
        );
        $this->assertCount(1, $validationException->getFields());
        $this->assertEquals($error, $validationException->getFields()[0]);
    }

    public function testFailOnInvalidFields()
    {
        $this->setExpectedException('InvalidArgumentException');
        new ValidationException(['foo']);
    }
}
