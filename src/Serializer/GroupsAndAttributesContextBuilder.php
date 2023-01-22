<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute\AttributesMapperInterface;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\Group\GroupsMapperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class GroupsAndAttributesContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @param SerializerContextBuilderInterface   $decoratedBuilder
     * @param iterable<GroupsMapperInterface>     $groupsMappers
     * @param iterable<AttributesMapperInterface> $attributesMappers
     */
    public function __construct(
        private readonly SerializerContextBuilderInterface $decoratedBuilder,
        private readonly iterable $groupsMappers,
        private readonly iterable $attributesMappers,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decoratedBuilder->createFromRequest($request, $normalization, $extractedAttributes);
        $apiRequest = new ApiRequest($request);

        $groups = $context[AbstractNormalizer::GROUPS] ?? null;
        foreach ($this->groupsMappers as $groupsMapper) {
            if ($groupsMapper->supports($apiRequest, $normalization)) {
                if (null === $groups) {
                    $groups = [];
                }
                $groups = array_merge($groups, $groupsMapper->map($apiRequest, $normalization));
            }
        }
        if (null !== $groups) {
            $context[AbstractNormalizer::GROUPS] = $groups;
        }

        $attributes = $context[AbstractNormalizer::ATTRIBUTES] ?? null;
        foreach ($this->attributesMappers as $attributesMapper) {
            if ($attributesMapper->supports($apiRequest, $normalization)) {
                if (null === $attributes) {
                    $attributes = [];
                }
                $attributes = array_merge_recursive($attributes, $attributesMapper->map($apiRequest, $normalization));
            }
        }
        if (null !== $attributes) {
            $context[AbstractNormalizer::ATTRIBUTES] = $attributes;
        }

        return $context;
    }
}
