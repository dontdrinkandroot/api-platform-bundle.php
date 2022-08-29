<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Dontdrinkandroot\ApiPlatformBundle\Security\AccessControlSubscriber;
use Dontdrinkandroot\ApiPlatformBundle\Security\DelegatingCrudApiVoter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(AccessControlSubscriber::class, AccessControlSubscriber::class)
        ->args([
            service('security.authorization_checker')
        ])
        ->tag('kernel.event_subscriber');

    $services->set(DelegatingCrudApiVoter::class, DelegatingCrudApiVoter::class)
        ->args([
            service('security.authorization_checker')
        ])
        ->tag('security.voter');
};
