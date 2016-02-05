<?php

namespace MediaMonks\RestApiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension;

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
}