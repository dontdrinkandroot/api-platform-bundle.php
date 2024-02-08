<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Dontdrinkandroot\ApiPlatformBundle\Model\DependencyInjection\ServiceId;
use Dontdrinkandroot\ApiPlatformBundle\Security\AccessCheckerProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(ServiceId::ACCESS_CHECKER_PROVIDER_DEFAULT, AccessCheckerProvider::class)
        ->decorate('api_platform.state_provider.access_checker')
        ->args([
            service('api_platform.state_provider.access_checker.inner'),
            service(AuthorizationCheckerInterface::class)
        ]);

    $services->set(ServiceId::ACCESS_CHECKER_PROVIDER_POST_DESERIALIZE, AccessCheckerProvider::class)
        ->decorate('api_platform.state_provider.access_checker.post_deserialize')
        ->args([
            service('api_platform.state_provider.access_checker.post_deserialize.inner'),
            service(AuthorizationCheckerInterface::class),
            'post_denormalize'
        ]);

    $services->set(ServiceId::ACCESS_CHECKER_PROVIDER_POST_VALIDATE, AccessCheckerProvider::class)
        ->decorate('api_platform.state_provider.access_checker.post_validate')
        ->args([
            service('api_platform.state_provider.access_checker.post_validate.inner'),
            service(AuthorizationCheckerInterface::class),
            'post_validate'
        ]);

};
