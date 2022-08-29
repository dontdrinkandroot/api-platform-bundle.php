<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;

interface AttributesMapperInterface
{
    public function supports(ApiRequest $apiRequest, bool $normalization): bool;

    /** @return array<array-key, mixed> */
    public function map(ApiRequest $apiRequest, bool $normalization): array;
}
