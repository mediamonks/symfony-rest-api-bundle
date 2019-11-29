<?php

namespace MediaMonks\RestApiBundle\Tests;

use MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension;
use MediaMonks\RestApiBundle\MediaMonksRestApiBundle;
use PHPUnit\Framework\TestCase;

class MediaMonksRestApiBundleTest extends TestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new MediaMonksRestApiBundle();
        $this->assertInstanceOf(
            MediaMonksRestApiExtension::class,
            $bundle->getContainerExtension()
        );
    }
}
