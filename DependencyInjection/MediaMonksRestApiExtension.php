<?php

namespace MediaMonks\RestApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
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

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->getDefinition('mediamonks_rest_api.request_matcher')
            ->replaceArgument(0, $config['request_matcher']['whitelist']);
        $container->getDefinition('mediamonks_rest_api.request_matcher')
            ->replaceArgument(1, $config['request_matcher']['blacklist']);

        $container->getDefinition('mediamonks_rest_api.response_transformer')
            ->replaceArgument(
                1,
                [
                    'debug'               => $this->getDebug($config, $container),
                    'post_message_origin' => $config['post_message_origin'],
                ]
            );

        $this->loadSerializer($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    protected function loadSerializer(ContainerBuilder $container, array $config)
    {
        if (!$container->has($config['serializer'])) {
            $config['serializer'] = 'mediamonks_rest_api.serializer.'.$config['serializer'];
        }

        $container->getDefinition('mediamonks_rest_api.request_transformer')
            ->replaceArgument(0, new Reference($config['serializer']))
        ;
        $container->getDefinition('mediamonks_rest_api.response_transformer')
            ->replaceArgument(0, new Reference($config['serializer']))
        ;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'mediamonks_rest_api';
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return bool
     */
    public function getDebug(array $config, ContainerBuilder $container)
    {
        if (isset($config['debug'])) {
            return $config['debug'];
        }
        if ($container->hasParameter('kernel.debug')) {
            return $container->getParameter('kernel.debug');
        }

        return false;
    }
}
