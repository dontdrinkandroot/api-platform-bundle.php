<?php

namespace Dontdrinkandroot\ApiPlatformBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class DdrApiPlatformExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        if (array_key_exists('services', $config)) {
            if (true === $config['services']['operation_context_builder']) {
                $loader->load('operation_context_builder.yaml');
            }
            if (true === $config['services']['access_control_subscriber']) {
                $loader->load('access_control_subscriber.yaml');
            }
        }
    }
}
