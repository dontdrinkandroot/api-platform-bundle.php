<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use Dontdrinkandroot\Common\CrudOperation;
use Override;
use ReflectionClass;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class AutomaticSerializationGroupsResourceMetadataFactory implements ResourceMetadataCollectionFactoryInterface
{
    /**
     * @param string[] $autoGroupPrefixes The namespace prefixes for which the auto groups should be applied for.
     */
    public function __construct(
        private ResourceMetadataCollectionFactoryInterface $inner,
        private array $autoGroupPrefixes
    ) {
    }

    #[Override]
    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $collection = $this->inner->create($resourceClass);

        if (!$this->shouldApplyAutoGroups($resourceClass)) {
            return $collection;
        }

        return $this->applyAutoGroupsToCollection($collection, $resourceClass);
    }

    private function shouldApplyAutoGroups(string $resourceClass): bool
    {
        return array_any(
            $this->autoGroupPrefixes,
            static fn(string $prefix): bool => str_starts_with($resourceClass, $prefix)
        );
    }

    private function applyAutoGroupsToCollection(
        ResourceMetadataCollection $collection,
        string $resourceClass
    ): ResourceMetadataCollection {
        foreach ($collection as $index => $apiResource) {
            $collection[$index] = $this->addGroupsToApiResource($apiResource, $resourceClass);
        }

        return $collection;
    }

    private function addGroupsToApiResource(ApiResource $apiResource, string $resourceClass): ApiResource
    {
        $operations = $apiResource->getOperations();
        if ($operations === null) {
            return $apiResource;
        }

        /** @phpstan-ignore argument.type */
        $shortName = $this->getResourceShortName($resourceClass);
        $updatedOperations = $this->processOperations($operations, $shortName);

        return $apiResource->withOperations(new Operations($updatedOperations));
    }

    /**
     * @param class-string $resourceClass
     */
    private function getResourceShortName(string $resourceClass): string
    {
        return strtolower((new ReflectionClass($resourceClass))->getShortName());
    }

    /**
     * @return array<string, Operation>
     */
    private function processOperations(Operations $operations, string $shortName): array
    {
        $processedOperations = [];

        foreach ($operations as $name => $operation) {
            $processedOperations[$name] = $this->processSingleOperation($operation, $shortName);
        }

        return $processedOperations;
    }

    private function processSingleOperation(Operation $operation, string $shortName): Operation
    {
        $crudOperation = $this->resolveCrudOperation($operation);
        if ($crudOperation === null) {
            return $operation;
        }

        return match ($crudOperation) {
            CrudOperation::LIST => $this->applyListGroups($operation, $shortName),
            CrudOperation::CREATE => $this->applyCreateGroups($operation, $shortName),
            CrudOperation::READ => $this->applyReadGroups($operation, $shortName),
            CrudOperation::UPDATE => $this->applyUpdateGroups($operation, $shortName),
            default => $operation,
        };
    }

    private function applyListGroups(Operation $operation, string $shortName): Operation
    {
        if ($this->hasExistingNormalizationGroups($operation)) {
            return $operation;
        }

        return $operation->withNormalizationContext([
            AbstractNormalizer::GROUPS => [$this->createGroupName($shortName, CrudOperation::LIST)]
        ]);
    }

    private function applyCreateGroups(Operation $operation, string $shortName): Operation
    {
        $updatedOperation = $operation;

        if (!$this->hasExistingDenormalizationGroups($operation)) {
            $updatedOperation = $updatedOperation->withDenormalizationContext([
                AbstractNormalizer::GROUPS => [$this->createGroupName($shortName, CrudOperation::CREATE)]
            ]);
        }

        if (!$this->hasExistingNormalizationGroups($operation)) {
            $updatedOperation = $updatedOperation->withNormalizationContext([
                AbstractNormalizer::GROUPS => [$this->createGroupName($shortName, CrudOperation::READ)]
            ]);
        }

        return $updatedOperation;
    }

    private function applyReadGroups(Operation $operation, string $shortName): Operation
    {
        if ($this->hasExistingNormalizationGroups($operation)) {
            return $operation;
        }

        return $operation->withNormalizationContext([
            AbstractNormalizer::GROUPS => [$this->createGroupName($shortName, CrudOperation::READ)]
        ]);
    }

    private function applyUpdateGroups(Operation $operation, string $shortName): Operation
    {
        $updatedOperation = $operation;

        if (!$this->hasExistingDenormalizationGroups($operation)) {
            $updatedOperation = $updatedOperation->withDenormalizationContext([
                AbstractNormalizer::GROUPS => [$this->createGroupName($shortName, CrudOperation::UPDATE)]
            ]);
        }

        if (!$this->hasExistingNormalizationGroups($operation)) {
            $updatedOperation = $updatedOperation->withNormalizationContext([
                AbstractNormalizer::GROUPS => [$this->createGroupName($shortName, CrudOperation::READ)]
            ]);
        }

        return $updatedOperation;
    }

    private function hasExistingNormalizationGroups(Operation $operation): bool
    {
        $context = $operation->getNormalizationContext();
        return isset($context[AbstractNormalizer::GROUPS]) || isset($context[AbstractNormalizer::ATTRIBUTES]);
    }

    private function hasExistingDenormalizationGroups(Operation $operation): bool
    {
        $context = $operation->getDenormalizationContext();
        return isset($context[AbstractNormalizer::GROUPS]) || isset($context[AbstractNormalizer::ATTRIBUTES]);
    }

    private function createGroupName(string $shortName, CrudOperation $operation): string
    {
        return $shortName . ':' . strtolower($operation->value);
    }

    private function resolveCrudOperation(Operation $operation): ?CrudOperation
    {
        return match (true) {
            $operation instanceof GetCollection => CrudOperation::LIST,
            $operation instanceof Post => CrudOperation::CREATE,
            $operation instanceof Get => CrudOperation::READ,
            $operation instanceof Put, $operation instanceof Patch => CrudOperation::UPDATE,
            $operation instanceof Delete => CrudOperation::DELETE,
            default => null
        };
    }
}
