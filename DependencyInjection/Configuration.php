<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
            ->booleanNode('security')->defaultTrue()->end()
            ->booleanNode('serializer')->defaultTrue()->end()
        ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
