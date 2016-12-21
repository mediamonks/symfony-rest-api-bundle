<?php

namespace MediaMonks\RestApiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension;
use Mockery as m;

class MediaMonksRestApiExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new MediaMonksRestApiExtension()
        ];
    }

    public function testAfterLoadingTheCorrectParametersAreLoaded()
    {
        $this->load();
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.io_event_subscriber.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.request_matcher.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.request_transformer.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.response_transformer.class');
    }

    public function testAfterLoadingTheCorrectServicesAreLoaded()
    {
        $this->load();
        $this->assertContainerBuilderHasService('mediamonks_rest_api.io_event_subscriber');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.request_matcher');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.request_transformer');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.response_transformer');
    }

    public function testGetDebugFromConfig()
    {
        $containerBuilder = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container = new MediaMonksRestApiExtension();
        $this->assertTrue($container->getDebug(['debug' => true], $containerBuilder));
    }

    public function testGetDebugFromKernel()
    {
        $containerBuilder = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder->shouldReceive('hasParameter')->withArgs(['kernel.debug'])->andReturn(true);
        $containerBuilder->shouldReceive('getParameter')->withArgs(['kernel.debug'])->andReturn(true);

        $container = new MediaMonksRestApiExtension();
        $this->assertTrue($container->getDebug([], $containerBuilder));
    }
}