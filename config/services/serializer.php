<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Dontdrinkandroot\ApiPlatformBundle\DependencyInjection\DdrApiPlatformExtension;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\GroupsAndAttributesContextBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(GroupsAndAttributesContextBuilder::class, GroupsAndAttributesContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder')
        ->args([
            service(GroupsAndAttributesContextBuilder::class . '.inner'),
            tagged_iterator(DdrApiPlatformExtension::TAG_GROUPS_MAPPER),
            tagged_iterator(DdrApiPlatformExtension::TAG_ATTRIBUTES_MAPPER),
        ]);
};
