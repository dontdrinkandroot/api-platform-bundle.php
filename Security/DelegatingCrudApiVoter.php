<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Crud\CrudOperation;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class DelegatingCrudApiVoter extends ApiVoter
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsOperation(ApiRequest $apiRequest, RequestEvent $event): bool
    {
        return
            $apiRequest->isCollectionGet()
            || $apiRequest->isCollectionPost()
            || $apiRequest->isItemGet()
            || $apiRequest->isItemPut()
            || $apiRequest->isItemDelete();
    }

    /**
     * {@inheritdoc}
     */
    protected function isOperationGranted(
        ApiRequest $apiRequest,
        RequestEvent $event,
        TokenInterface $token
    ): bool {
        $resourceClass = $apiRequest->getResourceClass();
        $data = $apiRequest->getData();

        if ($apiRequest->isCollectionGet()) {
            return $this->authorizationChecker->isGranted(CrudOperation::LIST, $resourceClass);
        }

        if ($apiRequest->isCollectionPost()) {
            return $this->authorizationChecker->isGranted(CrudOperation::CREATE, $data ?? $resourceClass);
        }

        if (null !== $data) {

            if ($apiRequest->isItemGet()) {
                return $this->authorizationChecker->isGranted(CrudOperation::READ, $data);
            }

            if ($apiRequest->isItemPut()) {
                return $this->authorizationChecker->isGranted(CrudOperation::UPDATE, $data);
            }

            if ($apiRequest->isItemDelete()) {
                return $this->authorizationChecker->isGranted(CrudOperation::DELETE, $data);
            }
        }

        return false;
    }

    protected function getAuthorizationChecker(): AuthorizationCheckerInterface
    {
        return $this->authorizationChecker;
    }
}
