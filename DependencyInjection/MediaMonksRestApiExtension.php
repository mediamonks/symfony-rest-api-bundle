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

        if (!empty($config['request_matcher']['path'])) {
            $this->usePathRequestMatcher($container, $config);
        }
        elseif (!empty($config['request_matcher']['whitelist'])) {
            $this->useRegexRequestMatcher($container, $config);
        }

        $container->getDefinition('mediamonks_rest_api.response_transformer')
            ->replaceArgument(
                2,
                [
                    'debug'               => $this->getDebug($config, $container),
                    'post_message_origin' => $config['post_message_origin'],
                ]
            );

        if (!empty($config['response_model'])) {
            $this->replaceResponseModel($container, $config);
        }

        $this->loadSerializer($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    protected function usePathRequestMatcher(ContainerBuilder $container, array $config)
    {
        $container->getDefinition('mediamonks_rest_api.path_request_matcher')
            ->replaceArgument(0, $config['request_matcher']['path']);

        $container->getDefinition('mediamonks_rest_api.rest_api_event_subscriber')
            ->replaceArgument(0, new Reference('mediamonks_rest_api.path_request_matcher'));
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    protected function useRegexRequestMatcher(ContainerBuilder $container, array $config)
    {
        $container->getDefinition('mediamonks_rest_api.regex_request_matcher')
            ->replaceArgument(0, $config['request_matcher']['whitelist']);
        $container->getDefinition('mediamonks_rest_api.regex_request_matcher')
            ->replaceArgument(1, $config['request_matcher']['blacklist']);
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
            ->replaceArgument(0, new Reference($config['serializer']));
        $container->getDefinition('mediamonks_rest_api.response_transformer')
            ->replaceArgument(0, new Reference($config['serializer']));
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    protected function replaceResponseModel(ContainerBuilder $container, array $config)
    {
        $container->getDefinition('mediamonks_rest_api.response_model_factory')
            ->replaceArgument(0, new Reference($config['response_model']));
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
