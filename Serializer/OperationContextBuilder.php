<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class OperationContextBuilder implements SerializerContextBuilderInterface
{
    private const SEPARATOR = ':';

    const METHOD_LIST = 'list';
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';

    const READ_METHODS = [self::METHOD_LIST, self::METHOD_GET];
    const WRITE_METHODS = [self::METHOD_POST, self::METHOD_PUT];

    const OPERATION_TYPE_COLLECTION = 'collection';
    const OPERATION_TYPE_ITEM = 'item';
    const OPERATION_TYPE_SUBRESOURCE = 'subresource';

    const ATTRIBUTE_OPERATION_TYPE = 'operation_type';
    const ATTRIBUTE_COLLECTION_OPERATION_NAME = 'collection_operation_name';
    const ATTRIBUTE_RESOURCE_CLASS = 'resource_class';
    const ATTRIBUTE_ITEM_OPERATION_NAME = 'item_operation_name';
    const ATTRIBUTE_SUBRESOURCE_RESOURCES = 'subresource_resources';

    /**
     * @var SerializerContextBuilderInterface
     */
    private $decoratedBuilder;

    public function __construct(SerializerContextBuilderInterface $decoratedBuilder)
    {
        $this->decoratedBuilder = $decoratedBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decoratedBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        $method = null;
        $resourceClass = null;
        $subresourceProperty = null;
        $customOperationName = null;

        switch ($context[self::ATTRIBUTE_OPERATION_TYPE]) {

            case self::OPERATION_TYPE_COLLECTION:

                switch ($context[self::ATTRIBUTE_COLLECTION_OPERATION_NAME]) {

                    case self::METHOD_GET:
                        $method = self::METHOD_LIST;
                        break;

                    case self::METHOD_POST:
                        $method = $normalization
                            ? self::METHOD_GET
                            : self::METHOD_POST;
                        break;

                    default:
                        $customOperationName = $context[self::ATTRIBUTE_COLLECTION_OPERATION_NAME];
                        $method = $normalization
                            ? self::METHOD_GET
                            : strtolower($request->getMethod());
                }
                $resourceClass = $this->getShortName($context[self::ATTRIBUTE_RESOURCE_CLASS]);
                break;

            case self::OPERATION_TYPE_ITEM:

                switch ($context[self::ATTRIBUTE_ITEM_OPERATION_NAME]) {

                    case self::METHOD_GET:
                        $method = self::METHOD_GET;
                        break;
                    case self::METHOD_PUT:

                        $method = $normalization
                            ? self::METHOD_GET
                            : self::METHOD_PUT;
                        break;

                    default:
                        $customOperationName = $context[self::ATTRIBUTE_ITEM_OPERATION_NAME];
                        $method = $normalization
                            ? self::METHOD_GET
                            : strtolower($request->getMethod());
                }
                $resourceClass = $this->getShortName($context[self::ATTRIBUTE_RESOURCE_CLASS]);
                break;

            case self::OPERATION_TYPE_SUBRESOURCE:

                $method = $this->isSubresourceCollection($request)
                    ? self::METHOD_LIST
                    : self::METHOD_GET;
                $resourceClass = $this->getShortName(key($context[self::ATTRIBUTE_SUBRESOURCE_RESOURCES]));
                $subresourceProperty = $this->getSubresourceProperty($request);
                break;
        }

        $context = $this->addGroups($context, $method, $resourceClass, $subresourceProperty, $customOperationName);

        return $context;
    }

    private static function isReadMethod(string $method): bool
    {
        return in_array($method, self::READ_METHODS, true);
    }

    private function getShortName(string $class): string
    {
        $reflectedClass = new ReflectionClass($class);

        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $reflectedClass->getShortName()));
    }

    private function getSubresourceProperty(Request $request): string
    {
        $subResourceContext = $request->attributes->get('_api_subresource_context');

        return $subResourceContext['property'];
    }

    private function isSubresourceCollection($request)
    {
        $subResourceContext = $request->attributes->get('_api_subresource_context');

        return $subResourceContext['collection'];
    }

    /**
     * @param array       $context
     * @param string|null $method
     * @param string|null $resourceClass
     * @param string|null $subresourceProperty
     * @param string|null $customOperationName
     *
     * @return array
     */
    protected function addGroups(
        array $context,
        ?string $method,
        ?string $resourceClass,
        ?string $subresourceProperty,
        ?string $customOperationName
    ): array {
        $groupSuffix = $resourceClass;

        if (null !== $subresourceProperty) {
            $groupSuffix .= '.' . $subresourceProperty;
        }

        if (null !== $customOperationName) {
            $groupSuffix .= '.' . $customOperationName;
        }

        $context['groups'][] = strtoupper($method) . self::SEPARATOR . $groupSuffix;
        $context['groups'][] = 'ALL' . self::SEPARATOR . $groupSuffix;

        $readMethod = self::isReadMethod($method);
        $context['groups'][] = ($readMethod ? 'READ' : 'WRITE') . self::SEPARATOR . $groupSuffix;
        $context['groups'][] = $readMethod ? 'API_READ' : 'API_WRITE';

        return $context;
    }
}
