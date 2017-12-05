<?php

namespace MediaMonks\RestApiBundle\Tests;

use MediaMonks\RestApiBundle\MediaMonksRestApiBundle;
use Symfony\Component\HttpKernel\Kernel;
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

    public function testYamlParser()
    {
        echo 'yaml parser:'.PHP_EOL;
        $yamlParser = new Parser();
        print_r(get_class_methods($yamlParser));

        echo 'version: '.Kernel::VERSION.PHP_EOL;

        echo file_get_contents(__DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Yaml/Parser.php');

        $this->assertTrue(true);
    }
}
