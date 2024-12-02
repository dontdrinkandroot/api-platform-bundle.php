<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Override;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    #[Override]
    public function getConfigTreeBuilder(): TreeBuilder
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
