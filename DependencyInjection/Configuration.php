<?php

namespace MediaMonks\RestApiBundle\DependencyInjection;

use MediaMonks\RestApiBundle\Request\Format;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mediamonks_rest_api');

        $rootNode
            ->children()
                ->scalarNode('post_message_origin')
                    ->defaultNull()
                ->end()
                ->arrayNode('request_matcher')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('whitelist')
                            ->defaultValue(['~^/api/$~', '~^/api~'])
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('blacklist')
                            ->defaultValue(['~^/api/doc~'])
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('output_formats')
                    ->defaultValue([Format::getDefault()])
                    ->prototype('scalar')
                    ->validate()
                        ->ifNotInArray(Format::getAvailable())
                        ->thenInvalid('Formats can only contain "' . implode('"', Format::getAvailable()) . '", not "%s"')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}