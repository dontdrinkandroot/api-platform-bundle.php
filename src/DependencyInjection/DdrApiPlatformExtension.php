<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Dontdrinkandroot\ApiPlatformBundle\Model\DependencyInjection\ParamName;
use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DdrApiPlatformExtension extends Extension
{
    final public const string TAG_GROUPS_MAPPER = 'ddr_api_platform.groups_mapper';
    final public const string TAG_ATTRIBUTES_MAPPER = 'ddr_api_platform.attributes_mapper';

    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config/services'));
        $loader->load('services.php');

        if (true === $config['security']) {
            $loader->load('security.php');
        }

        if (true === $config['serializer']['enabled']) {
            $loader->load('serializer.php');
            $container->setParameter(ParamName::DDR_API_PLATFORM_AUTO_GROUP_PREFIXES, $config['serializer']['auto_group_prefixes']);
        }
    }
}
