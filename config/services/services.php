<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
};
