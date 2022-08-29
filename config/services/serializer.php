<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Dontdrinkandroot\ApiPlatformBundle\Serializer\Group\OperationGroupsMapper;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\GroupsAndAttributesContextBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(GroupsAndAttributesContextBuilder::class, GroupsAndAttributesContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder')
        ->args([
            service(GroupsAndAttributesContextBuilder::class . '.inner'),
            tagged_iterator('ddr_api_platform.groups_mapper'),
            tagged_iterator('ddr_api_platform.attributes_mapper'),
        ]);

    $services->set(OperationGroupsMapper::class, OperationGroupsMapper::class)
        ->args([
            param('ddr_api_platform.serializer.operation_groups_enabled')
        ])
        ->tag('ddr_api_platform.attributes_mapper');
};
