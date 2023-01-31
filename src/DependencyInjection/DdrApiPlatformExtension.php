<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute\AttributesMapperInterface;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\Group\GroupsMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DdrApiPlatformExtension extends Extension
{
    public const TAG_GROUPS_MAPPER = 'ddr_api_platform.groups_mapper';
    public const TAG_ATTRIBUTES_MAPPER = 'ddr_api_platform.attributes_mapper';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config/services'));
        $loader->load('services.php');

        if (true === $config['security']) {
            $loader->load('security.php');
        }
        if (true === $config['serializer']) {
            $loader->load('serializer.php');

            $container
                ->registerForAutoconfiguration(GroupsMapperInterface::class)
                ->addTag(self::TAG_GROUPS_MAPPER);

            $container
                ->registerForAutoconfiguration(AttributesMapperInterface::class)
                ->addTag(self::TAG_ATTRIBUTES_MAPPER);
        }
    }
}
