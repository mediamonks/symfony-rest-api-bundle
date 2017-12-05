<?php

namespace MediaMonks\RestApiBundle\Tests;

use MediaMonks\RestApiBundle\MediaMonksRestApiBundle;
use Symfony\Component\Yaml\Parser;

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

    public function testYamlLoader()
    {
        $parser = new Parser();
        var_dump(method_exists($parser, 'parseFile'));
        $this->assertTrue(true);
    }
}
