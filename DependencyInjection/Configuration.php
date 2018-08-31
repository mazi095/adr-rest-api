<?php
namespace Mazi\AdrRestApi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('adr_rest_api');

        $rootNode
            ->children()
                ->arrayNode('subscribers')
                    ->addDefaultsIfNotSet()
                        ->children()
                        ->scalarNode('api_response_subscriber')->defaultTrue()->end()
                    ->end()
                ->end()
                ->scalarNode('logger')->defaultValue('default')->end()
            ->end();

        return $treeBuilder;
    }

}