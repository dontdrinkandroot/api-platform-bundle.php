<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute\AttributesMapperInterface;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\Group\GroupsMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DdrApiPlatformExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/services'));
        $loader->load('services.yaml');

        if (true === $config['security']) {
            $loader->load('security.yaml');
        }
        if (true === $config['serializer']['enabled']) {
            $loader->load('serializer.yaml');

            $container->setParameter(
                'ddr_api_platform.serializer.operation_groups_enabled',
                $config['serializer']['operation_groups_enabled']
            );

            $container
                ->registerForAutoconfiguration(GroupsMapperInterface::class)
                ->addTag('ddr_api_platform.groups_mapper');

            $container
                ->registerForAutoconfiguration(AttributesMapperInterface::class)
                ->addTag('ddr_api_platform.attributes_mapper');
        }
    }
}
