<?php

namespace MediaMonks\RestApiBundle\Tests\Util;

use MediaMonks\RestApiBundle\Util\StringUtil;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Constraint;

class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testClassToSnakeCase()
    {
        $this->assertEquals('not_found', StringUtil::classToSnakeCase(new NotFoundHttpException, 'HttpException'));
        $this->assertEquals('bad_request', StringUtil::classToSnakeCase(new BadRequestHttpException, 'HttpException'));
        $this->assertEquals('not_blank', StringUtil::classToSnakeCase(new Constraint\NotBlank));
        $this->assertEquals('email', StringUtil::classToSnakeCase(new Constraint\Email));
    }
}
