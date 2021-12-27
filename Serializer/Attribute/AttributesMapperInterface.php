<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;

interface AttributesMapperInterface
{
    public function supports(ApiRequest $apiRequest, bool $normalization): bool;

    /** @return array<string, string|array> */
    public function map(ApiRequest $apiRequest, bool $normalization): array;
}
