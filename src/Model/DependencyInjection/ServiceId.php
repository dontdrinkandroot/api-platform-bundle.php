<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Model\DependencyInjection;

class ServiceId
{
    public const string ACCESS_CHECKER_PROVIDER_DEFAULT = 'ddr.api_platform.security.access_checker_provider.default';
    public const string ACCESS_CHECKER_PROVIDER_POST_DESERIALIZE = 'ddr.api_platform.security.access_checker_provider.post_deserialize';
    public const string ACCESS_CHECKER_PROVIDER_POST_VALIDATE = 'ddr.api_platform.security.access_checker_provider.post_validate';
}
