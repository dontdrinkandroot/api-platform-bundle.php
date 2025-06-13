<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Dontdrinkandroot\ApiPlatformBundle\Model\DependencyInjection\ParamName;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\AutomaticSerializationGroupsResourceMetadataFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(AutomaticSerializationGroupsResourceMetadataFactory::class)
        ->decorate('api_platform.metadata.resource.metadata_collection_factory')
        ->arg('$inner', service(AutomaticSerializationGroupsResourceMetadataFactory::class . '.inner'))
        ->arg('$autoGroupPrefixes', param(ParamName::DDR_API_PLATFORM_AUTO_GROUP_PREFIXES));
};
