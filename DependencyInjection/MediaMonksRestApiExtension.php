<?php

namespace MediaMonks\RestApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MediaMonksRestApiExtension extends Extension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->getDefinition('mediamonks_rest_api.request_matcher')
            ->replaceArgument(0, $config['path']['whitelist']);
        $container->getDefinition('mediamonks_rest_api.request_matcher')
            ->replaceArgument(1, $config['path']['blacklist']);

        $container->getDefinition('mediamonks_rest_api.request_transformer')
            ->replaceArgument(0, $config['output_formats']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'mediamonks_rest_api';
    }
}
