<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Dontdrinkandroot\ApiPlatformBundle\Model\DependencyInjection\ServiceId;
use Dontdrinkandroot\ApiPlatformBundle\Security\AccessCheckerProvider;
use Dontdrinkandroot\ApiPlatformBundle\Security\DelegatingCrudApiVoter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

//    $services->set(AccessControlSubscriber::class, AccessControlSubscriber::class)
//        ->args([
//            service('security.authorization_checker')
//        ])
//        ->tag('kernel.event_subscriber');

    $services->set(DelegatingCrudApiVoter::class, DelegatingCrudApiVoter::class)
        ->args([
            service('security.authorization_checker')
        ])
        ->tag('security.voter');

//    <service id="api_platform.state_provider.access_checker" class="ApiPlatform\Symfony\Security\State\AccessCheckerProvider" decorates="api_platform.state_provider.read">
//            <argument type="service" id="api_platform.state_provider.access_checker.inner" />
//            <argument type="service" id="api_platform.security.resource_access_checker" />
//        </service>
//
//        <service id="api_platform.state_provider.access_checker.post_deserialize" class="ApiPlatform\Symfony\Security\State\AccessCheckerProvider" decorates="api_platform.state_provider.deserialize">
//            <argument type="service" id="api_platform.state_provider.access_checker.post_deserialize.inner" />
//            <argument type="service" id="api_platform.security.resource_access_checker" />
//            <argument>post_denormalize</argument>
//        </service>
//
//    <service id="api_platform.state_provider.access_checker.post_validate" class="ApiPlatform\Symfony\Security\State\AccessCheckerProvider" decorates="api_platform.state_provider.validate">
//            <argument type="service" id="api_platform.state_provider.access_checker.post_validate.inner" />
//            <argument type="service" id="api_platform.security.resource_access_checker" />
//            <argument>post_validate</argument>
//        </service>

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
