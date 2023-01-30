<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute;

use ApiPlatform\Metadata\Operation;
use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\Asserted;

abstract class AbstractAttributesMapper implements AttributesMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function map(ApiRequest $apiRequest, bool $normalization): array
    {
        $operation = Asserted::notNull($apiRequest->getOperation());
        if ($normalization) {
            return $this->mapNormalization($operation, $apiRequest);
        }

        return $this->mapDenormalization($operation, $apiRequest);
    }

    abstract protected function mapNormalization(Operation $operation, ApiRequest $apiRequest): array;

    abstract protected function mapDenormalization(Operation $operation, ApiRequest $apiRequest): array;
}
