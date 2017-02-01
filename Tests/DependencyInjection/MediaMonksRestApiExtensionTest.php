<?php

namespace MediaMonks\RestApiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MediaMonks\RestApi\Request\PathRequestMatcher;
use MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension;
use Mockery as m;

class MediaMonksRestApiExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new MediaMonksRestApiExtension(),
        ];
    }

    public function testAfterLoadingTheCorrectParametersAreLoaded()
    {
        $this->load();
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.rest_api_event_subscriber.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.regex_request_matcher.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.path_request_matcher.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.request_transformer.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.response_transformer.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.serializer.jms.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.serializer.json.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.serializer.msgpack.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.response_model.class');
        $this->assertContainerBuilderHasParameter('mediamonks_rest_api.response_model_factory.class');
    }

    public function testAfterLoadingTheCorrectServicesAreLoaded()
    {
        $this->load();
        $this->assertContainerBuilderHasService('mediamonks_rest_api.rest_api_event_subscriber');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.regex_request_matcher');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.path_request_matcher');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.request_transformer');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.response_transformer');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.serializer.json');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.serializer.msgpack');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.serializer.jms');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.response_model');
        $this->assertContainerBuilderHasService('mediamonks_rest_api.response_model_factory');
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

    public function testUsePathFromConfig()
    {
        $this->load(['request_matcher' => ['path' => '/foo']]);

        $this->assertEquals(
            'mediamonks_rest_api.path_request_matcher',
            (string)$this->container->getDefinition('mediamonks_rest_api.rest_api_event_subscriber')->getArgument(0)
        );
    }

    public function testUseWhitelistFromConfig()
    {
        $this->load(['request_matcher' => ['whitelist' => ['~/foo~']]]);

        $this->assertEquals(
            'mediamonks_rest_api.regex_request_matcher',
            (string)$this->container->getDefinition('mediamonks_rest_api.rest_api_event_subscriber')->getArgument(0)
        );
    }

    public function testUseCustomResponseModel()
    {
        $this->load(['response_model' => 'custom_response_model']);

        $this->assertEquals(
            'custom_response_model',
            (string)$this->container->getDefinition('mediamonks_rest_api.response_model_factory')->getArgument(0)
        );
    }
}