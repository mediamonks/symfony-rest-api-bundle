<?php

namespace MediaMonks\RestApiBundle\DependencyInjection;

use MediaMonks\RestApiBundle\Request\Format;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $this->addDebugNode($rootNode);
        $this->addRequestMatcherNode($rootNode);
        $this->addOutputFormatNode($rootNode);
        $this->addPostMessageOriginNode($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addDebugNode(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('debug')
            ->defaultNull()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRequestMatcherNode(ArrayNodeDefinition $node)
    {
        $node->children()
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
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addOutputFormatNode(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('output_formats')
                ->defaultValue([Format::getDefault()])
                ->prototype('scalar')
                ->validate()
                    ->ifNotInArray(Format::getAvailable())
                    ->thenInvalid('Formats can only contain "' . implode('"', Format::getAvailable()) . '", not "%s"')
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addPostMessageOriginNode(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('post_message_origin')
            ->defaultNull()
            ->end();
    }
}
