<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ddr_api_platform');
        $rootNode = $treeBuilder->getRootNode();

        // @formatter:off
        $rootNode->children()
            ->arrayNode('services')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('operation_context_builder')->defaultFalse()->end()
                    ->booleanNode('access_control_subscriber')->defaultFalse()->end()
                ->end()
            ->end()
        ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
