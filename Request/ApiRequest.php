<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Request;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Helper class that eases the handling with Api Platform request attributes.
 */
class ApiRequest
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';
    const ATTRIBUTE_ID = 'id';
    const ATTRIBUTE_DATA = 'data';
    const ATTRIBUTE_API_RESOURCE_CLASS = '_api_resource_class';
    const ATTRIBUTE_API_COLLECTION_OPERATION_NAME = '_api_collection_operation_name';
    const ATTRIBUTE_API_ITEM_OPERATION_NAME = '_api_item_operation_name';
    const ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME = '_api_subresource_operation_name';
    const ATTRIBUTE_API_SUBRESOURCE_CONTEXT = '_api_subresource_context';
    const ATTRIBUTE_ROUTE = '_route';

    public function __construct(private Request $request)
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string|null
     * @deprecated We cannot predict the field here.
     */
    public function getId(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_ID);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->request->attributes->get(self::ATTRIBUTE_DATA);
    }

    public function getResourceClass(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_RESOURCE_CLASS);
    }

    public function getCollectionOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_COLLECTION_OPERATION_NAME);
    }

    public function isCollectionOperation(): bool
    {
        return null !== $this->getCollectionOperation();
    }

    public function getItemOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_ITEM_OPERATION_NAME);
    }

    public function isItemOperation(): bool
    {
        return null !== $this->getItemOperation();
    }

    public function getSubresourceOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME);
    }

    public function isSubresoureOperation(): bool
    {
        return null !== $this->getSubresourceOperation();
    }

    /**
     * Checks if the Request handles the given resource class.
     */
    public function handlesResourceClass(string $resourceClass, bool $checkDataClass = false): bool
    {
        if ($resourceClass !== $this->getResourceClass()) {
            return false;
        }

        if ($checkDataClass && null !== $this->getData()) {
            return is_a($this->getData(), $resourceClass);
        }

        return true;
    }

    /**
     * Checks if the Request handles one of the given resource classes
     */
    public function handlesOneOfTheResourceClasses(array $resourceClasses, bool $checkDataClass = false): bool
    {
        foreach ($resourceClasses as $resourceClass) {
            if ($this->handlesResourceClass($resourceClass, $checkDataClass)) {
                return true;
            }
        }

        return false;
    }

    public function getRoute(): string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_ROUTE);
    }

    public function handlesRoute(string $route): bool
    {
        return $this->getRoute() === $route;
    }

    public function handlesOneOfTheRoutes(array $routes): bool
    {
        foreach ($routes as $route) {
            if ($this->handlesRoute($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the Request is a create or update operation for the given resource class.
     */
    public function isCreateOrUpdate(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        return $this->isCollectionPost($resourceClass, $checkDataClass) || $this->isItemPut(
                $resourceClass,
                $checkDataClass
            );
    }

    public function isCollectionGet(string $resourceClass = null): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass)) {
            return false;
        }

        return self::METHOD_GET === $this->getCollectionOperation();
    }

    public function isCollectionPost(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return self::METHOD_POST === $this->getCollectionOperation();
    }

    public function isItemGet(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return self::METHOD_GET === $this->getItemOperation();
    }

    public function isItemPut(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return self::METHOD_PUT === $this->getItemOperation();
    }

    public function isItemDelete(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return self::METHOD_DELETE === $this->getItemOperation();
    }

    public function getAttribute(string $key): mixed
    {
        return $this->request->attributes->get($key);
    }

    /**
     * @template T
     *
     * @return class-string<T>|null
     */
    public function getSubresourceContextClass(): ?string
    {
        if ($this->request->attributes->has(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT)) {
            $subresourceContext = $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT);

            $numIdentifiers = count($subresourceContext['identifiers']);
            if (0 === $numIdentifiers) {
                throw new RuntimeException('No identifier found');
            }
            if ($numIdentifiers > 1) {
                throw new RuntimeException('Too many identifiers');
            }

            return reset($subresourceContext['identifiers'])[0];
        }

        return null;
    }
}
