<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute;

use ApiPlatform\Metadata\Operation;
use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\Asserted;
use Override;

/**
 * @phpstan-type AttributePath string|AttributePath[]
 */
abstract class AbstractAttributesMapper implements AttributesMapperInterface
{
    #[Override]
    public function map(ApiRequest $apiRequest, bool $normalization): array
    {
        $operation = Asserted::notNull($apiRequest->getOperation());
        if ($normalization) {
            return $this->mapNormalization($operation, $apiRequest);
        }

        return $this->mapDenormalization($operation, $apiRequest);
    }

    /**
     * @return AttributePath[]
     */
    abstract protected function mapNormalization(Operation $operation, ApiRequest $apiRequest): array;

    /**
     * @return AttributePath[]
     */
    abstract protected function mapDenormalization(Operation $operation, ApiRequest $apiRequest): array;
}
