<?php

namespace MediaMonks\RestApiBundle\Tests;

use MediaMonks\RestApiBundle\MediaMonksRestApiBundle;

class MediaMonksRestApiBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new MediaMonksRestApiBundle();
        $this->assertInstanceOf(
            '\MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension',
            $bundle->getContainerExtension()
        );
    }
}
