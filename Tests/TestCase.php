<?php

namespace MediaMonks\RestApiBundle\Tests;


class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function assertNoException($callback)
    {
        try {
            $callback();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Exception thrown when none was expected. Exception message: ' . $e->getMessage());
        }
    }
}
