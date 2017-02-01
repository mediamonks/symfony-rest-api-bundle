<?php

namespace MediaMonks\RestApiBundle\DependencyInjection;

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
        $this->addSerializer($rootNode);
        $this->addPostMessageOriginNode($rootNode);
        $this->addResponseModel($rootNode);

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
                        ->scalarNode('path')
                        ->end()
                        ->arrayNode('whitelist')
                            ->defaultValue(['~^/api$~', '~^/api/~'])
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
    private function addSerializer(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('serializer')
            ->defaultValue('json')
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

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResponseModel(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('response_model')
            ->defaultNull()
            ->end();
    }
}
