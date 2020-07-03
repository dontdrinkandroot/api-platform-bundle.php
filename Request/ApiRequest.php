<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * Helper class that eases the handling with Api Platform request attributes.
 *
 * @author Philip Washington Sorst <philip@sorst.net>
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
    const ATTRIBUTE_ROUTE = '_route';

    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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

    public function getItemOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_ITEM_OPERATION_NAME);
    }

    public function getSubresourceOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME);
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
        return $this->isCollectionPost($resourceClass, $checkDataClass) || $this->isItemPut($resourceClass, $checkDataClass);
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
}
