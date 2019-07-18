<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use Dontdrinkandroot\ApiPlatformBundle\ApiPlatform\ApiRequestAttributes;
use Dontdrinkandroot\ApiPlatformBundle\Security\ApiVoter;
use Dontdrinkandroot\DoctrineBundle\Controller\CrudAction;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
abstract class CrudApiVoter extends ApiVoter
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsOperation(ApiRequestAttributes $apiAttributes, RequestEvent $event): bool
    {
        return $apiAttributes->handlesResourceClass($this->getResourceClass());
    }

    /**
     * {@inheritdoc}
     */
    protected function isOperationGranted(
        ApiRequestAttributes $apiAttributes,
        RequestEvent $event,
        TokenInterface $token
    ): bool {

        if ($apiAttributes->isCollectionGet()) {
            return $this->isReadGranted();
        }

        if ($apiAttributes->handlesData($this->getResourceClass())) {

            $data = $apiAttributes->getData();

            if ($apiAttributes->isCollectionPost()) {
                return $this->isCreateGranted($data);
            }

            if ($apiAttributes->isItemGet()) {
                return $this->isReadGranted($data);
            }

            if ($apiAttributes->isItemPut()) {
                return $this->isUpdatedGranted($data);
            }

            if ($apiAttributes->isItemDelete()) {
                return $this->isDeleteGranted($data);
            }
        }

        return false;
    }

    protected abstract function getResourceClass(): string;

    protected function isCreateGranted(object $data = null)
    {
        if (null === $data) {
            return $this->authorizationChecker->isGranted(CrudAction::CREATE, $this->getResourceClass());
        }

        return $this->authorizationChecker->isGranted(CrudAction::CREATE, $data);
    }

    protected function isReadGranted(object $data = null)
    {
        if (null === $data) {
            return $this->authorizationChecker->isGranted(CrudAction::READ, $this->getResourceClass());
        }

        return $this->authorizationChecker->isGranted(CrudAction::READ, $data);
    }

    protected function isUpdatedGranted(object $data)
    {
        return $this->authorizationChecker->isGranted(CrudAction::UPDATE, $data);
    }

    protected function isDeleteGranted(object $data)
    {
        return $this->authorizationChecker->isGranted(CrudAction::DELETE, $data);
    }
}
