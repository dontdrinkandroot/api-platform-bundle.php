<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Group;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;

interface GroupsMapperInterface
{
    public function supports(ApiRequest $apiRequest, bool $normalization): bool;

    /** @return array<string, mixed> */
    public function map(ApiRequest $apiRequest, bool $normalization): array;
}
